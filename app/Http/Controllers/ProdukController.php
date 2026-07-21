<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdukModel;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    protected $ProdukModel;

    public function __construct(){
        $this->middleware('auth');
        $this->ProdukModel = new ProdukModel();
    }

    public function index(){
        $data = [
            'produk'=> $this->ProdukModel->allData(),
        ];
        return view('t_produk', $data);
    }

    public function add(){
        $stok_items = DB::table('t_stok_item')->get();
        $dbKategori = DB::table('t_kategori')->pluck('nama_kategori')->toArray();
        $kategori_list = array_values(array_unique(array_merge(['Makanan', 'Minuman'], $dbKategori)));
        
        return view('t_addproduk', compact('stok_items', 'kategori_list'));
    }

    public function getNextId(Request $request)
    {
        $kategori = $request->query('kategori');
        if (!$kategori) {
            return response()->json(['success' => false, 'message' => 'Kategori required'], 400);
        }
        $prefix = ProdukModel::getCategoryPrefix($kategori);
        $nextId = ProdukModel::getNextIdForCategory($kategori);

        return response()->json([
            'success' => true,
            'prefix' => $prefix,
            'next_id' => $nextId
        ]);
    }

    public function insert(Request $request)
    {
        $expectedPrefix = ProdukModel::getCategoryPrefix($request->kategori ?? '');
        $idRegex = '/^' . $expectedPrefix . '[0-9]{3,5}$/';

        $request->validate([
            // Kolom id_produk bertipe INT — diawali angka khusus sesuai kategori (1 untuk Makanan, 2 untuk Minuman, dst.)
            'id_produk' => ['required', 'regex:' . $idRegex, 'unique:t_produk,id_produk'],
            'nama_produk' => 'required',
            'harga_jual' => 'required|numeric|min:0',
            'kategori' => 'required',
            // Recipe validation
            'id_stok' => 'required|array',
            'id_stok.*' => 'required',
            'jumlah_resep' => 'required|array',
            'jumlah_resep.*' => 'required|numeric|min:0.01',
        ], [
            'id_produk.required' => 'Silakan isi ID Menu.',
            'id_produk.regex' => "ID Menu untuk kategori {$request->kategori} harus diawali dengan angka {$expectedPrefix} (contoh: {$expectedPrefix}001).",
            'id_produk.unique' => 'ID Menu sudah ada.',
            'nama_produk.required' => 'Silakan isi Nama Menu.',
            'harga_jual.required' => 'Silakan isi Harga Jual.',
            'kategori.required' => 'Silakan pilih Kategori.',
            'id_stok.*.required' => 'Silakan pilih bahan baku.',
            'jumlah_resep.*.required' => 'Silakan isi jumlah pemakaian.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Samakan dengan penyimpanan INT di t_produk
                $idProduk = ProdukModel::normalizeId($request->id_produk);

                // 1. Simpan Data Menu Utama dengan Tanggal Pembuatan Otomatis (created_at)
                $data = [
                    'id_produk' => $idProduk,
                    'nama_produk' => $request->nama_produk,
                    'stok' => 0,
                    'harga_beli' => 0,
                    'harga_jual' => $request->harga_jual,
                    'kategori' => $request->kategori,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $this->ProdukModel->addData($data);

                // 2. Simpan Data Resep (id_resep via AUTO_INCREMENT)
                foreach ($request->id_stok as $i => $id_stok) {
                    if ($id_stok === null || $id_stok === '') continue;

                    DB::table('t_menu_resep')->insert([
                        'id_menu' => $idProduk,
                        'id_stok' => $id_stok,
                        'jumlah' => $request->jumlah_resep[$i],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            });

            return redirect()->route('menu')->with('pesan_sukses', 'Menu dan Resep berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('pesan_error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($edit_produk){

        if (!$this->ProdukModel->editData($edit_produk)) {
            abort(404);
        }
        $data = [
            'produk' => $this->ProdukModel->editData($edit_produk),
       ];
        return view('t_editproduk', $data);
    }

    public function update($edit_produk){
        Request()->validate([
            'id_produk' => ['required', 'regex:/^[1-9][0-9]{3,5}$/'],
            'nama_produk' => 'required',    
            'harga_jual' => 'required', 
            'kategori' => 'required', 
        ],[
            'id_produk.required' => 'Silakan isi ID Produk.',
            'id_produk.regex' => 'ID Menu harus 4–6 digit dan tidak boleh diawali 0. Contoh: 1001.',
            'nama_produk.required' => 'Silakan isi Nama Produk.',
            'harga_jual.required' => 'Silakan isi Harga Jual.',
            'kategori.required' => 'Silakan pilih Kategori.',
        ]);

        $data = [
            'id_produk' => ProdukModel::normalizeId(Request()->id_produk),
            'nama_produk' => Request()->nama_produk,
            'harga_jual' => Request()->harga_jual,
            'kategori' => Request()->kategori,
        ]; 

        $this->ProdukModel->updateData($edit_produk, $data);
        return redirect()->route('menu')->with('pesan_sukses','Data Berhasil di Update');
    }

    public function delete($id_produk)
    {
        try {
            DB::transaction(function () use ($id_produk) {
                $idNorm = ProdukModel::normalizeId($id_produk);

                // 1. Hapus Resep terkait (termasuk id_menu lama ber-leading-zero)
                DB::table('t_menu_resep')
                    ->where('id_menu', $id_produk)
                    ->orWhere('id_menu', $idNorm)
                    ->orWhereRaw('CAST(id_menu AS UNSIGNED) = ?', [(int) $id_produk])
                    ->delete();

                // 2. Hapus Data Produk
                $this->ProdukModel->deleteData($id_produk);
            });

            return redirect()->back()->with('pesan_hapus', 'Menu dan semua resep terkait berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('pesan_error', 'Gagal menghapus menu: ' . $e->getMessage());
        }
    }

}

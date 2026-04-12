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
        return view('t_addproduk', compact('stok_items'));
    }

    public function insert(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|unique:t_produk,id_produk|min:4|max:6',
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
            'id_produk.unique' => 'ID Menu sudah ada.',
            'nama_produk.required' => 'Silakan isi Nama Menu.',
            'harga_jual.required' => 'Silakan isi Harga Jual.',
            'kategori.required' => 'Silakan pilih Kategori.',
            'id_stok.*.required' => 'Silakan pilih bahan baku.',
            'jumlah_resep.*.required' => 'Silakan isi jumlah pemakaian.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Simpan Data Menu Utama
                $data = [
                    'id_produk' => $request->id_produk,
                    'nama_produk' => $request->nama_produk,
                    'stok' => 0,
                    'harga_beli' => 0,
                    'harga_jual' => $request->harga_jual,
                    'kategori' => $request->kategori,
                ];
                $this->ProdukModel->addData($data);

                // 2. Simpan Data Resep
                foreach ($request->id_stok as $i => $id_stok) {
                    if (empty($id_stok)) continue;

                    // Generate ID Resep otomatis: IDMenu + urutan (01, 02, dst)
                    $generated_id_resep = (int)($request->id_produk . str_pad($i + 1, 2, '0', STR_PAD_LEFT));

                    DB::table('t_menu_resep')->insert([
                        'id_resep' => $generated_id_resep,
                        'id_menu' => $request->id_produk,
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
            'id_produk' => 'required|min:4|max:6',
            'nama_produk' => 'required',    
            'harga_jual' => 'required', 
            'kategori' => 'required', 
        ],[
            'id_produk.required' => 'Silakan isi ID Produk.',
            'id_produk.min' => 'ID Produk minimal 4 karakter.',
            'id_produk.max' => 'ID Produk maximal 6 karakter.',
            'nama_produk.required' => 'Silakan isi Nama Produk.',
            'harga_jual.required' => 'Silakan isi Harga Jual.',
            'kategori.required' => 'Silakan pilih Kategori.',
        ]);

        $data = [
            'id_produk' => Request()->id_produk,
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
                // 1. Hapus Resep terkait
                DB::table('t_menu_resep')->where('id_menu', $id_produk)->delete();

                // 2. Hapus Data Produk
                $this->ProdukModel->deleteData($id_produk);
            });

            return redirect()->back()->with('pesan_hapus', 'Menu dan semua resep terkait berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('pesan_error', 'Gagal menghapus menu: ' . $e->getMessage());
        }
    }

}

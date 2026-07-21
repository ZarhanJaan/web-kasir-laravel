<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdukModel;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    protected $ProdukModel;

    public function __construct()
    {
        $this->middleware('auth');
        $this->ProdukModel = new ProdukModel();
    }

    public function index()
    {
        return view('t_produk', ['produk' => $this->ProdukModel->allData()]);
    }

    public function add()
    {
        $stok_items = DB::table('t_stok_item')->get();
        $kategori = DB::table('t_kategori')->orderBy('nama_kategori')->get();
        return view('t_addproduk', compact('stok_items', 'kategori'));
    }

    public function insert(Request $request)
    {
        $request->validate([
            'id_kategori' => 'required|exists:t_kategori,id_kategori',
            'id_menu_suffix' => 'required',
            'nama_produk' => 'required',
            'harga_jual' => 'required|numeric|min:0',
            'id_stok' => 'required|array',
            'id_stok.*' => 'required',
            'jumlah_resep' => 'required|array',
            'jumlah_resep.*' => 'required|numeric|min:0.01',
        ], [
            'id_kategori.required' => 'Silakan pilih kategori terlebih dahulu.',
            'id_menu_suffix.required' => 'Silakan isi nomor ID menu.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $kategori = DB::table('t_kategori')->where('id_kategori', $request->id_kategori)->first();
                $idProduk = (string) $kategori->id_kategori . $request->id_menu_suffix;

                if (DB::table('t_produk')->where('id_produk', $idProduk)->exists()) {
                    throw new \Exception('ID Menu ' . $idProduk . ' sudah ada.');
                }

                $creator = auth()->user();
                $this->ProdukModel->addData([
                    'id_produk' => $idProduk,
                    'nama_produk' => $request->nama_produk,
                    'stok' => 0,
                    'harga_beli' => 0,
                    'harga_jual' => $request->harga_jual,
                    'kategori' => $kategori->nama_kategori,
                    'created_by_id' => $creator->id,
                    'created_by_name' => $creator->name,
                ]);

                foreach ($request->id_stok as $i => $id_stok) {
                    if ($id_stok === null || $id_stok === '') {
                        continue;
                    }

                    DB::table('t_menu_resep')->insert([
                        'id_menu' => $idProduk,
                        'id_stok' => $id_stok,
                        'jumlah' => $request->jumlah_resep[$i],
                        'created_at' => now(),
                        'updated_at' => now(),
                        'created_by_id' => $creator->id,
                        'created_by_name' => $creator->name,
                    ]);
                }
            });

            return redirect()->route('menu')->with('pesan_sukses', 'Menu dan resep berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('pesan_error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($edit_produk)
    {
        if (!$this->ProdukModel->editData($edit_produk)) {
            abort(404);
        }
        return view('t_editproduk', ['produk' => $this->ProdukModel->editData($edit_produk)]);
    }

    public function update($edit_produk)
    {
        Request()->validate([
            'id_produk' => 'required',
            'nama_produk' => 'required',
            'harga_jual' => 'required',
            'kategori' => 'required',
        ]);

        $this->ProdukModel->updateData($edit_produk, [
            'id_produk' => ProdukModel::normalizeId(Request()->id_produk),
            'nama_produk' => Request()->nama_produk,
            'harga_jual' => Request()->harga_jual,
            'kategori' => Request()->kategori,
        ]);
        return redirect()->route('menu')->with('pesan_sukses', 'Data berhasil diubah.');
    }

    public function delete($id_produk)
    {
        try {
            DB::transaction(function () use ($id_produk) {
                $idNorm = ProdukModel::normalizeId($id_produk);
                DB::table('t_menu_resep')->where('id_menu', $id_produk)->orWhere('id_menu', $idNorm)
                    ->orWhereRaw('CAST(id_menu AS UNSIGNED) = ?', [(int) $id_produk])->delete();
                $this->ProdukModel->deleteData($id_produk);
            });
            return redirect()->back()->with('pesan_hapus', 'Menu dan semua resep terkait berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('pesan_error', 'Gagal menghapus menu: ' . $e->getMessage());
        }
    }
}

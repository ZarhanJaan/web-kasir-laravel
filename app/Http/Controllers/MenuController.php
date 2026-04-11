<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuModel;
use App\Models\MenuDetailModel;
use App\Models\ProdukModel;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        $menus = MenuModel::with('details')->get();
        return view('t_menu', compact('menus'));
    }

    public function add()
    {
        $produkList = ProdukModel::all();
        return view('t_addmenu', compact('produkList'));
    }

    public function insert(Request $request)
    {
        $request->validate([
            'id_menu' => 'required|unique:t_menu,id_menu|min:4|max:6',
            'nama_menu' => 'required',
            'harga_menu' => 'required|numeric',
            'id_produk' => 'required|array',
            'jumlah_dipakai' => 'required|array',
        ], [
            'id_menu.required' => 'Silakan isi ID Menu.',
            'id_menu.unique' => 'ID Menu sudah ada.',
            'id_menu.min' => 'ID Menu minimal 4 karakter.',
            'id_menu.max' => 'ID Menu maximal 6 karakter.',
            'nama_menu.required' => 'Silakan isi Nama Menu.',
            'harga_menu.required' => 'Silakan isi Harga Menu.',
            'id_produk.required' => 'Silakan pilih bahan/produk.',
            'jumlah_dipakai.required' => 'Silakan isi jumlah dipakai.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Insert menu master
                MenuModel::create([
                    'id_menu' => $request->id_menu,
                    'nama_menu' => $request->nama_menu,
                    'harga_menu' => $request->harga_menu,
                ]);

                // Insert menu details (recipe)
                foreach ($request->id_produk as $i => $id_produk) {
                    // Only insert if qty > 0
                    if ($request->jumlah_dipakai[$i] > 0) {
                        MenuDetailModel::create([
                            'id_menu' => $request->id_menu,
                            'id_produk' => $id_produk,
                            'jumlah_dipakai' => $request->jumlah_dipakai[$i]
                        ]);
                    }
                }
            });

            return redirect()->route('menu')->with('pesan_sukses', 'Menu Berhasil di Tambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('pesan_error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function edit($id_menu)
    {
        $menu = MenuModel::with('details')->where('id_menu', $id_menu)->firstOrFail();
        $produkList = ProdukModel::all();
        return view('t_editmenu', compact('menu', 'produkList'));
    }

    public function update(Request $request, $id_menu)
    {
        $request->validate([
            'nama_menu' => 'required',
            'harga_menu' => 'required|numeric',
            'id_produk' => 'required|array',
            'jumlah_dipakai' => 'required|array',
        ]);

        try {
            DB::transaction(function () use ($request, $id_menu) {
                $menu = MenuModel::findOrFail($id_menu);
                $menu->update([
                    'nama_menu' => $request->nama_menu,
                    'harga_menu' => $request->harga_menu,
                ]);

                // Hapus detail lama, insert yang baru untuk mempermudah update
                MenuDetailModel::where('id_menu', $id_menu)->delete();

                foreach ($request->id_produk as $i => $id_produk) {
                    if ($request->jumlah_dipakai[$i] > 0) {
                        MenuDetailModel::create([
                            'id_menu' => $id_menu,
                            'id_produk' => $id_produk,
                            'jumlah_dipakai' => $request->jumlah_dipakai[$i]
                        ]);
                    }
                }
            });

            return redirect()->route('menu')->with('pesan_sukses', 'Menu Berhasil di Update');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('pesan_error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function delete($id_menu)
    {
        $menu = MenuModel::find($id_menu);
        if ($menu) {
            // Because cascade delete should handle child naturally, but let's be explicit
            MenuDetailModel::where('id_menu', $id_menu)->delete();
            $menu->delete();
            return redirect()->route('menu')->with('pesan_hapus', 'Menu Berhasil di Delete');
        }
        return redirect()->route('menu')->with('pesan_hapus', 'Menu Tidak Ditemukan');
    }
}

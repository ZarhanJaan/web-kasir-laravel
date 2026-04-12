<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepController extends Controller
{
    public function index()
    {
        $menus = DB::table('t_produk')->get();
        foreach ($menus as $menu) {
            $menu->resep = DB::table('t_menu_resep')
                ->join('t_stok_item', 't_menu_resep.id_stok', '=', 't_stok_item.id_stok')
                ->where('t_menu_resep.id_menu', $menu->id_produk)
                ->select('t_menu_resep.*', 't_stok_item.nama_stok')
                ->get();
        }

        return view('t_resep', compact('menus'));
    }

    public function edit($id_menu)
    {
        $menu = DB::table('t_produk')->where('id_produk', $id_menu)->first();
        if (!$menu) abort(404);

        $resep = DB::table('t_menu_resep')
            ->join('t_stok_item', 't_menu_resep.id_stok', '=', 't_stok_item.id_stok')
            ->where('t_menu_resep.id_menu', $id_menu)
            ->select('t_menu_resep.*', 't_stok_item.nama_stok')
            ->get();

        $stok_items = DB::table('t_stok_item')->get();

        return view('t_resep_edit', compact('menu', 'resep', 'stok_items'));
    }

    public function add_item(Request $request)
    {
        $request->validate([
            'id_resep' => 'required|unique:t_menu_resep,id_resep',
            'id_menu' => 'required',
            'id_stok' => 'required',
            'jumlah' => 'required|numeric|min:1'
        ]);

        // Check if ingredient already in recipe
        $exists = DB::table('t_menu_resep')
            ->where('id_menu', $request->id_menu)
            ->where('id_stok', $request->id_stok)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('pesan_error', 'Bahan ini sudah ada di resep.');
        }

        DB::table('t_menu_resep')->insert([
            'id_resep' => $request->id_resep,
            'id_menu' => $request->id_menu,
            'id_stok' => $request->id_stok,
            'jumlah' => $request->jumlah,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('pesan_sukses', 'Bahan berhasil ditambahkan ke resep.');
    }

    public function delete_item($id_resep)
    {
        DB::table('t_menu_resep')->where('id_resep', $id_resep)->delete();
        return redirect()->back()->with('pesan_sukses', 'Bahan berhasil dihapus dari resep.');
    }
}

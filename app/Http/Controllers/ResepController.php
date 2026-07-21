<?php

namespace App\Http\Controllers;

use App\Models\ProdukModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepController extends Controller
{
    /**
     * Cocokkan id_menu dengan id_produk INT, termasuk data lama ber-leading-zero (0001 vs 1).
     */
    private function resepByMenuQuery($idMenu)
    {
        $idNorm = ProdukModel::normalizeId($idMenu);

        return DB::table('t_menu_resep')
            ->where(function ($q) use ($idMenu, $idNorm) {
                $q->where('id_menu', (string) $idMenu)
                    ->orWhere('id_menu', $idNorm)
                    ->orWhereRaw('CAST(id_menu AS UNSIGNED) = ?', [(int) $idMenu]);
            });
    }

    public function index()
    {
        $menus = DB::table('t_produk')->get();
        foreach ($menus as $menu) {
            $menu->resep = $this->resepByMenuQuery($menu->id_produk)
                ->join('t_stok_item', 't_menu_resep.id_stok', '=', 't_stok_item.id_stok')
                ->select('t_menu_resep.*', 't_stok_item.nama_stok')
                ->get();
        }

        return view('t_resep', compact('menus'));
    }

    public function edit($id_menu)
    {
        $menu = DB::table('t_produk')->where('id_produk', $id_menu)->first();
        if (!$menu) abort(404);

        $resep = $this->resepByMenuQuery($menu->id_produk)
            ->join('t_stok_item', 't_menu_resep.id_stok', '=', 't_stok_item.id_stok')
            ->select('t_menu_resep.*', 't_stok_item.nama_stok')
            ->get();

        $stok_items = DB::table('t_stok_item')->get();

        return view('t_resep_edit', compact('menu', 'resep', 'stok_items'));
    }

    public function add_item(Request $request)
    {
        $request->validate([
            'id_menu' => 'required',
            'id_stok' => 'required',
            'jumlah' => 'required|numeric|min:1'
        ]);

        // Samakan dengan id_produk INT di t_produk
        $idMenu = ProdukModel::normalizeId($request->id_menu);

        // Check if ingredient already in recipe
        $exists = $this->resepByMenuQuery($idMenu)
            ->where('id_stok', $request->id_stok)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('pesan_error', 'Bahan ini sudah ada di resep.');
        }

        $creator = auth()->user();
        DB::table('t_menu_resep')->insert([
            'id_menu' => $idMenu,
            'id_stok' => $request->id_stok,
            'jumlah' => $request->jumlah,
            'created_at' => now(),
            'updated_at' => now(),
            'created_by_id' => $creator->id,
            'created_by_name' => $creator->name,
        ]);

        return redirect()->back()->with('pesan_sukses', 'Bahan berhasil ditambahkan ke resep.');
    }

    public function delete_item($id_resep)
    {
        DB::table('t_menu_resep')->where('id_resep', $id_resep)->delete();
        return redirect()->back()->with('pesan_sukses', 'Bahan berhasil dihapus dari resep.');
    }
}

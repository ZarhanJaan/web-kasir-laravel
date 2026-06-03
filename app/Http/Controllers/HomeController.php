<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Hitung total dari t_produk (Sekarang Menu)
        $totalProduk = DB::table('t_produk')->count();
        $totalStok = DB::table('t_stok_item')->sum('stok'); // Total bahan baku

        // Penjabaran Kategori Menu
        $kategoriMenu = DB::table('t_produk')
            ->select('kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori')
            ->get();

        // 3. Menu Terlaris (mengambil dari tabel riwayat_stok / produk)
        $menu_terlaris = DB::table('t_riwayat_stok')
            ->join('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->select('t_produk.nama_produk', DB::raw('SUM(t_riwayat_stok.jumlah) as total_terjual'))
            ->where('t_riwayat_stok.jenis', 'keluar')
            ->groupBy('t_produk.nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get();

        // Notifikasi barang/stok menipis (Ambil dari t_stok_item)
        $stok_menipis = DB::table('t_stok_item')
            ->where('stok', '<', 10)
            ->get();

        // Kirim data ke view
        return view('home', compact('totalProduk', 'totalStok', 'stok_menipis', 'menu_terlaris', 'kategoriMenu'));
    }
}

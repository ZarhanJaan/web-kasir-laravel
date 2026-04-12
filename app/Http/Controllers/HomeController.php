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

        // Hitung total dari t_penjualan
        $totalPenjualan = DB::table('t_penjualan')->sum('total');
        $totalData = DB::table('t_penjualan')->count();

        // Notifikasi barang/stok menipis (Ambil dari t_stok_item)
        $stok_menipis = DB::table('t_stok_item')
            ->where('stok', '<', 10)
            ->get();

        // Kirim data ke view
        return view('home', compact('totalProduk', 'totalPenjualan', 'totalStok', 'totalData', 'stok_menipis'));
    }
}

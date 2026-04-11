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
        // Hitung total dari t_produk
        $totalProduk = DB::table('t_produk')->count();
        $totalStok = DB::table('t_produk')->sum('stok');

        // Hitung total dari t_penjualan
        $totalPenjualan = DB::table('t_penjualan')->sum('total');
        $totalData = DB::table('t_penjualan')->count();

        // Kirim data ke view
        return view('home', compact('totalProduk', 'totalPenjualan', 'totalStok', 'totalData'));
    }
}

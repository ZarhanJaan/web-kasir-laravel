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
        // Hitung total stok (Total kuantitas barang)
        $totalStok = DB::table('t_stok_item')->sum('stok'); 
        
        // Hitung total jenis barang
        $totalJenis = DB::table('t_stok_item')->count();

        // Penjabaran Kategori (menggunakan kolom satuan)
        $kategoriMenu = DB::table('t_stok_item')
            ->select('satuan as kategori', DB::raw('count(*) as total'))
            ->groupBy('satuan')
            ->get();

        // Stok Terbanyak
        $stok_terbanyak = DB::table('t_stok_item')
            ->orderBy('stok', 'desc')
            ->limit(5)
            ->get();
            
        // Riwayat Transaksi (Stok Masuk & Keluar)
        $totalStokMasuk = DB::table('t_riwayat_stok')->where('jenis', 'masuk')->sum('jumlah');
        $totalStokKeluar = DB::table('t_riwayat_stok')->where('jenis', 'keluar')->sum('jumlah');

        // Notifikasi barang/stok menipis (Ambil dari t_stok_item)
        $stok_menipis = DB::table('t_stok_item')
            ->where('stok', '<', 10)
            ->get();

        // Kirim data ke view
        return view('home', compact('totalJenis', 'totalStok', 'totalStokMasuk', 'totalStokKeluar', 'stok_menipis', 'stok_terbanyak', 'kategoriMenu'));
    }
}

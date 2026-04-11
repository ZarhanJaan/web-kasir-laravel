<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokModel;
use App\Models\ProdukModel;
use App\Models\PenjualanModel;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    protected $StokModel;
    protected $ProdukModel;

    public function __construct()
    {
        $this->StokModel = new StokModel();
        $this->ProdukModel = new ProdukModel();
    }

    public function dashboard()
    {
        // Notifikasi barang/stok menipis
        $stok_menipis = DB::table('t_produk')
            ->where('stok', '<', 10)
            ->get();

        // Informasi produk terlaris
        // Menggunakan riwayat penjualan untuk menentukan produk terlaris (contoh: top 5 produk)
        $produk_terlaris = DB::table('t_penjualan')
            ->select('id_produk', DB::raw('SUM(jumlah_barang) as total_terjual'))
            ->groupBy('id_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get();

        // Di t_penjualan, id_produk bisa jadi comma-separated jika pembelian banyak tipe
        // Karena struktur tabel sederhana, kita bisa gunakan grouping sederhana atau pendekatan manual
        // Untuk mempermudah, kita ambil dari t_produk yang paling sedikit stoknya ATAU yang memiliki mutasi keluar terbanyak dari stok
        $top_terlaris = DB::table('t_riwayat_stok')
            ->join('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->select('t_produk.nama_produk', DB::raw('SUM(t_riwayat_stok.jumlah) as total_terjual'))
            ->where('t_riwayat_stok.jenis', 'keluar')
            ->groupBy('t_produk.nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get();

        return view('t_stok_dashboard', compact('stok_menipis', 'top_terlaris'));
    }

    public function add()
    {
        $produk = DB::table('t_produk')->get();
        return view('t_stok_tambah', compact('produk'));
    }

    public function keluar()
    {
        $riwayat_keluar = DB::table('t_riwayat_stok')
            ->join('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->where('t_riwayat_stok.jenis', 'keluar')
            ->orderBy('t_riwayat_stok.id_riwayat', 'desc')
            ->get();
        return view('t_stok_keluar', compact('riwayat_keluar'));
    }

    public function keluar_edit($id_riwayat)
    {
        $riwayat = DB::table('t_riwayat_stok')
            ->join('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->where('t_riwayat_stok.id_riwayat', $id_riwayat)
            ->first();
            
        if(!$riwayat) return redirect('/stok/keluar')->with('pesan_error', 'Data riwayat tidak ditemukan');
            
        return view('t_stok_keluar_edit', compact('riwayat'));
    }

    public function keluar_update(Request $request, $id_riwayat)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
            'nama_pelanggan' => 'nullable|string',
        ]);

        $riwayat_lama = DB::table('t_riwayat_stok')->where('id_riwayat', $id_riwayat)->first();
        if(!$riwayat_lama) return redirect()->back()->with('pesan_error', 'Data tidak ditemukan');

        try {
            DB::transaction(function () use ($request, $riwayat_lama, $id_riwayat) {
                // Refund stok lama
                DB::table('t_produk')->where('id_produk', $riwayat_lama->id_produk)->increment('stok', $riwayat_lama->jumlah);
                
                $produk = DB::table('t_produk')->where('id_produk', $riwayat_lama->id_produk)->first();
                if ($produk->stok < $request->jumlah) {
                    throw new \Exception('Stok master produk tidak mencukupi untuk update kuantitas ini.');
                }

                // Kurangi stok dengan jumlah baru
                DB::table('t_produk')->where('id_produk', $riwayat_lama->id_produk)->decrement('stok', $request->jumlah);

                // Update data riwayat
                DB::table('t_riwayat_stok')->where('id_riwayat', $id_riwayat)->update([
                    'jumlah' => $request->jumlah,
                    'total_harga' => ($request->jumlah * $produk->harga_jual),
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan,
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'updated_at' => now()
                ]);
            });
            return redirect('/stok/keluar')->with('pesan_sukses', 'Data stok keluar berhasil diperbarui dan stok master tervalidasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('pesan_error', $e->getMessage());
        }
    }

    public function keluar_delete($id_riwayat)
    {
        try {
            $riwayat = DB::table('t_riwayat_stok')->where('id_riwayat', $id_riwayat)->first();
            if(!$riwayat) return redirect()->back()->with('pesan_error', 'Data tidak ditemukan');

            DB::transaction(function () use ($riwayat, $id_riwayat) {
                // Refund stok ke t_produk
                DB::table('t_produk')->where('id_produk', $riwayat->id_produk)->increment('stok', $riwayat->jumlah);
                
                // Eksekusi delete riwayat
                DB::table('t_riwayat_stok')->where('id_riwayat', $id_riwayat)->delete();
            });
            return redirect('/stok/keluar')->with('pesan_sukses', 'Riwayat dihapus dan stok barang terkait berhasil dikembalikan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('pesan_error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function keluar_add()
    {
        $produk = DB::table('t_produk')->get();
        return view('t_stok_keluar_add', compact('produk'));
    }

    public function keluar_insert(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_pelanggan' => 'required|string',
            'id_produk' => 'required|array',
            'jumlah_barang' => 'required|array',
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->id_produk as $i => $id_produk) {
                    $jumlah = $request->jumlah_barang[$i];
                    if(empty($id_produk) || $jumlah <= 0) continue;

                    $produk = DB::table('t_produk')->where('id_produk', $id_produk)->first();
                    if ($produk->stok < $jumlah) {
                        throw new \Exception('Stok tidak mencukupi untuk: ' . $produk->nama_produk);
                    }

                    // 1. Kurangi master
                    DB::table('t_produk')->where('id_produk', $id_produk)->decrement('stok', $jumlah);

                    // 2. Insert ke riwayat
                    DB::table('t_riwayat_stok')->insert([
                        'id_produk' => $id_produk,
                        'jenis' => 'keluar',
                        'jumlah' => $jumlah,
                        'total_harga' => ($jumlah * $produk->harga_jual),
                        'tanggal' => $request->tanggal,
                        'keterangan' => $request->keterangan ?: 'Stok Keluar Manual',
                        'nama_pelanggan' => $request->nama_pelanggan,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            });
            return redirect('/stok/keluar')->with('pesan_sukses', 'Riwayat stok keluar manual berhasil dicatat!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('pesan_error', $e->getMessage());
        }
    }

    public function insert(Request $request)
    {
        $request->validate([
            'id_produk' => 'required',
            'jenis' => 'required|in:masuk,keluar',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Insert ke Riwayat Stok
                $this->StokModel->addData([
                    'id_produk' => $request->id_produk,
                    'jenis' => $request->jenis,
                    'jumlah' => $request->jumlah,
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan ?: 'Stok Masuk',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Update Stok di t_produk
                $produk = DB::table('t_produk')->where('id_produk', $request->id_produk)->first();
                if ($produk) {
                    $stok_baru = $produk->stok;
                    if ($request->jenis == 'masuk') {
                        $stok_baru += $request->jumlah;
                    } else {
                        if ($produk->stok < $request->jumlah) {
                            throw new \Exception('Stok tidak mencukupi untuk dikeluarkan.');
                        }
                        $stok_baru -= $request->jumlah;
                    }

                    DB::table('t_produk')
                        ->where('id_produk', $request->id_produk)
                        ->update(['stok' => $stok_baru]);
                }
            });

            return redirect()->route('stok.riwayat')->with('pesan_sukses', 'Mutasi stok berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('pesan_error', $e->getMessage());
        }
    }

    public function riwayat(Request $request)
    {
        $awal = $request->get('tgl_awal');
        $akhir = $request->get('tgl_akhir');

        if ($awal && $akhir) {
            $riwayat = $this->StokModel->filterByDate($awal, $akhir);
        } else {
            $riwayat = $this->StokModel->allData();
        }

        return view('t_stok_riwayat', compact('riwayat', 'awal', 'akhir'));
    }
}

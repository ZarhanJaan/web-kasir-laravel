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
        $this->middleware('auth');
        $this->StokModel = new StokModel();
        $this->ProdukModel = new ProdukModel();
    }

    public function dashboard()
    {
        // Notifikasi barang/stok menipis (Ambil dari t_stok_item)
        $stok_menipis = DB::table('t_stok_item')
            ->where('stok', '<', 10)
            ->get();

        // Informasi produk terlaris (Tetap dari t_penjualan)
        $top_terlaris = DB::table('t_riwayat_stok')
            ->join('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->select('t_produk.nama_produk', DB::raw('SUM(t_riwayat_stok.jumlah) as total_terjual'))
            ->where('t_riwayat_stok.jenis', 'keluar')
            ->groupBy('t_produk.nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get();

        // Daftar Lengkap Stok Dimiliki (Request User #2)
        $stok_lengkap = DB::table('t_stok_item')->get();

        return view('t_stok_dashboard', compact('stok_menipis', 'top_terlaris', 'stok_lengkap'));
    }

    public function add()
    {
        $produk = DB::table('t_stok_item')->get(); // Sekarang Stok Masuk memilih dari t_stok_item
        return view('t_stok_tambah', compact('produk'));
    }

    public function bahan()
    {
        $bahan = DB::table('t_stok_item')->get();
        return view('t_stok_bahan', compact('bahan'));
    }

    public function bahan_insert(Request $request)
    {
        $request->validate([
            'id_stok' => 'required|unique:t_stok_item,id_stok',
            'nama_stok' => 'required',
            'stok' => 'nullable|numeric'
        ]);

        DB::table('t_stok_item')->insert([
            'id_stok' => $request->id_stok,
            'nama_stok' => $request->nama_stok,
            'stok' => $request->stok ?? 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('pesan_sukses', 'Bahan berhasil ditambahkan.');
    }

    public function bahan_delete($id)
    {
        DB::table('t_stok_item')->where('id_stok', $id)->delete();
        return redirect()->back()->with('pesan_sukses', 'Bahan berhasil dihapus.');
    }

    public function insert(Request $request)
    {
        $request->validate([
            'id_riwayat' => 'required|unique:t_riwayat_stok,id_riwayat',
            'id_produk' => 'required', // Input manual (ID atau Nama)
            'jenis' => 'required|in:masuk,keluar',
            'jumlah' => 'required|numeric|min:1',
            'harga_beli' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            // Opsional untuk bahan baru
            'id_stok_baru' => 'nullable|unique:t_stok_item,id_stok',
        ], [
            'id_riwayat.unique' => 'ID Riwayat sudah ada.',
            'id_stok_baru.unique' => 'ID Bahan Baru sudah terdaftar.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Cari bahan berdasarkan ID atau Nama
                $item = DB::table('t_stok_item')
                    ->where('id_stok', $request->id_produk)
                    ->orWhere('nama_stok', $request->id_produk)
                    ->first();

                $id_stok_fix = null;

                if (!$item) {
                    // Jika tidak ditemukan, coba registrasi sebagai bahan baru
                    if (!$request->id_stok_baru) {
                        throw new \Exception('Bahan baku "' . $request->id_produk . '" belum terdaftar. Silakan isi "ID Bahan Baru" di bawah untuk mendaftarkannya secara otomatis.');
                    }

                    // Registrasi bahan baru (Satuan otomatis pakai default DB: Pcs)
                    DB::table('t_stok_item')->insert([
                        'id_stok' => $request->id_stok_baru,
                        'nama_stok' => $request->id_produk,
                        'stok' => 0, 
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $id_stok_fix = $request->id_stok_baru;
                } else {
                    $id_stok_fix = $item->id_stok;
                }

                // Insert ke Riwayat Stok
                $this->StokModel->addData([
                    'id_riwayat' => $request->id_riwayat,
                    'id_produk' => null,
                    'id_stok' => $id_stok_fix,
                    'jenis' => $request->jenis,
                    'jumlah' => $request->jumlah,
                    'harga_beli' => $request->harga_beli,
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan ?: 'Stok Masuk',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Update Stok di t_stok_item
                if ($request->jenis == 'masuk') {
                    DB::table('t_stok_item')->where('id_stok', $id_stok_fix)->increment('stok', $request->jumlah);
                } else {
                    $currentItem = DB::table('t_stok_item')->where('id_stok', $id_stok_fix)->first();
                    if ($currentItem->stok < $request->jumlah) {
                        throw new \Exception('Stok tidak mencukupi untuk dikeluarkan.');
                    }
                    DB::table('t_stok_item')->where('id_stok', $id_stok_fix)->decrement('stok', $request->jumlah);
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

        $query = DB::table('t_riwayat_stok')
            ->leftJoin('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->leftJoin('t_stok_item', 't_riwayat_stok.id_stok', '=', 't_stok_item.id_stok')
            ->select('t_riwayat_stok.*', 't_produk.nama_produk', 't_stok_item.nama_stok')
            ->orderBy('t_riwayat_stok.created_at', 'desc');

        if ($awal && $akhir) {
            $query->whereBetween('t_riwayat_stok.tanggal', [$awal, $akhir]);
        }

        $riwayat = $query->get();

        return view('t_stok_riwayat', compact('riwayat', 'awal', 'akhir'));
    }

    public function keluar()
    {
        $riwayat_keluar = DB::table('t_riwayat_stok')
            ->leftJoin('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->leftJoin('t_stok_item', 't_riwayat_stok.id_stok', '=', 't_stok_item.id_stok')
            ->where('t_riwayat_stok.jenis', 'keluar')
            ->select('t_riwayat_stok.*', 't_produk.nama_produk', 't_stok_item.nama_stok')
            ->orderBy('t_riwayat_stok.tanggal', 'desc')
            ->orderBy('t_riwayat_stok.id_riwayat', 'desc')
            ->get();
        return view('t_stok_keluar', compact('riwayat_keluar'));
    }

    public function keluar_add()
    {
        $produk = DB::table('t_stok_item')->get();
        return view('t_stok_keluar_add', compact('produk'));
    }

    public function keluar_insert(Request $request)
    {
        $request->validate([
            'id_riwayat_base' => 'required',
            'id_produk' => 'required|array',
            'jumlah_barang' => 'required|array',
            'tanggal' => 'required|date',
            'nama_pelanggan' => 'required',
            'keterangan' => 'nullable'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $historyCount = 1;
                foreach ($request->id_produk as $i => $id_stok) {
                    $jumlah = $request->jumlah_barang[$i];
                    if (empty($id_stok) || $jumlah <= 0) continue;

                    $item = DB::table('t_stok_item')->where('id_stok', $id_stok)->first();
                    if (!$item || $item->stok < $jumlah) {
                        throw new \Exception('Stok ' . ($item->nama_stok ?? 'Bahan') . ' tidak mencukupi.');
                    }

                    // Manual ID generation for history based on base ID
                    $manual_id = (int)($request->id_riwayat_base . str_pad($historyCount, 2, '0', STR_PAD_LEFT));
                    $historyCount++;

                    // 1. Catat di t_riwayat_stok
                    DB::table('t_riwayat_stok')->insert([
                        'id_riwayat' => $manual_id,
                        'id_stok' => $id_stok,
                        'id_produk' => 'Bhn ' . $id_stok, // Filler for id_produk varchar column
                        'jenis' => 'keluar',
                        'jumlah' => $jumlah,
                        'tanggal' => $request->tanggal,
                        'nama_pelanggan' => $request->nama_pelanggan,
                        'keterangan' => $request->keterangan ?? 'Stok Keluar Manual',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // 2. Kurangi stok
                    DB::table('t_stok_item')->where('id_stok', $id_stok)->decrement('stok', $jumlah);
                }
            });

            return redirect()->route('stok.keluar')->with('pesan_sukses', 'Pengeluaran stok berhasil dicatat.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('pesan_error', $e->getMessage());
        }
    }

    public function keluar_delete($id)
    {
        // Refund logic (Optional, but good for data integrity)
        $riwayat = DB::table('t_riwayat_stok')->where('id_riwayat', $id)->first();
        if ($riwayat && $riwayat->id_stok) {
            DB::table('t_stok_item')->where('id_stok', $riwayat->id_stok)->increment('stok', $riwayat->jumlah);
        }
        DB::table('t_riwayat_stok')->where('id_riwayat', $id)->delete();
        return redirect()->back()->with('pesan_sukses', 'Riwayat berhasil dihapus (Stok telah dikembalikan).');
    }
}

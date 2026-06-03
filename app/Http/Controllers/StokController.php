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
    public function __construct()
    {
        $this->middleware('auth');
        $this->StokModel = new StokModel();
    }

    public function dashboard()
    {
        // Notifikasi barang/stok menipis (Ambil dari t_stok_item)
        $stok_menipis = DB::table('t_stok_item')
            ->where('stok', '<', 10)
            ->get();

        $top_terlaris = collect([]);

        // Daftar Lengkap Stok Dimiliki (Request User #2)
        $stok_lengkap = DB::table('t_stok_item')->get();

        return view('t_stok_dashboard', compact('stok_menipis', 'top_terlaris', 'stok_lengkap'));
    }

    public function edit_stok($id)
    {
        $stok = DB::table('t_stok_item')->where('id_stok', $id)->first();
        if (!$stok) {
            return redirect()->route('stok.dashboard')->with('pesan_error', 'Stok tidak ditemukan.');
        }
        $kategori = DB::table('t_kategori')->orderBy('nama_kategori')->get();
        return view('t_stok_edit', compact('stok', 'kategori'));
    }

    public function update_stok(Request $request, $id)
    {
        $request->validate([
            'nama_stok' => 'required',
            'stok' => 'required|numeric',
            'satuan' => 'nullable'
        ]);

        try {
            DB::table('t_stok_item')->where('id_stok', $id)->update([
                'nama_stok' => $request->nama_stok,
                'stok' => $request->stok,
                'satuan' => $request->satuan,
                'updated_at' => now()
            ]);
            return redirect()->route('stok.dashboard')->with('pesan_sukses', 'Informasi stok berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('pesan_error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function add()
    {
        $produk = DB::table('t_stok_item')->get(); // Sekarang Stok Masuk memilih dari t_stok_item
        $kategori = DB::table('t_kategori')->orderBy('nama_kategori')->get();
        return view('t_stok_tambah', compact('produk', 'kategori'));
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
                    'satuan' => ($request->jenis == 'keluar') ? 'pcs' : $request->satuan,
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan ?: 'Stok Masuk',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Hitung Qty Real (Pcs) untuk update stok gudang
                $qty_untuk_gudang = $request->jumlah;
                if ($request->jenis == 'masuk' && $request->satuan == 'box' && $request->isi_pcs_per_box) {
                    $qty_untuk_gudang = $request->jumlah * $request->isi_pcs_per_box;
                }

                // Update Stok di t_stok_item (Selalu dalam satuan Pcs)
                if ($request->jenis == 'masuk') {
                    DB::table('t_stok_item')->where('id_stok', $id_stok_fix)->increment('stok', $qty_untuk_gudang);
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
            ->leftJoin('t_stok_item', 't_riwayat_stok.id_stok', '=', 't_stok_item.id_stok')
            ->select('t_riwayat_stok.*', 't_stok_item.nama_stok')
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
            ->leftJoin('t_stok_item', 't_riwayat_stok.id_stok', '=', 't_stok_item.id_stok')
            ->where('t_riwayat_stok.jenis', 'keluar')
            ->select('t_riwayat_stok.*', 't_stok_item.nama_stok')
            ->orderBy('t_riwayat_stok.tanggal', 'desc')
            ->orderBy('t_riwayat_stok.id_riwayat', 'desc')
            ->get();
        return view('t_stok_keluar', compact('riwayat_keluar'));
    }

    public function keluar_add()
    {
        $produk = DB::table('t_stok_item')->get();
        $kategori = DB::table('t_kategori')->orderBy('nama_kategori')->get();
        return view('t_stok_keluar_add', compact('produk', 'kategori'));
    }

    public function keluar_insert(Request $request)
    {
        $request->validate([
            'id_riwayat_base' => 'required',
            'id_produk' => 'required|array',
            'jumlah_barang' => 'required|array',
            'tanggal' => 'required|date',
            'satuan' => 'required|string|max:255',
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
                        'satuan' => $request->satuan,
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

    public function keluar_edit($id)
    {
        $riwayat = DB::table('t_riwayat_stok')
            ->leftJoin('t_stok_item', 't_riwayat_stok.id_stok', '=', 't_stok_item.id_stok')
            ->where('t_riwayat_stok.id_riwayat', $id)
            ->select('t_riwayat_stok.*', 't_stok_item.nama_stok as nama_produk')
            ->first();

        if (!$riwayat) {
            return redirect()->route('stok.keluar')->with('pesan_error', 'Data tidak ditemukan.');
        }

        $kategori = DB::table('t_kategori')->orderBy('nama_kategori')->get();
        return view('t_stok_keluar_edit', compact('riwayat', 'kategori'));
    }

    public function keluar_update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'satuan' => 'required|string|max:255',
            'keterangan' => 'nullable'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $riwayat_lama = DB::table('t_riwayat_stok')->where('id_riwayat', $id)->first();
                if (!$riwayat_lama) {
                    throw new \Exception('Data riwayat tidak ditemukan.');
                }

                // Hitung selisih
                $selisih = $request->jumlah - $riwayat_lama->jumlah;

                if ($riwayat_lama->id_stok) {
                    $item = DB::table('t_stok_item')->where('id_stok', $riwayat_lama->id_stok)->first();
                    
                    if ($selisih > 0) {
                        // Jika jumlah keluar NAMBAH, berarti stok di gudang BERKURANG
                        if (!$item || $item->stok < $selisih) {
                            throw new \Exception('Stok ' . ($item->nama_stok ?? 'Bahan') . ' tidak mencukupi untuk dikeluarkan lebih banyak.');
                        }
                        DB::table('t_stok_item')->where('id_stok', $riwayat_lama->id_stok)->decrement('stok', $selisih);
                    } else if ($selisih < 0) {
                        // Jika jumlah keluar BERKURANG, berarti stok di gudang KEMBALI
                        DB::table('t_stok_item')->where('id_stok', $riwayat_lama->id_stok)->increment('stok', abs($selisih));
                    }
                }

                DB::table('t_riwayat_stok')->where('id_riwayat', $id)->update([
                    'jumlah' => $request->jumlah,
                    'tanggal' => $request->tanggal,
                    'satuan' => $request->satuan,
                    'keterangan' => $request->keterangan,
                    'updated_at' => now()
                ]);
            });

            return redirect()->route('stok.keluar')->with('pesan_sukses', 'Data berhasil diperbarui.');
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

    public function laporan_grafik(Request $request)
    {
        $tgl_awal = $request->get('tgl_awal', date('Y-m-01')); // default awal bulan
        $tgl_akhir = $request->get('tgl_akhir', date('Y-m-d')); // default hari ini

        // Data agregat per hari untuk grafik chart.js
        $riwayat_masuk = DB::table('t_riwayat_stok')
            ->where('jenis', 'masuk')
            ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])
            ->selectRaw('tanggal, sum(jumlah) as total_masuk')
            ->groupBy('tanggal')
            ->get()->keyBy('tanggal');

        $riwayat_keluar = DB::table('t_riwayat_stok')
            ->where('jenis', 'keluar')
            ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])
            ->selectRaw('tanggal, sum(jumlah) as total_keluar')
            ->groupBy('tanggal')
            ->get()->keyBy('tanggal');

        $period = new \DatePeriod(
            new \DateTime($tgl_awal),
            new \DateInterval('P1D'),
            (new \DateTime($tgl_akhir))->modify('+1 day')
        );

        $labels = [];
        $data_masuk = [];
        $data_keluar = [];

        foreach ($period as $date) {
            $tgl = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $data_masuk[] = isset($riwayat_masuk[$tgl]) ? $riwayat_masuk[$tgl]->total_masuk : 0;
            $data_keluar[] = isset($riwayat_keluar[$tgl]) ? $riwayat_keluar[$tgl]->total_keluar : 0;
        }

        // Data rekap per barang untuk tabel detail (dan PDF)
        $rekap_barang = DB::table('t_riwayat_stok')
            ->leftJoin('t_stok_item', 't_riwayat_stok.id_stok', '=', 't_stok_item.id_stok')
            ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])
            ->selectRaw('t_riwayat_stok.id_stok, t_stok_item.nama_stok, 
                SUM(CASE WHEN jenis="masuk" THEN jumlah ELSE 0 END) as total_masuk,
                SUM(CASE WHEN jenis="keluar" THEN jumlah ELSE 0 END) as total_keluar')
            ->groupBy('t_riwayat_stok.id_stok', 't_stok_item.nama_stok')
            ->orderBy('t_stok_item.nama_stok')
            ->get();

        return view('t_stok_laporan_grafik', compact('tgl_awal', 'tgl_akhir', 'labels', 'data_masuk', 'data_keluar', 'rekap_barang'));
    }

    public function laporan_grafik_pdf_masuk(Request $request)
    {
        return $this->laporan_grafik_pdf_by_jenis($request, 'masuk');
    }

    public function laporan_grafik_pdf_keluar(Request $request)
    {
        return $this->laporan_grafik_pdf_by_jenis($request, 'keluar');
    }

    private function laporan_grafik_pdf_by_jenis(Request $request, string $jenis)
    {
        $tgl_awal = $request->get('tgl_awal', date('Y-m-01'));
        $tgl_akhir = $request->get('tgl_akhir', date('Y-m-d'));

        $rekap_barang = DB::table('t_riwayat_stok')
            ->leftJoin('t_stok_item', 't_riwayat_stok.id_stok', '=', 't_stok_item.id_stok')
            ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])
            ->where('jenis', $jenis)
            ->selectRaw('t_riwayat_stok.id_stok, t_stok_item.nama_stok, SUM(jumlah) as total')
            ->groupBy('t_riwayat_stok.id_stok', 't_stok_item.nama_stok')
            ->orderBy('t_stok_item.nama_stok')
            ->get();

        $pdf = \PDF::loadView('t_stok_laporan_pdf', compact('tgl_awal', 'tgl_akhir', 'rekap_barang', 'jenis'));
        $pdf->setPaper('A4', 'portrait');

        $label = $jenis === 'masuk' ? 'Masuk' : 'Keluar';

        return $pdf->download('Laporan_Stok_' . $label . '_' . $tgl_awal . '_sd_' . $tgl_akhir . '.pdf');
    }
}

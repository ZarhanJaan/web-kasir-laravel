<?php

namespace App\Http\Controllers;

use App\Models\ProdukModel;
use Illuminate\Http\Request;
use App\Models\PenjualanModel;
use App\Exports\PenjualanExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class PenjualanController extends Controller
{
    protected $PenjualanModel;

    public function __construct()
    {
        $this->middleware('auth');
        $this->PenjualanModel = new PenjualanModel();
    }

    public function index()
    {
        $data = [
            'penjualan' => $this->PenjualanModel->allData(),
        ];
        return view('t_penjualan', $data);
    }

    public function pos()
    {
        $produk = DB::table('t_produk as p')
            ->select('p.*', DB::raw('
                CASE 
                    WHEN NOT EXISTS (SELECT 1 FROM t_menu_resep mr WHERE mr.id_menu = p.id_produk) THEN 1
                    WHEN (
                        SELECT COUNT(*) 
                        FROM t_menu_resep mr 
                        JOIN t_stok_item si ON mr.id_stok = si.id_stok 
                        WHERE mr.id_menu = p.id_produk AND si.stok < mr.jumlah
                    ) = 0 THEN 1
                    ELSE 0
                END as is_available
            '))
            ->get();
            
        return view('t_transaksi_pos', compact('produk'));
    }

    public function pos_insert(Request $request)
    {
        $request->validate([
            'id_penjualan' => 'required|unique:t_penjualan,id_penjualan',
            'tanggal' => 'required',    
            'nama_pelanggan' => 'required',         
            'jumlah_barang' => 'required|array', 
            'id_produk' =>  'required|array',
            'total' => 'required',
            'metode_pembayaran' => 'required'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $historyCount = 1;
                // Loop untuk setiap produk yang dipesan via POS
                foreach ($request->id_produk as $i => $id_produk) {
                    $menu = DB::table('t_produk')->where('id_produk', $id_produk)->first();
                    $jumlah_beli = $request->jumlah_barang[$i];
                    
                    if(empty($id_produk) || $jumlah_beli <= 0) continue; 
                    
                    // Ambil RESEP untuk menu ini
                    $resep = DB::table('t_menu_resep')->where('id_menu', $id_produk)->get();
                    
                    foreach ($resep as $item) {
                        $total_kebutuhan = $item->jumlah * $jumlah_beli;
                        $stok_item = DB::table('t_stok_item')->where('id_stok', $item->id_stok)->first();
                        
                        if (!$stok_item || $stok_item->stok < $total_kebutuhan) {
                            throw new \Exception('Stok ' . ($stok_item->nama_stok ?? 'Bahan') . ' tidak mencukupi untuk ' . $menu->nama_produk);
                        }

                        // Generate unique history ID based on Sale ID (Manual)
                        // Example: Sale 10001 -> History 1000101, 1000102...
                        $manual_riwayat_id = (int)($request->id_penjualan . str_pad($historyCount, 2, '0', STR_PAD_LEFT));
                        $historyCount++;

                        // 1. Kurangi stok bahan
                        DB::table('t_stok_item')->where('id_stok', $item->id_stok)->decrement('stok', $total_kebutuhan);

                        // 2. Catat di t_riwayat_stok
                        DB::table('t_riwayat_stok')->insert([
                            'id_riwayat' => $manual_riwayat_id,
                            'id_produk' => $id_produk,
                            'id_stok' => $item->id_stok,
                            'jenis' => 'keluar',
                            'jumlah' => $total_kebutuhan,
                            'total_harga' => 0, 
                            'tanggal' => date('Y-m-d'),
                            'keterangan' => 'Terpakai (Menu: ' . $menu->nama_produk . ')',
                            'nama_pelanggan' => $request->nama_pelanggan,
                            'satuan' => 'pcs',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }

                // 3. Simpan rekap Data Penjualan Utama
                $data = [
                    'id_penjualan' => $request->id_penjualan,
                    'tanggal' => $request->tanggal,
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'jumlah_barang' => implode(',', $request->jumlah_barang),
                    'id_produk' => implode(',', $request->id_produk),
                    'total' => $request->total,
                    'metode_pembayaran' => $request->metode_pembayaran
                ];
                $this->PenjualanModel->addData($data);
            });
            return redirect()->route('kasir')->with('pesan_sukses_trx', $request->id_penjualan);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('pesan_error', $e->getMessage());
        }
    }

    public function struk($id_penjualan)
    {
        $trx = DB::table('t_penjualan')->where('id_penjualan', $id_penjualan)->first();
        if (!$trx)
            abort(404);

        $ids = explode(',', $trx->id_produk);
        $produk = DB::table('t_produk')->whereIn('id_produk', $ids)->get();

        $settingsPath = storage_path('app/settings.json');
        $settings = [];
        if (\Illuminate\Support\Facades\File::exists($settingsPath)) {
            $settings = json_decode(\Illuminate\Support\Facades\File::get($settingsPath), true);
        }

        $store_name = $settings['store_name'] ?? 'Toko Sembako';
        $store_address = $settings['store_address'] ?? 'Jl. Contoh Alamat No.123';

        return view('t_struk', compact('trx', 'produk', 'store_name', 'store_address'));
    }

    public function add()
    {
        $produkList = ProdukModel::all();
        return view('t_addpenjualan', compact('produkList'));

    }

    public function insert(Request $request)
    {
        $request->validate([
            'id_penjualan' => 'required|unique:t_penjualan,id_penjualan|min:4|max:6',
            'tanggal' => 'required',
            'nama_pelanggan' => 'required',
            'jumlah_barang' => 'required|array',
            'id_produk' => 'required|array',
            'total' => 'required',
        ], [
            'id_penjualan.required' => 'Silakan isi ID Penjualan.',
            'id_penjualan.unique' => 'ID Penjualan sudah ada.',
            'id_penjualan.min' => 'ID Penjualan minimal 4 karakter.',
            'id_penjualan.max' => 'ID Penjualan maximal 6 karakter.',
            'tanggal.required' => 'Silakan isi Tanggal.',
            'nama_pelanggan.required' => 'Silakan isi nama Pelanggan.',
            'jumlah_barang.required' => 'Silakan isi jumlah barang.',
            'id_produk.required' => 'Silakan isi id produk.',
            'total.required' => 'Silakan isi total harga.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Loop untuk setiap produk yang dipesan
                foreach ($request->id_produk as $i => $id_produk) {
                    $produk = ProdukModel::findOrFail($id_produk);
                    $jumlah = $request->jumlah_barang[$i];
                    if ($produk->stok < $jumlah) {
                        throw new \Exception('Stok tidak mencukupi untuk produk ' . $produk->nama_produk);
                    }
                    $produk->stok -= $jumlah;
                    $produk->save();
                }
                // Simpan data penjualan utama
                $data = [
                    'id_penjualan' => $request->id_penjualan,
                    'tanggal' => $request->tanggal,
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'jumlah_barang' => implode(',', $request->jumlah_barang),
                    'id_produk' => implode(',', $request->id_produk),
                    'total' => $request->total,
                ];
                $this->PenjualanModel->addData($data);
            });
            return redirect()->route('riwayat-transaksi')->with('pesan_sukses', 'Data Berhasil di Tambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('pesan_error', $e->getMessage());
        }
    }

    public function edit($edit_penjualan)
    {

        if (!$this->PenjualanModel->editData($edit_penjualan)) {
            abort(404);
        }
        $data = [
            'penjualan' => $this->PenjualanModel->editData($edit_penjualan),
        ];
        return view('t_editpenjualan', $data);
    }

    public function update($edit_penjualan)
    {
        Request()->validate([
            'tanggal' => 'required',
            'nama_pelanggan' => 'required',
            'jumlah_barang' => 'required',
            'id_produk' => 'required',
            'total' => 'required',
        ], [
            'tanggal.required' => 'Silakan isi Tanggal.',
            'nama_pelanggan.required' => 'Silakan isi nama Pelanggan.',
            'jumlah_barang.required' => 'Silakan isi jumlah barang.',
            'id_produk.required' => 'Silakan isi id produk.',
            'total.required' => 'Silakan isi total harga.',
        ]);

        $data = [
            'id_penjualan' => Request()->id_penjualan,
            'tanggal' => Request()->tanggal,
            'nama_pelanggan' => Request()->nama_pelanggan,
            'jumlah_barang' => is_array(Request()->jumlah_barang) ? implode(',', Request()->jumlah_barang) : Request()->jumlah_barang,
            'id_produk' => is_array(Request()->id_produk) ? implode(',', Request()->id_produk) : Request()->id_produk,
            'total' => Request()->total,
        ];

        $this->PenjualanModel->updateData($edit_penjualan, $data);
        return redirect()->route('riwayat-transaksi')->with('pesan_sukses', 'Data Berhasil di Update');
    }

    public function delete($id_penjualan)
    {

        $this->PenjualanModel->deleteData($id_penjualan);
        return redirect()->route('riwayat-transaksi')->with('pesan_hapus', 'Data Berhasil di Delete');

    }

    public function cetakexcel_page()
    {
        return view('cetakexcel_page');
    }

    public function exportexcel()
    {
        return Excel::download(new PenjualanExport, 'data_penjualan.xlsx');
    }

    public function exportexcel_tanggal(Request $request)
    {
        $request->validate([
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
        ]);

        return Excel::download(new PenjualanExport($request->tgl_awal, $request->tgl_akhir), 'data_penjualan_' . $request->tgl_awal . '_sampai_' . $request->tgl_akhir . '.xlsx');
    }

    public function exportpdf()
    {
        $data = PenjualanModel::all();
        view()->share('data', $data);
        $pdf = PDF::loadview('datapenjualan_pdf', $data);
        return $pdf->download('data.pdf');
    }

    public function datapenjualan_tgl_pdf()
    {
        return view('datapenjualan_tgl_pdf');
    }

    public function cetak_tgl_pdf($tglawal, $tglakhir)
    {
        $cetakpertanggal = PenjualanModel::whereBetween('tanggal', [$tglawal, $tglakhir])->get();
        $pdf = PDF::loadview('cetak_pertanggal_pdf', compact('cetakpertanggal'));
        return $pdf->download('laporan-penjualan-pertanggal.pdf');
    }


    public function laporan()
    {
        // 1. Penjualan Hari Ini (Per Jam)
        $penjualan_hari_ini = DB::table('t_penjualan')
            ->select(DB::raw('HOUR(created_at) as jam'), DB::raw('SUM(total) as total_penjualan'))
            ->whereDate('created_at', date('Y-m-d'))
            ->groupBy('jam')
            ->orderBy('jam', 'asc')
            ->get();

        // 2. Penjualan Bulanan (Tahun Ini)
        $tahun_ini = date('Y');
        $penjualan_bulanan = DB::table('t_penjualan')
            ->select(DB::raw('MONTH(tanggal) as bulan'), DB::raw('SUM(total) as total_penjualan'))
            ->whereYear('tanggal', $tahun_ini)
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
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

        // 4. Laporan Stok Masuk & Keluar Bulanan
        $stok_in_out = DB::table('t_riwayat_stok')
            ->select(
                DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as bulan"), 
                'jenis', 
                DB::raw('SUM(jumlah) as qty')
            )
            ->groupBy('bulan', 'jenis')
            ->orderBy('bulan', 'asc')
            ->get();

        // 5. Daftar Bulan Tersedia untuk Filter Mingguan
        $available_months = DB::table('t_riwayat_stok')
            ->select(DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as bulan"))
            ->distinct()
            ->orderBy('bulan', 'desc')
            ->get();

        // 6. Riwayat Stok Terbaru untuk List
        $riwayat_stok_recent = DB::table('t_riwayat_stok')
            ->leftJoin('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->leftJoin('t_stok_item', 't_riwayat_stok.id_stok', '=', 't_stok_item.id_stok')
            ->select('t_riwayat_stok.*', 't_produk.nama_produk', 't_stok_item.nama_stok')
            ->orderBy('t_riwayat_stok.tanggal', 'desc')
            ->orderBy('t_riwayat_stok.id_riwayat', 'desc')
            ->limit(20)
            ->get();

        return view('t_laporan', compact(
            'penjualan_hari_ini', 
            'penjualan_bulanan', 
            'menu_terlaris', 
            'stok_in_out',
            'available_months',
            'riwayat_stok_recent'
        ));
    }

    public function exportTerlarisPdf()
    {
        $data = DB::table('t_riwayat_stok')
            ->join('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->select('t_produk.nama_produk', DB::raw('SUM(t_riwayat_stok.jumlah) as total_terjual'))
            ->where('t_riwayat_stok.jenis', 'keluar')
            ->groupBy('t_produk.nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(20)
            ->get();

        $pdf = \PDF::loadview('t_laporan_terlaris_pdf', compact('data'));
        return $pdf->download('laporan-menu-terlaris.pdf');
    }

    public function exportStokPdf()
    {
        $tgl_mulai = now()->subDays(30)->format('Y-m-d');
        $data = DB::table('t_riwayat_stok')
            ->leftJoin('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->leftJoin('t_stok_item', 't_riwayat_stok.id_stok', '=', 't_stok_item.id_stok')
            ->select(
                't_riwayat_stok.tanggal',
                't_riwayat_stok.jenis',
                't_riwayat_stok.satuan',
                DB::raw('SUM(t_riwayat_stok.jumlah) as jumlah'),
                DB::raw('MAX(t_produk.nama_produk) as nama_produk'),
                DB::raw('MAX(t_stok_item.nama_stok) as nama_stok'),
                DB::raw("NULL as nama_pelanggan"),
                DB::raw("GROUP_CONCAT(DISTINCT COALESCE(t_riwayat_stok.nama_pelanggan, t_riwayat_stok.keterangan) SEPARATOR ', ') as keterangan")
            )
            ->where('t_riwayat_stok.tanggal', '>=', $tgl_mulai)
            ->groupBy('t_riwayat_stok.tanggal', 't_riwayat_stok.id_produk', 't_riwayat_stok.id_stok', 't_riwayat_stok.jenis', 't_riwayat_stok.satuan')
            ->orderBy('t_riwayat_stok.tanggal', 'desc')
            ->get();

        $title = 'Laporan Mutasi Stok (Masuk & Keluar)';
        $pdf = \PDF::loadview('t_laporan_stok_pdf', compact('data', 'tgl_mulai', 'title'));
        return $pdf->download('laporan-mutasi-stok.pdf');
    }

    public function exportStokMasukPdf()
    {
        $tgl_mulai = now()->subDays(30)->format('Y-m-d');
        $data = DB::table('t_riwayat_stok')
            ->leftJoin('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->leftJoin('t_stok_item', 't_riwayat_stok.id_stok', '=', 't_stok_item.id_stok')
            ->select(
                't_riwayat_stok.tanggal',
                't_riwayat_stok.jenis',
                't_riwayat_stok.satuan',
                DB::raw('SUM(t_riwayat_stok.jumlah) as jumlah'),
                DB::raw('MAX(t_produk.nama_produk) as nama_produk'),
                DB::raw('MAX(t_stok_item.nama_stok) as nama_stok'),
                DB::raw("NULL as nama_pelanggan"),
                DB::raw("GROUP_CONCAT(DISTINCT COALESCE(t_riwayat_stok.nama_pelanggan, t_riwayat_stok.keterangan) SEPARATOR ', ') as keterangan")
            )
            ->where('t_riwayat_stok.tanggal', '>=', $tgl_mulai)
            ->where('t_riwayat_stok.jenis', 'masuk')
            ->groupBy('t_riwayat_stok.tanggal', 't_riwayat_stok.id_produk', 't_riwayat_stok.id_stok', 't_riwayat_stok.jenis', 't_riwayat_stok.satuan')
            ->orderBy('t_riwayat_stok.tanggal', 'desc')
            ->get();

        $title = 'Laporan Stok Masuk';
        $pdf = \PDF::loadview('t_laporan_stok_pdf', compact('data', 'tgl_mulai', 'title'));
        return $pdf->download('laporan-stok-masuk.pdf');
    }

    public function exportStokKeluarPdf()
    {
        $tgl_mulai = now()->subDays(30)->format('Y-m-d');
        $data = DB::table('t_riwayat_stok')
            ->leftJoin('t_produk', 't_riwayat_stok.id_produk', '=', 't_produk.id_produk')
            ->leftJoin('t_stok_item', 't_riwayat_stok.id_stok', '=', 't_stok_item.id_stok')
            ->select(
                't_riwayat_stok.tanggal',
                't_riwayat_stok.jenis',
                't_riwayat_stok.satuan',
                DB::raw('SUM(t_riwayat_stok.jumlah) as jumlah'),
                DB::raw('MAX(t_produk.nama_produk) as nama_produk'),
                DB::raw('MAX(t_stok_item.nama_stok) as nama_stok'),
                DB::raw("NULL as nama_pelanggan"),
                DB::raw("GROUP_CONCAT(DISTINCT COALESCE(t_riwayat_stok.nama_pelanggan, t_riwayat_stok.keterangan) SEPARATOR ', ') as keterangan")
            )
            ->where('t_riwayat_stok.tanggal', '>=', $tgl_mulai)
            ->where('t_riwayat_stok.jenis', 'keluar')
            ->groupBy('t_riwayat_stok.tanggal', 't_riwayat_stok.id_produk', 't_riwayat_stok.id_stok', 't_riwayat_stok.jenis', 't_riwayat_stok.satuan')
            ->orderBy('t_riwayat_stok.tanggal', 'desc')
            ->get();

        $title = 'Laporan Stok Keluar';
        $pdf = \PDF::loadview('t_laporan_stok_pdf', compact('data', 'tgl_mulai', 'title'));
        return $pdf->download('laporan-stok-keluar.pdf');
    }

    public function getWeeklyStok(Request $request)
    {
        $bulan = $request->bulan; // Format: YYYY-MM
        
        $data = DB::table('t_riwayat_stok')
            ->select(
                DB::raw("FLOOR((DAY(tanggal)-1)/7)+1 as minggu"), 
                'jenis', 
                DB::raw('SUM(jumlah) as qty')
            )
            ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"), $bulan)
            ->groupBy('minggu', 'jenis')
            ->orderBy('minggu', 'asc')
            ->get();
            
        return response()->json($data);
    }
}

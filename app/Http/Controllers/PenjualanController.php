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
        $produk = ProdukModel::all();
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
                    'jumlah_barang' => array_sum($request->jumlah_barang),
                    'id_produk' => implode(',', $request->id_produk),
                    'total' => $request->total,
                    'metode_pembayaran' => $request->metode_pembayaran
                ];
                $this->PenjualanModel->addData($data);
            });
            return redirect()->route('pos')->with('pesan_sukses_trx', $request->id_penjualan);
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
        return view('t_struk', compact('trx', 'produk'));
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
                    'jumlah_barang' => array_sum($request->jumlah_barang),
                    'id_produk' => implode(',', $request->id_produk),
                    'total' => $request->total,
                ];
                $this->PenjualanModel->addData($data);
            });
            return redirect()->route('penjualan')->with('pesan_sukses', 'Data Berhasil di Tambahkan');
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
            'jumlah_barang' => is_array(Request()->jumlah_barang) ? array_sum(Request()->jumlah_barang) : Request()->jumlah_barang,
            'id_produk' => is_array(Request()->id_produk) ? implode(',', Request()->id_produk) : Request()->id_produk,
            'total' => Request()->total,
        ];

        $this->PenjualanModel->updateData($edit_penjualan, $data);
        return redirect()->route('penjualan')->with('pesan_sukses', 'Data Berhasil di Update');
    }

    public function delete($id_penjualan)
    {

        $this->PenjualanModel->deleteData($id_penjualan);
        return redirect()->route('penjualan')->with('pesan_hapus', 'Data Berhasil di Delete');

    }

    public function exportexcel()
    {
        return Excel::download(new PenjualanExport, 'data_penjualan.xlsx');
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


}

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
    public function __construct(){
        $this->PenjualanModel = new PenjualanModel();
    }

    public function index(){
        $data = [
            'penjualan'=> $this->PenjualanModel->allData(),
        ];
        return view('t_penjualan', $data);
    }
    

    public function add(){
        $menuList = \App\Models\MenuModel::all();
        return view('t_addpenjualan', compact('menuList'));
    }

    public function insert(Request $request){
        $request->validate([
            'id_penjualan' => 'required|unique:t_penjualan,id_penjualan|min:4|max:6',
            'tanggal' => 'required',    
            'nama_pelanggan' => 'required',         
            'jumlah_barang' => 'required|array', 
            'id_menu' =>  'required|array',
            'total' => 'required',  
        ],[
            'id_penjualan.required' => 'Silakan isi ID Penjualan.',
            'id_penjualan.unique' => 'ID Penjualan sudah ada.',
            'id_penjualan.min' => 'ID Penjualan minimal 4 karakter.',
            'id_penjualan.max' => 'ID Penjualan maximal 6 karakter.',
            'tanggal.required' => 'Silakan isi Tanggal.',
            'nama_pelanggan.required' => 'Silakan isi nama Pelanggan.',
            'jumlah_barang.required' => 'Silakan isi jumlah pesanan.',
            'id_menu.required' => 'Silakan pilih menu.',
            'total.required' => 'Silakan isi total harga.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Loop untuk setiap Menu yang dipesan
                foreach ($request->id_menu as $i => $id_menu) {
                    $menu = \App\Models\MenuModel::with('details')->where('id_menu', $id_menu)->firstOrFail();
                    $jumlah_beli_menu = $request->jumlah_barang[$i];
                    
                    // Reduce stock based on the recipe
                    foreach ($menu->details as $detail) {
                        $produk = ProdukModel::findOrFail($detail->id_produk);
                        // Multiply recipe requirement with the exact order amount
                        $total_dipakai = $detail->jumlah_dipakai * $jumlah_beli_menu;
                        
                        if ($produk->stok < $total_dipakai) {
                            throw new \Exception('Stok tidak mencukupi untuk item resep ' . $produk->nama_produk . ' pada menu ' . $menu->nama_menu);
                        }
                        // Eloquent save vs Query builder update: ProdukModel might only be a DB wrapper here so we use DB mapping
                        DB::table('t_produk')
                            ->where('id_produk', $produk->id_produk)
                            ->update(['stok' => $produk->stok - $total_dipakai]);
                    }
                }
                
                // Simpan data penjualan utama (saving id_menu as comma separated into id_produk field to reuse table structure)
                $data = [
                    'id_penjualan' => $request->id_penjualan,
                    'tanggal' => $request->tanggal,
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'jumlah_barang' => array_sum($request->jumlah_barang),
                    'id_produk' => implode(',', $request->id_menu), 
                    'total' => $request->total,
                ];
                $this->PenjualanModel->addData($data);
            });
            return redirect()->route('penjualan')->with('pesan_sukses','Data Berhasil di Tambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('pesan_error', $e->getMessage());
        }
    }

    public function edit($edit_penjualan){

        if (!$this->PenjualanModel->editData($edit_penjualan)) {
            abort(404);
        }
        $data = [
            'penjualan' => $this->PenjualanModel->editData($edit_penjualan),
       ];
        return view('t_editpenjualan', $data);
    }

    public function update($edit_penjualan){
        Request()->validate([
            'tanggal' => 'required',    
            'nama_pelanggan' => 'required',         
            'jumlah_barang' => 'required',
            'id_menu' =>  'required', 
            'total' => 'required',  
        ],[
            'tanggal.required' => 'Silakan isi Tanggal.',
            'nama_pelanggan.required' => 'Silakan isi nama Pelanggan.',
            'jumlah_barang.required' => 'Silakan isi jumlah barang.',
            'id_menu.required' => 'Silakan isi id menu.',
            'total.required' => 'Silakan isi total harga.',
        ]);

        $data = [
            'id_penjualan' => Request()->id_penjualan,
            'tanggal' => Request()->tanggal,
            'nama_pelanggan' => Request()->nama_pelanggan,
            'jumlah_barang' => is_array(Request()->jumlah_barang) ? array_sum(Request()->jumlah_barang) : Request()->jumlah_barang,
            'id_produk' => is_array(Request()->id_menu) ? implode(',', Request()->id_menu) : Request()->id_menu,
            'total' => Request()->total,
        ]; 

        $this->PenjualanModel->updateData($edit_penjualan, $data);
        return redirect()->route('penjualan')->with('pesan_sukses','Data Berhasil di Update');
    }

    public function delete($id_penjualan){

        $this->PenjualanModel->deleteData($id_penjualan);
        return redirect()->route('penjualan')->with('pesan_hapus','Data Berhasil di Delete');
        
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

    public function cetak_tgl_pdf($tglawal, $tglakhir){
        $cetakpertanggal = PenjualanModel::whereBetween('tanggal', [$tglawal, $tglakhir])->get();
        $pdf = PDF::loadview('cetak_pertanggal_pdf', compact('cetakpertanggal'));
        return $pdf->download('laporan-penjualan-pertanggal.pdf');
    }

 
}

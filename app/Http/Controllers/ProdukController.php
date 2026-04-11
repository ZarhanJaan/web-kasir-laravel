<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdukModel;

class ProdukController extends Controller
{
    public function __construct(){
        $this->ProdukModel = new ProdukModel();
    }

    public function index(){
        $data = [
            'produk'=> $this->ProdukModel->allData(),
        ];
        return view('t_produk', $data);
    }

    public function add(){
       
        return view('t_addproduk');
    }

    public function insert(){
        Request()->validate([
            'id_produk' => 'required|unique:t_produk,id_produk|min:4|max:6',
            'nama_produk' => 'required',    
            'stok' => 'required',         
            'harga_beli' => 'required', 
            'harga_jual' => 'required', 
            'satuan' => 'required', 

        ],[
            'id_produk.required' => 'Silakan isi ID Produk.',
            'id_produk.unique' => 'ID Produk sudah ada.',
            'id_produk.min' => 'ID Produk minimal 4 karakter.',
            'id_produk.max' => 'ID Produk maximal 6 karakter.',
            'nama_produk.required' => 'Silakan isi Nama Produk.',
            'stok.required' => 'Silakan isi Stok Produk',
            'harga_beli.required' => 'Silakan isi Harga Beli.',
            'harga_jual.required' => 'Silakan isi Harga Jual.',
            'satuan.required' => 'Silakan isi Satuan Produk.'
        ]);

        $data = [
            'id_produk' => Request()->id_produk,
            'nama_produk' => Request()->nama_produk,
            'stok' => Request()->stok,
            'harga_beli' => Request()->harga_beli,
            'harga_jual' => Request()->harga_jual,
            'satuan' => Request()->satuan,
        ]; 

        $this->ProdukModel->addData($data);
        return redirect()->route('produk')->with('pesan_sukses','Data Berhasil di Tambahkan');
    }

    public function edit($edit_produk){

        if (!$this->ProdukModel->editData($edit_produk)) {
            abort(404);
        }
        $data = [
            'produk' => $this->ProdukModel->editData($edit_produk),
       ];
        return view('t_editproduk', $data);
    }

    public function update($edit_produk){
        Request()->validate([
            'id_produk' => 'required|min:4|max:6',
            'nama_produk' => 'required',    
            'stok' => 'required',         
            'harga_beli' => 'required', 
            'harga_jual' => 'required', 
            'satuan' => 'required', 

        ],[
            'id_produk.required' => 'Silakan isi ID Produk.',
            'id_produk.unique' => 'ID Produk sudah ada.',
            'id_produk.min' => 'ID Produk minimal 4 karakter.',
            'id_produk.max' => 'ID Produk maximal 6 karakter.',
            'nama_produk.required' => 'Silakan isi Nama Produk.',
            'stok.required' => 'Silakan isi Stok Produk',
            'harga_beli.required' => 'Silakan isi Harga Beli.',
            'harga_jual.required' => 'Silakan isi Harga Jual.',
            'satuan.required' => 'Silakan isi Satuan Produk.'
        ]);

        $data = [
            'id_produk' => Request()->id_produk,
            'nama_produk' => Request()->nama_produk,
            'stok' => Request()->stok,
            'harga_beli' => Request()->harga_beli,
            'harga_jual' => Request()->harga_jual,
            'satuan' => Request()->satuan,
        ]; 

        $this->ProdukModel->updateData($edit_produk, $data);
        return redirect()->route('produk')->with('pesan_sukses','Data Berhasil di Update');
    }

    public function delete($id_produk){

        $this->ProdukModel->deleteData($id_produk);
        return redirect()->route('produk')->with('pesan_hapus','Data Berhasil di Delete');
        
    }

}

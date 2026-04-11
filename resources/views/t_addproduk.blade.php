
@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-table-large menu-icon"></i>
            </span> Add Produk
          </h3>
        </div>
        <br><br>
        {{-- add produk --}}
        <form action="/produk/insert" method="POST">
            @csrf
            <div class="row g-3">
            <div class="col-12 col-md-6">
                        <label for="">ID Produk</label>
                        <input name="id_produk" class="form-control" value="{{ old('id_produk') }}">
                        <div class="text-danger">
                              @error('id_produk')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Nama Produk</label>
                        <input name="nama_produk" class="form-control" value="{{ old('nama_produk') }}">
                        <div class="text-danger">
                              @error('nama_produk')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Stok Produk</label>
                        <input name="stok" class="form-control" value="{{ old('stok') }}">
                        <div class="text-danger">
                              @error('stok')
                                  {{ $message }}
                              @enderror
                        </div>
            </div>
            <div class="col-12 col-md-6">
                        <label for="">Harga Beli</label>
                        <input name="harga_beli" class="form-control" value="{{ old('harga_beli') }}">
                        <div class="text-danger">
                              @error('harga_beli')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Harga Jual</label>
                        <input name="harga_jual" class="form-control" value="{{ old('harga_jual') }}">
                        <div class="text-danger">
                              @error('harga_jual')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Satuan</label>
                        <input name="satuan" class="form-control" value="{{ old('satuan') }}">
                        <div class="text-danger">
                              @error('satuan')
                                  {{ $message }}
                              @enderror
                        </div>
                        
            </div>
            </div>
            <br>
            <div class="row">
                  <div class="col-12 col-md-6">
                        <button class="btr btn bg-gradient-info text-white">Simpan</button>
                  </div>
            </div>
        </form>
        
       
      </div>

      
    
@endsection

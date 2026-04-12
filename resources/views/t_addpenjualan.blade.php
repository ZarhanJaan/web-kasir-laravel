@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-table-large menu-icon"></i>
            </span> Tambah Transaksi
          </h3>
        </div>
        <br><br>
        {{-- add produk --}}
        <form action="/riwayat-transaksi/insert" method="POST">
            @csrf
            @if(session('pesan_sukses'))
                <div class="alert alert-success">{{ session('pesan_sukses') }}</div>
            @endif
            @if(session('pesan_error'))
                <div class="alert alert-danger">{{ session('pesan_error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif
             <div class="row g-3">
            <div class="col-12 col-md-6">
                        <label for="">ID Transaksi</label>
                        <input name="id_penjualan" class="form-control" value="{{ old('id_penjualan') }}">
                        <div class="text-danger">
                              @error('id_penjualan')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Tanggal</label>
                        <input name="tanggal" class="form-control" type="date" value="{{ old('tanggal') }}">
                        <div class="text-danger">
                              @error('tanggal')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Nama Pelanggan</label>
                        <input name="nama_pelanggan" class="form-control" value="{{ old('nama_pelanggan') }}">
                        <div class="text-danger">
                              @error('nama_pelanggan')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
            </div>
            <div class="col-12 col-md-6">
                        <label for="">Total</label>
                        <input name="total" class="form-control" value="{{ old('total') }}">
                        <div class="text-danger">
                              @error('total')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Daftar Produk & Jumlah</label>
                        <div id="produk-list">
                            <div class="row mb-2 produk-row">
                                <div class="col-7">
                                    <select name="id_produk[]" class="form-control">
                                        @foreach($produkList as $produk)
                                            <option value="{{ $produk->id_produk }}">{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <input type="number" name="jumlah_barang[]" class="form-control" placeholder="Jumlah">
                                </div>
                                <div class="col-1 d-flex align-items-center">
                                    <button type="button" class="btn bg-gradient-danger text-white btn-sm remove-produk">-</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn bg-gradient-success text-white btn-sm mt-2" id="add-produk">+ Tambah Produk</button>
                        <br>
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



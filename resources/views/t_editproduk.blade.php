
@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-table-large menu-icon"></i>
            </span> Edit Produk
          </h3>
        </div>
        <br><br>
        {{-- Edit produk --}}
        <form action="/produk/update/{{ $produk->id_produk }}" method="POST">
            @csrf
            <div class="row g-3">
            <div class="col-12 col-md-6">
                        <label for="">ID Produk</label>
                        <input name="id_produk" class="form-control" value="{{ $produk->id_produk }}" readonly>
                        <div class="text-danger">
                              @error('id_produk')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Nama Produk</label>
                        <input name="nama_produk" class="form-control" value="{{ $produk->nama_produk }}">
                        <div class="text-danger">
                              @error('nama_produk')
                                  {{ $message }}
                              @enderror
                        </div>
            </div>
                        <label for="">Harga Jual</label>
                        <input name="harga_jual" class="form-control" value="{{ $produk->harga_jual }}">
                        <div class="text-danger">
                              @error('harga_jual')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Kategori Produk</label>
                        <select name="kategori" class="form-control">
                              <option value="">-- Pilih Kategori --</option>
                              <option value="Makanan" {{ $produk->kategori == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                              <option value="Minuman" {{ $produk->kategori == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                              <option value="Sembako" {{ $produk->kategori == 'Sembako' ? 'selected' : '' }}>Sembako</option>
                              <option value="Bumbu" {{ $produk->kategori == 'Bumbu' ? 'selected' : '' }}>Bumbu</option>
                        </select>
                        <div class="text-danger">
                              @error('kategori')
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

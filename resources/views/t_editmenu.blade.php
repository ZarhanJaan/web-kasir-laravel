@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-book-open-page-variant menu-icon"></i>
            </span> Edit Menu
          </h3>
        </div>
        <br><br>
        @if (session('pesan_error'))
        <div class="alert alert-danger" role="alert">
          <i class="fa fa-warning"></i>
            {{session('pesan_error')}}
        </div>     
        @endif
        {{-- edit menu --}}
        <form action="/menu/update/{{ $menu->id_menu }}" method="POST">
            @csrf
            
            <h4 class="mb-3">Informasi Menu</h4>
            <div class="row g-3">
            <div class="col-12 col-md-6">
                        <label for="">ID Menu (Tidak bisa diubah)</label>
                        <input class="form-control" value="{{ $menu->id_menu }}" disabled>
                        <br>
                        <label for="">Nama Menu</label>
                        <input name="nama_menu" class="form-control" value="{{ $menu->nama_menu }}">
                        <div class="text-danger">
                              @error('nama_menu')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Harga Menu</label>
                        <input type="number" name="harga_menu" class="form-control" value="{{ $menu->harga_menu }}">
                        <div class="text-danger">
                              @error('harga_menu')
                                  {{ $message }}
                              @enderror
                        </div>
            </div>
            </div>

            <hr>
            <h4 class="mb-3">Resep / Produk yang Digunakan</h4>
            
            <div id="produk-list">
                  @foreach($menu->details as $detail)
                  <div class="row g-3 mb-3 produk-row">
                        <div class="col-12 col-md-6">
                              <label>Produk / Bahan</label>
                              <select name="id_produk[]" class="form-control">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produkList as $produk)
                                          <option value="{{ $produk->id_produk }}" {{ $detail->id_produk == $produk->id_produk ? 'selected' : '' }}>
                                              {{ $produk->id_produk }} - {{ $produk->nama_produk }} (Stok: {{ $produk->stok }})
                                          </option>
                                    @endforeach
                              </select>
                        </div>
                        <div class="col-10 col-md-4">
                              <label>Jumlah Dipakai</label>
                              <input type="number" name="jumlah_dipakai[]" class="form-control" value="{{ $detail->jumlah_dipakai }}" min="0">
                        </div>
                        <div class="col-2 col-md-2 d-flex align-items-end">
                              <button type="button" class="btn btn-danger remove-produk"><i class="fa fa-trash-o"></i></button>
                        </div>
                  </div>
                  @endforeach
                  
                  {{-- Tampilkan baris kosong setidaknya 1 jika tidak ada detail --}}
                  @if($menu->details->count() == 0)
                  <div class="row g-3 mb-3 produk-row">
                        <div class="col-12 col-md-6">
                              <label>Produk / Bahan</label>
                              <select name="id_produk[]" class="form-control">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produkList as $produk)
                                          <option value="{{ $produk->id_produk }}">{{ $produk->id_produk }} - {{ $produk->nama_produk }} (Stok: {{ $produk->stok }})</option>
                                    @endforeach
                              </select>
                        </div>
                        <div class="col-10 col-md-4">
                              <label>Jumlah Dipakai</label>
                              <input type="number" name="jumlah_dipakai[]" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-2 col-md-2 d-flex align-items-end">
                              <button type="button" class="btn btn-danger remove-produk"><i class="fa fa-trash-o"></i></button>
                        </div>
                  </div>
                  @endif
            </div>
            
            <button type="button" id="add-produk" class="btn btn-success btn-sm mb-4">Tambah Produk +</button>

            <br>
            <div class="row">
                  <div class="col-12 col-md-6">
                        <button class="btr btn bg-gradient-info text-white">Update Menu</button>
                        <a href="/menu" class="btn btn-outline-secondary ms-2">Batal</a>
                  </div>
            </div>
        </form>
        
       
      </div>
    
@endsection

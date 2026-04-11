@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-pencil menu-icon"></i>
            </span> Edit Stok Keluar
          </h3>
        </div>
        <br>
        
        @if (session('pesan_error'))
        <div class="alert alert-danger" role="alert">
            <i class="fa fa-exclamation-triangle"></i>
            {{ session('pesan_error') }}
        </div>
        @endif

        <div class="card shadow p-4 mb-5 bg-body rounded">
        <form action="/stok/keluar/update/{{ $riwayat->id_riwayat }}" method="POST">
            @csrf
            
            <div class="alert alert-warning mb-4">
                <strong><i class="mdi mdi-information-outline"></i> Info:</strong> Mengubah jumlah stok di sini akan otomatis mensinkronkan ulang kuantitas pada gudang produk asli (t_produk). Produk terkait saat ini adalah: <strong>{{ $riwayat->nama_produk }}</strong>
            </div>

            <div class="row g-3">
            <div class="col-12 col-md-6">
                        <label for="">Nama Produk (Dikunci)</label>
                        <input type="text" class="form-control" value="{{ $riwayat->nama_produk }}" readonly disabled>
                        <br>
                        
                        <label for="">Nama Pelanggan / Kasir</label>
                        <input type="text" name="nama_pelanggan" class="form-control" value="{{ old('nama_pelanggan', $riwayat->nama_pelanggan) }}">
                        <div class="text-danger">
                              @error('nama_pelanggan') {{ $message }} @enderror
                        </div>
                        <br>

                        <label for="">Jumlah Keluar (Pcs/Unit)</label>
                        <input type="number" name="jumlah" class="form-control" value="{{ old('jumlah', $riwayat->jumlah) }}" min="1" required>
                        <div class="text-danger">
                              @error('jumlah') {{ $message }} @enderror
                        </div>
            </div>
            
            <div class="col-12 col-md-6">
                        <label for="">Tanggal Transaksi</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $riwayat->tanggal) }}" required>
                        <div class="text-danger">
                              @error('tanggal') {{ $message }} @enderror
                        </div>
                        <br>

                        <label for="">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="4" placeholder="Misal: Retur, Pembelian Baru, Rusak">{{ old('keterangan', $riwayat->keterangan) }}</textarea>
                        <div class="text-danger">
                              @error('keterangan') {{ $message }} @enderror
                        </div>
                        <br>
            </div>
            </div>
            <br>
            <div class="row">
                  <div class="col-12 col-md-6">
                        <button type="submit" class="btr btn btn-gradient-primary text-white"><i class="mdi mdi-content-save"></i> Simpan Perubahan</button>
                        <a href="/stok/keluar" class="btn btn-secondary ms-2">Batal</a>
                  </div>
            </div>
        </form>
        </div>
      </div>
@endsection

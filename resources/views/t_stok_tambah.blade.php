@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-import menu-icon"></i>
            </span> Stok Masuk
          </h3>
        </div>

        @if (session('pesan_error'))
        <div class="alert alert-danger" role="alert">
            <i class="mdi mdi-alert-circle"></i>
            {{ session('pesan_error') }}
        </div>
        @endif

        <div class="row">
          <div class="col-12 grid-margin">
            <div class="card shadow-sm border-0 bg-body rounded">
              <div class="card-body p-lg-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0 text-primary"><i class="mdi mdi-plus-box"></i> Form Tambah Stok Bahan</h4>
                    <span class="badge bg-gradient-info text-white">Input Manual</span>
                </div>
                <p class="card-description text-muted mb-5">Gunakan formulir ini untuk mencatat barang atau bahan baku yang masuk ke gudang.</p>

                <form action="/stok/insert" method="POST" class="forms-sample">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_riwayat" class="font-weight-bold">ID Riwayat <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-gradient-primary text-white"><i class="mdi mdi-numeric"></i></span>
                                    </div>
                                    <input type="number" name="id_riwayat" id="id_riwayat" class="form-control" placeholder="Contoh: 5001" value="{{ old('id_riwayat') }}" required>
                                </div>
                                <small class="text-muted">Masukkan ID transaksi riwayat secara manual.</small>
                            </div>

                            <div class="form-group mt-4">
                                <label for="id_produk" class="font-weight-bold">Pilih Bahan Baku <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-gradient-primary text-white"><i class="mdi mdi-package-variant"></i></span>
                                    </div>
                                    <input list="bahan_list" name="id_produk" id="id_produk" class="form-control" placeholder="Ketik Nama atau ID Bahan..." required>
                                    <datalist id="bahan_list">
                                        @foreach($produk as $p)
                                            <option value="{{ $p->id_stok }}">{{ $p->nama_stok }} (ID: {{ $p->id_stok }})</option>
                                        @endforeach
                                    </datalist>
                                </div>
                                @error('id_produk') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="card bg-light border-0 mt-3 mb-4 shadow-sm">
                                <div class="card-body p-3">
                                    <h6 class="text-primary font-weight-bold mb-2"><i class="mdi mdi-plus-circle"></i> Registrasi Bahan Baru (Opsional)</h6>
                                    <p class="text-muted extra-small mb-3">Jika bahan belum terdaftar, isi ID di bawah ini untuk mendaftarkannya otomatis.</p>
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold">ID Bahan Baru</label>
                                        <input name="id_stok_baru" class="form-control form-control-sm border-primary" placeholder="Contoh: 9001" value="{{ old('id_stok_baru') }}">
                                        @error('id_stok_baru') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal" class="font-weight-bold">Tanggal Transaksi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-gradient-primary text-white"><i class="mdi mdi-calendar"></i></span>
                                    </div>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="jumlah" class="font-weight-bold">Jumlah (Unit) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="jumlah" id="jumlah" class="form-control text-center" value="{{ old('jumlah') }}" min="1" required placeholder="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text">Unit</span>
                                            </div>
                                        </div>
                                        @error('jumlah') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="harga_beli" class="font-weight-bold">Harga Beli <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="number" name="harga_beli" id="harga_beli" class="form-control" value="{{ old('harga_beli') }}" min="0" required placeholder="0">
                                        </div>
                                        @error('harga_beli') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="keterangan" class="font-weight-bold">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="4" placeholder="Contoh: Stok Baru, Retur, dll.">{{ old('keterangan') }}</textarea>
                                <small class="text-muted">Jika kosong, otomatis terisi "Stok Masuk".</small>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="jenis" value="masuk">

                    <div class="mt-5 d-flex justify-content-end">
                        <a href="{{ route('stok.dashboard') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-gradient-primary btn-icon-text">
                            <i class="mdi mdi-file-check btn-icon-prepend"></i> Simpan Stok Masuk
                        </button>
                    </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
@endsection

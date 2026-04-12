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
        <br>
        @if (session('pesan_error'))
        <div class="alert alert-danger" role="alert">
            <i class="fa fa-exclamation-triangle"></i>
            {{ session('pesan_error') }}
        </div>
        @endif

        <div class="card shadow p-4 mb-5 bg-body rounded">
        <form action="/stok/insert" method="POST">
            @csrf
            <div class="row g-3">
                    <div class="col-12">
                        <label for="">ID Riwayat (Manual)</label>
                        <input type="number" name="id_riwayat" class="form-control" placeholder="Contoh: 5001" required>
                        <br>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="">Pilih Bahan Baku (Ketik ID atau Nama)</label>
                        <input list="bahan_list" name="id_produk" class="form-control" placeholder="Contoh: Minyak Goreng" required>
                        <datalist id="bahan_list">
                            @foreach($produk as $p)
                                <option value="{{ $p->id_stok }}">{{ $p->nama_stok }} (ID: {{ $p->id_stok }})</option>
                            @endforeach
                        </datalist>
                        <div class="text-danger small">@error('id_produk') {{ $message }} @enderror</div>
                        <br>

                        <div class="card bg-light border-dashed p-3 mb-3">
                            <h6 class="text-muted"><i class="mdi mdi-plus-circle-outline"></i> Informasi Bahan Baru (Opsional)</h6>
                            <p class="text-muted extra-small mb-2">Hanya isi jika Anda mengetik nama bahan baru yang belum terdaftar.</p>
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="small font-weight-bold">ID Bahan Baru</label>
                                    <input name="id_stok_baru" class="form-control form-control-sm" placeholder="Contoh: 9001">
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="jenis" value="masuk">

                        <label for="">Jumlah (Pcs/Unit)</label>
                        <input type="number" name="jumlah" class="form-control" value="{{ old('jumlah') }}" min="1" required>
                        <div class="text-danger">
                              @error('jumlah') {{ $message }} @enderror
                        </div>
                        <br>

                        <label for="">Harga Beli (Per Item)</label>
                        <input type="number" name="harga_beli" class="form-control" value="{{ old('harga_beli') }}" min="0" required>
                        <div class="text-danger">
                                @error('harga_beli') {{ $message }} @enderror
                        </div>
            </div>
            
            <div class="col-12 col-md-6">
                        <label for="">Tanggal Transaksi</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        <div class="text-danger">
                              @error('tanggal') {{ $message }} @enderror
                        </div>
                        <br>

                        <label for="">Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control" rows="4" placeholder="Misal: Retur, Pembelian Baru, dll. Jika dikosongkan otomatis terisi 'Stok Masuk'">{{ old('keterangan') }}</textarea>
                        <div class="text-danger">
                              @error('keterangan') {{ $message }} @enderror
                        </div>
                        <br>
            </div>
            </div>
            <br>
            <div class="row">
                  <div class="col-12 col-md-6">
                        <button type="submit" class="btr btn bg-gradient-info text-white">Simpan Stok</button>
                  </div>
            </div>
        </form>
        </div>
      </div>
@endsection

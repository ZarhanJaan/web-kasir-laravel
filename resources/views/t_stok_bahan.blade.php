@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/pages.css') }}">

<div class="content-wrapper">

  {{-- Page Header --}}
  <div class="page-header">
    <h3 class="page-title">
      <span class="page-title-icon bg-gradient-primary text-white me-2">
        <i class="mdi mdi-package-variant menu-icon"></i>
      </span> Manajemen Stok
    </h3>
    <nav aria-label="breadcrumb">
      <button type="button" class="btn-glass-primary" data-bs-toggle="modal" data-bs-target="#addBahanModal">
        <i class="mdi mdi-plus"></i> Bahan Baru
      </button>
    </nav>
  </div>

  {{-- Flash Messages --}}
  @if(session('pesan_sukses'))
    <div class="page-alert-success">
      <strong><i class="mdi mdi-check-circle me-1"></i>Berhasil!</strong> {{ session('pesan_sukses') }}
    </div>
  @endif

  {{-- Table Card --}}
  <div class="row">
    <div class="col-12 grid-margin">
      <div class="page-card">
        <div class="bg-circle"></div>
        <div class="card-body">

          <div class="page-card-header">
            <div class="page-card-title">
              <div class="title-icon" style="background: linear-gradient(135deg, #fcc419, #e67700); box-shadow: 0 4px 14px rgba(252,196,25,0.3);">
                <i class="mdi mdi-format-list-bulleted"></i>
              </div>
              <div>
                <h4>Daftar Stok Bahan Baku</h4>
                <p>Kelola inventaris bahan baku toko</p>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="page-table" id="mytable">
              <thead>
                <tr>
                  <th>ID Bahan</th>
                  <th>Nama Bahan</th>
                  <th>Stok Saat Ini</th>
                  <th>Satuan</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($bahan as $data)
                  <tr>
                    <td><span class="badge-glass badge-glass-primary">{{ $data->id_stok }}</span></td>
                    <td class="text-primary-val">{{ $data->nama_stok }}</td>
                    <td style="font-weight: 700; color: {{ $data->stok < 10 ? 'var(--error-color)' : 'var(--success-color)' }}">
                      {{ $data->stok }}
                    </td>
                    <td style="color: var(--text-muted);">{{ $data->satuan }}</td>
                    <td>
                      @if($data->stok < 10)
                        <span class="stok-badge-low"><i class="mdi mdi-alert-circle"></i> Menipis</span>
                      @else
                        <span class="stok-badge-ok"><i class="mdi mdi-check-circle"></i> Aman</span>
                      @endif
                    </td>
                    <td>
                      <a href="/stok/bahan/delete/{{ $data->id_stok }}"
                         class="btn-act btn-act-delete"
                         onclick="return confirm('Apakah Anda yakin ingin menghapus bahan ini? Ini mungkin merusak resep menu yang sudah ada.')">
                        <i class="mdi mdi-delete"></i> Hapus
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>

</div>

{{-- Modal Tambah Bahan --}}
<div class="modal fade" id="addBahanModal" tabindex="-1" aria-labelledby="addBahanModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="/stok/bahan/insert" method="POST">
        @csrf
        <div class="modal-header modal-header-primary">
          <h5 class="modal-title" id="addBahanModalLabel">
            <i class="mdi mdi-package-variant-plus me-1"></i> Tambah Bahan Baku Baru
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label">ID Bahan (Manual)</label>
            <input type="number" name="id_stok" class="form-control" placeholder="Contoh: 101" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Nama Bahan</label>
            <input type="text" name="nama_stok" class="form-control"
              placeholder="Contoh: Minyak Goreng, Beras, Telur" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Satuan</label>
            <select name="satuan" class="form-control" required>
              <option value="Pcs">Pcs / Biji</option>
              <option value="Bungkus">Bungkus</option>
              <option value="Unit">Unit</option>
              <option value="Kg">Kg (Kilogram)</option>
              <option value="Gram">Gram</option>
              <option value="Liter">Liter</option>
              <option value="Box">Box / Kardus</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Stok Awal (Opsional)</label>
            <input type="number" name="stok" class="form-control" value="0">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn-act btn-act-view" data-bs-dismiss="modal" style="padding: 10px 18px;">
            <i class="mdi mdi-close"></i> Batal
          </button>
          <button type="submit" class="btn-glass-primary">
            <i class="mdi mdi-content-save"></i> Simpan Bahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
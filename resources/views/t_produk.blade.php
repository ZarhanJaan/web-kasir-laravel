@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/pages.css') }}">

<div class="content-wrapper">

  {{-- Page Header --}}
  <div class="page-header">
    <h3 class="page-title">
      <span class="page-title-icon bg-gradient-primary text-white me-2">
        <i class="mdi mdi-silverware menu-icon"></i>
      </span> Menu
    </h3>
    @if(Auth::user()->hasAnyRole(['owner', 'admin']))
      <nav aria-label="breadcrumb">
        <a href="/stok" class="btn-glass-info">
          <i class="mdi mdi-package-variant"></i> Daftar Stok
        </a>
      </nav>
    @endif
  </div>

  {{-- Flash Messages --}}
  @if(session('pesan_sukses'))
    <div class="page-alert-success">
      <strong><i class="mdi mdi-check-circle me-1"></i>Berhasil!</strong> {{ session('pesan_sukses') }}
    </div>
  @endif

  @if(session('pesan_hapus'))
    <div class="page-alert-danger">
      <strong><i class="mdi mdi-delete me-1"></i>Dihapus!</strong> {{ session('pesan_hapus') }}
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
              <div class="title-icon">
                <i class="mdi mdi-silverware"></i>
              </div>
              <div>
                <h4>Daftar Menu</h4>
                <p>Semua produk yang tersedia di toko</p>
              </div>
            </div>
            @if(Auth::user()->hasAnyRole(['owner', 'admin']))
              <a href="/resep/add" class="btn-glass-primary">
                <i class="mdi mdi-plus"></i> Tambah Menu
              </a>
            @endif
          </div>

          <div class="table-responsive">
            <table class="page-table" id="mytable">
              <thead>
                <tr>
                  <th>ID Menu</th>
                  <th>Nama Menu</th>
                  <th>Harga Jual</th>
                  <th>Kategori</th>
                  @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                    <th>Aksi</th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @foreach ($produk as $data)
                  <tr>
                    <td><span class="badge-glass badge-glass-primary">{{ $data->id_produk }}</span></td>
                    <td class="text-primary-val">{{ $data->nama_produk }}</td>
                    <td style="color: var(--success-color); font-weight: 600;">
                      Rp {{ number_format($data->harga_jual, 0, ',', '.') }}
                    </td>
                    <td>
                      <span class="badge-glass {{ $data->kategori == 'Makanan' ? 'badge-glass-info' : 'badge-glass-success' }}">
                        {{ $data->kategori }}
                      </span>
                    </td>
                    @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                      <td>
                        <a href="/menu/edit/{{ $data->id_produk }}" class="btn-act btn-act-edit">
                          <i class="mdi mdi-border-color"></i> Edit
                        </a>
                        <a href="/menu/delete/{{ $data->id_produk }}"
                           class="btn-act btn-act-delete ms-1"
                           onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">
                          <i class="mdi mdi-delete"></i> Hapus
                        </a>
                      </td>
                    @endif
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
@endsection
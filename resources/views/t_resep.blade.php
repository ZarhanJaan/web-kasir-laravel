@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/pages.css') }}">

<div class="content-wrapper">

  {{-- Page Header --}}
  <div class="page-header">
    <h3 class="page-title">
      <span class="page-title-icon bg-gradient-primary text-white me-2">
        <i class="mdi mdi-book-open-page-variant menu-icon"></i>
      </span> Resep Menu
    </h3>
    <nav aria-label="breadcrumb">
      <a href="/resep/add" class="btn-glass-primary">
        <i class="mdi mdi-plus"></i> Tambah Menu
      </a>
    </nav>
  </div>

  {{-- Flash Messages --}}
  @if(session('pesan_sukses'))
    <div class="page-alert-success">
      <strong><i class="mdi mdi-check-circle me-1"></i>Berhasil!</strong> {{ session('pesan_sukses') }}
    </div>
  @endif

  {{-- Recipe Cards Grid --}}
  <div class="row">
    @forelse ($menus as $menu)
      <div class="col-md-6 grid-margin stretch-card">
        <div class="resep-card">
          <div class="card-body">

            {{-- Card Header --}}
            <div class="resep-header">
              <div class="resep-title">
                <div class="resep-icon">
                  <i class="mdi mdi-silverware"></i>
                </div>
                <div>
                  <div class="resep-name">{{ $menu->nama_produk }}</div>
                  <div class="resep-meta">
                    ID: {{ $menu->id_produk }}
                    &nbsp;&bull;&nbsp;
                    <span class="badge-glass badge-glass-info" style="font-size: 11px; padding: 2px 8px;">{{ $menu->kategori }}</span>
                  </div>
                </div>
              </div>
              <div class="d-flex gap-2">
                <a href="/resep/edit/{{ $menu->id_produk }}" class="btn-act btn-act-edit">
                  <i class="mdi mdi-pencil"></i> Edit
                </a>
                <a href="/menu/delete/{{ $menu->id_produk }}"
                   class="btn-act btn-act-delete"
                   onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini? Semua resep terkait akan hilang.')">
                  <i class="mdi mdi-delete"></i>
                </a>
              </div>
            </div>

            <hr>

            <h6>Bahan yang digunakan:</h6>

            @if(count($menu->resep) > 0)
              @foreach($menu->resep as $item)
                <div class="resep-item">
                  <span class="nama">{{ $item->nama_stok }}</span>
                  <span class="qty">{{ $item->jumlah }}</span>
                </div>
              @endforeach
            @else
              <div class="resep-empty">
                <i class="mdi mdi-alert-circle me-1"></i>
                Resep belum diatur. Menu ini tidak akan mengurangi stok saat terjual.
              </div>
            @endif

          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="page-card">
          <div class="card-body text-center" style="padding: 60px !important;">
            <i class="mdi mdi-book-open-page-variant" style="font-size: 48px; color: var(--text-muted); display:block; margin-bottom: 16px;"></i>
            <p style="color: var(--text-muted); font-size: 15px;">Belum ada menu yang terdaftar.</p>
            <a href="/resep/add" class="btn-glass-primary mt-2">
              <i class="mdi mdi-plus"></i> Tambah Menu Pertama
            </a>
          </div>
        </div>
      </div>
    @endforelse
  </div>

</div>
@endsection

@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/resep.css') }}">

  <div class="content-wrapper">
    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
          <i class="mdi mdi-book-open-page-variant menu-icon"></i>
        </span> Manajemen Resep Menu
      </h3>
      <nav aria-label="breadcrumb">
        <a href="/resep/add" class="resep-btn-header">
          <i class="mdi mdi-plus"></i> Add Menu
        </a>
      </nav>
    </div>

    @if (session('pesan_sukses'))
      <div class="resep-alert-success">
        <i class="mdi mdi-check-circle"></i> {{ session('pesan_sukses') }}
      </div>
    @endif

    <div class="row">
      @foreach ($menus as $menu)
        <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-duration="600" data-aos-delay="{{ $loop->index * 100 }}">
          <div class="resep-card">
            <div class="card-body">
              <div class="resep-card-header">
                <h4 class="resep-card-title">
                  <i class="mdi mdi-silverware"></i> {{ $menu->nama_produk }}
                  @if(count($menu->resep) > 0 && isset($menu->resep[0]->created_at))
                    <span style="font-size: 0.7em; color: #888;"> (Dibuat: {{ \Carbon\Carbon::parse($menu->resep[0]->created_at)->format('d/m/Y') }})</span>
                  @endif
                </h4>
                <div class="d-flex gap-2">
                  <a href="/resep/edit/{{ $menu->id_produk }}" class="resep-btn resep-btn-edit">
                    <i class="mdi mdi-pencil"></i> Edit Resep
                  </a>
                  <a href="/menu/delete/{{ $menu->id_produk }}" class="resep-btn resep-btn-delete"
                    onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini? Semua resep terkait akan hilang.')">
                    <i class="mdi mdi-delete"></i>
                  </a>
                </div>
              </div>
              <p class="resep-card-meta">ID Menu: {{ $menu->id_produk }} | Kategori: {{ $menu->kategori }}</p>
              <hr class="resep-divider">
              <h6 class="resep-section-title">Bahan yang digunakan</h6>
              @if(count($menu->resep) > 0)
                <ul class="resep-ingredient-list">
                  @foreach($menu->resep as $item)
                    <li class="resep-ingredient-item">
                      <span>{{ $item->nama_stok }}</span>
                      <span class="resep-ingredient-badge">{{ $item->jumlah }}</span>
                    </li>
                  @endforeach
                </ul>
              @else
                <p class="resep-empty-state">Resep belum diatur. Menu ini tidak akan mengurangi stok saat terjual.</p>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

@endsection
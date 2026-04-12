@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-book-open-page-variant menu-icon"></i>
            </span> Manajemen Resep Menu
          </h3>
          <nav aria-label="breadcrumb">
            <a href="/produk/add" class="btn btn-gradient-primary text-white font-weight-bold shadow-sm">
                  <i class="mdi mdi-plus"></i> Add Menu
            </a>
          </nav>
        </div>

        @if (session('pesan_sukses'))
            <div class="alert alert-success">
                  <i class="fa fa-check-circle"></i> {{ session('pesan_sukses') }}
            </div>
        @endif

        <div class="row">
          @foreach ($menus as $menu)
          <div class="col-md-6 grid-margin stretch-card">
            <div class="card shadow rounded">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title text-success mb-0"><i class="mdi mdi-silverware"></i> {{ $menu->nama_produk }}</h4>
                    <div class="btn-group">
                        <a href="/resep/edit/{{ $menu->id_produk }}" class="btn btn-sm btn-inverse-primary">Edit Resep</a>
                        <a href="/produk/delete/{{ $menu->id_produk }}" class="btn btn-sm btn-inverse-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini? Semua resep terkait akan hilang.')"><i class="mdi mdi-delete"></i></a>
                    </div>
                </div>
                <p class="text-muted small">ID Menu: {{ $menu->id_produk }} | Kategori: {{ $menu->kategori }}</p>
                <hr>
                <h6 class="mb-2">Bahan yang digunakan:</h6>
                @if(count($menu->resep) > 0)
                  <ul class="list-group list-group-flush">
                    @foreach($menu->resep as $item)
                      <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0">
                        {{ $item->nama_stok }}
                        <span class="badge bg-light text-dark">{{ $item->jumlah }}</span>
                      </li>
                    @endforeach
                  </ul>
                @else
                  <p class="text-danger italic">Resep belum diatur. Menu ini tidak akan mengurangi stok saat terjual.</p>
                @endif
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

@endsection

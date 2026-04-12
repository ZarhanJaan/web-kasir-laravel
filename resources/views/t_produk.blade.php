@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-silverware menu-icon"></i>
            </span> Menu
          </h3>
          <nav aria-label="breadcrumb">
            <div class="btn-group">
                <a href="/stok/bahan" class="btn btn-gradient-info text-white shadow-sm ms-2"><i class="mdi mdi-package-variant"></i> Daftar Bahan</a>
            </div>
          </nav>
        </div>
         @if (session('pesan_sukses'))
            <div class="alert alert-success">
                  <i class="fa fa-check-circle"></i> {{ session('pesan_sukses') }}
            </div>
      @endif

      @if (session('pesan_hapus'))
            <div class="alert alert-danger">
                  <i class="fa fa-trash"></i> {{ session('pesan_hapus') }}
            </div>
      @endif


        <div class="row">
          <div class="col-12 grid-margin">
            <div class="card shadow p-3 mb-5 bg-body rounded">
              <div class="card-body">
                <h4 class="card-title text-primary"><i class="mdi mdi-silverware"></i> Daftar Menu</h4>
                <div class="table-responsive">
                  <table class="table table-bordered" id="mytable">
                        <thead>
                              <tr>
                                    <th>ID Menu</th>
                                    <th>Nama Menu</th>
                                    <th>Harga Jual</th>
                                    <th>Kategori Menu</th>
                                    <th>Action</th>
                              </tr>
                        </thead>
                        <tbody>
                        @foreach ($produk as $data)
                              <tr>
                                    <td>{{ $data->id_produk }}</td>
                                    <td>{{ $data->nama_produk }}</td>
                                    <td>Rp.{{ number_format($data->harga_jual, 0, ',', '.') }}</td>
                                    <td>
                                          <span class="badge {{ $data->kategori == 'Makanan' ? 'bg-gradient-info' : 'bg-gradient-success' }}">
                                                {{ $data->kategori }}
                                          </span>
                                    </td>
                                    <td>
                                          <a href="/produk/edit/{{ $data->id_produk }}" class="btn btn-sm btn-warning mb-1"><i class="mdi mdi-border-color"></i></a>
                                          <a href="/produk/delete/{{ $data->id_produk }}" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')"><i class="mdi mdi-delete"></i></a>
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
@endsection

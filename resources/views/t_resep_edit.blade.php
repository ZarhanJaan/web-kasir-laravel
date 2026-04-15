@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-success text-white me-2">
                  <i class="mdi mdi-book-open-page-variant menu-icon"></i>
            </span> Edit Resep: {{ $menu->nama_produk }}
          </h3>
          <nav aria-label="breadcrumb">
            <a href="/resep" class="btn btn-sm btn-gradient-secondary text-white">
                   <i class="mdi mdi-arrow-left"></i> Kembali
            </a>
          </nav>
        </div>

         @if (session('pesan_sukses'))
            <div class="alert alert-success" role="alert">
                  <i class="mdi mdi-check-circle"></i> {{ session('pesan_sukses') }}
            </div>
      @endif

      @if (session('pesan_error'))
            <div class="alert alert-danger" role="alert">
                  <i class="mdi mdi-alert-circle"></i> {{ session('pesan_error') }}
            </div>
      @endif

        <div class="row">
          <div class="col-md-7 grid-margin stretch-card">
            <div class="card shadow-sm border-0 bg-body rounded">
              <div class="card-body p-4">
                <h4 class="card-title text-primary mb-4"><i class="mdi mdi-format-list-bulleted"></i> Komponen Bahan Baku Saat Ini</h4>
                <p class="text-muted small mb-4">Daftar bahan yang sudah terdaftar dalam resep menu ini.</p>
                
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                        <thead class="bg-light">
                              <tr>
                                    <th>Nama Bahan Baku</th>
                                    <th class="text-center">Jumlah / Unit</th>
                                    <th class="text-center">Aksi</th>
                              </tr>
                        </thead>
                        <tbody>
                        @foreach ($resep as $item)
                              <tr>
                                    <td><b class="text-dark">{{ $item->nama_stok }}</b> <small class="text-muted">(ID: {{ $item->id_stok }})</small></td>
                                    <td class="text-center"><span class="badge bg-light text-dark border">{{ $item->jumlah }} Unit</span></td>
                                    <td class="text-center">
                                          <a href="/resep/item/delete/{{ $item->id_resep }}" class="btn btn-outline-danger btn-sm p-2" onclick="return confirm('Hapus bahan ini dari resep?')">
                                              <i class="mdi mdi-delete-forever"></i>
                                          </a>
                                    </td>
                              </tr>
                        @endforeach
                        @if($resep->isEmpty())
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-flask-empty-outline mdi-48px d-block mb-2"></i>
                                    Belum ada bahan dalam resep ini.
                                </td>
                            </tr>
                        @endif
                        </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-5 grid-margin stretch-card">
            <div class="card shadow-sm border-0 bg-body rounded">
              <div class="card-body p-4">
                <div class="bg-light p-3 rounded mb-4">
                    <h4 class="card-title mb-0"><i class="mdi mdi-plus-circle text-success"></i> Tambah Komponen</h4>
                </div>
                <form action="/resep/item/add" method="POST" class="forms-sample">
                    @csrf
                    <input type="hidden" name="id_menu" value="{{ $menu->id_produk }}">
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">ID Resep (Manual) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-gradient-success text-white"><i class="mdi mdi-numeric"></i></span>
                            </div>
                            <input type="number" name="id_resep" class="form-control" placeholder="Contoh: 1001" required>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Pilih Bahan Baku <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-gradient-success text-white"><i class="mdi mdi-package-variant"></i></span>
                            </div>
                            <select name="id_stok" class="form-control border-success" required>
                                <option value="">-- Pilih Bahan --</option>
                                @foreach($stok_items as $s)
                                    <option value="{{ $s->id_stok }}">{{ $s->nama_stok }} (Tersedia: {{ $s->stok }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Jumlah Pemakaian <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="jumlah" class="form-control text-center" value="1" min="1" required>
                            <div class="input-group-append">
                                <span class="input-group-text bg-light">Unit</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gradient-success btn-icon-text w-100 mt-2">
                        <i class="mdi mdi-plus-box btn-icon-prepend"></i> Masukkan ke Resep
                    </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

@endsection

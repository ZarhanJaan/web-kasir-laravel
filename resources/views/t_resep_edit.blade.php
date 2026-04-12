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
            <a href="/resep" class="btn bg-gradient-secondary text-white">
                   Kembali
            </a>
          </nav>
        </div>

         @if (session('pesan_sukses'))
            <div class="alert alert-success">
                  <i class="fa fa-check-circle"></i> {{ session('pesan_sukses') }}
            </div>
      @endif

      @if (session('pesan_error'))
            <div class="alert alert-danger">
                  <i class="fa fa-exclamation-triangle"></i> {{ session('pesan_error') }}
            </div>
      @endif

        <div class="row">
          <div class="col-md-7 grid-margin stretch-card">
            <div class="card shadow">
              <div class="card-body">
                <h4 class="card-title text-primary"><i class="mdi mdi-format-list-bulleted"></i> Komponen Bahan</h4>
                <div class="table-responsive">
                  <table class="table table-hover">
                        <thead>
                              <tr>
                                    <th>Nama Bahan</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Hapus</th>
                              </tr>
                        </thead>
                        <tbody>
                        @foreach ($resep as $item)
                              <tr>
                                    <td><b>{{ $item->nama_stok }}</b></td>
                                    <td>{{ $item->jumlah }}</td>
                                    <td>{{ $item->satuan }}</td>
                                    <td>
                                          <a href="/resep/item/delete/{{ $item->id_resep }}" class="text-danger" onclick="return confirm('Hapus bahan ini dari resep?')"><i class="mdi mdi-delete-forever mdi-24px"></i></a>
                                    </td>
                              </tr>
                        @endforeach
                        @if($resep->isEmpty())
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada bahan dalam resep ini.</td>
                            </tr>
                        @endif
                        </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-5 grid-margin stretch-card">
            <div class="card shadow bg-light">
              <div class="card-body">
                <h4 class="card-title"><i class="mdi mdi-plus-circle"></i> Tambah Bahan ke Resep</h4>
                <form action="/resep/item/add" method="POST">
                    @csrf
                    <input type="hidden" name="id_menu" value="{{ $menu->id_produk }}">
                    
                    <div class="mb-3">
                        <label class="form-label">ID Resep (Manual)</label>
                        <input type="number" name="id_resep" class="form-control" placeholder="Contoh: 1001" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Bahan Baku</label>
                        <select name="id_stok" class="form-control" required>
                            <option value="">-- Pilih Bahan --</option>
                            @foreach($stok_items as $s)
                                <option value="{{ $s->id_stok }}">{{ $s->nama_stok }} (ID: {{ $s->id_stok }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Pemakaian</label>
                        <input type="number" name="jumlah" class="form-control" value="1" min="1" required>
                    </div>

                    <button type="submit" class="btn btn-success btn-block w-100">Tambah ke Resep</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

@endsection

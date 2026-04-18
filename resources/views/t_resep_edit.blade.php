@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/resep.css') }}">

  <div class="content-wrapper">
    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-success text-white me-2">
          <i class="mdi mdi-book-open-page-variant menu-icon"></i>
        </span> Edit Resep: {{ $menu->nama_produk }}
      </h3>
      <nav aria-label="breadcrumb">
        <a href="/resep" class="resep-btn-back">
          <i class="mdi mdi-arrow-left"></i> Kembali
        </a>
      </nav>
    </div>

    @if (session('pesan_sukses'))
      <div class="resep-alert-success">
        <i class="mdi mdi-check-circle"></i> {{ session('pesan_sukses') }}
      </div>
    @endif

    @if (session('pesan_error'))
      <div class="resep-alert-danger">
        <i class="mdi mdi-alert-circle"></i> {{ session('pesan_error') }}
      </div>
    @endif

    <div class="row">
      <div class="col-md-7 mb-4" data-aos="fade-up" data-aos-duration="600">
        <div class="resep-table-card">
          <div class="card-body">
            <div class="resep-card-title-row">
              <div class="title-icon">
                <i class="mdi mdi-format-list-bulleted"></i>
              </div>
              <h4>Komponen Bahan</h4>
            </div>
            <div class="table-responsive">
              <table class="resep-table">
                <thead>
                  <tr>
                    <th>Nama Bahan</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($resep as $item)
                    <tr>
                      <td class="bahan-name">{{ $item->nama_stok }}</td>
                      <td>{{ $item->jumlah }}</td>
                      <td>
                        <a href="/resep/item/delete/{{ $item->id_resep }}" class="resep-btn-del-inline"
                          onclick="return confirm('Hapus bahan ini dari resep?')">
                          <i class="mdi mdi-delete-forever"></i> Hapus
                        </a>
                      </td>
                    </tr>
                  @endforeach
                  @if($resep->isEmpty())
                    <tr class="resep-table-empty">
                      <td colspan="3">Belum ada bahan dalam resep ini.</td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-5 mb-4" data-aos="fade-up" data-aos-duration="600" data-aos-delay="150">
        <div class="resep-form-card">
          <div class="card-body">
            <div class="resep-card-title-row">
              <div class="title-icon">
                <i class="mdi mdi-plus-circle"></i>
              </div>
              <h4>Tambah Bahan ke Resep</h4>
            </div>
            <form action="/resep/item/add" method="POST">
              @csrf
              <input type="hidden" name="id_menu" value="{{ $menu->id_produk }}">

              <div class="resep-form-group">
                <label>ID Resep (Manual)</label>
                <input type="number" name="id_resep" class="resep-input" placeholder="Contoh: 1001" required>
              </div>

              <div class="resep-form-group">
                <label>Pilih Bahan Baku</label>
                <select name="id_stok" class="resep-select" required>
                  <option value="">-- Pilih Bahan --</option>
                  @foreach($stok_items as $s)
                    <option value="{{ $s->id_stok }}">{{ $s->nama_stok }} (ID: {{ $s->id_stok }})</option>
                  @endforeach
                </select>
              </div>

              <div class="resep-form-group">
                <label>Jumlah Pemakaian</label>
                <input type="number" name="jumlah" class="resep-input" value="1" min="1" required>
              </div>

              <button type="submit" class="resep-btn-submit">
                <i class="mdi mdi-plus"></i> Tambah ke Resep
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
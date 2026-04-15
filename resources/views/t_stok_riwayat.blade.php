@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/pages.css') }}">

<div class="content-wrapper">
  {{-- Page Header --}}
  <div class="page-header">
    <h3 class="page-title">
      <span class="page-title-icon bg-gradient-primary text-white me-2">
        <i class="mdi mdi-history menu-icon"></i>
      </span> Riwayat Stok
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('stok.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Riwayat Stok</li>
        </ol>
    </nav>
  </div>

  {{-- Flash Messages --}}
  @if (session('pesan_sukses'))
    <div class="page-alert-success">
      <strong><i class="mdi mdi-check-circle me-1"></i>Berhasil!</strong> {{ session('pesan_sukses') }}
    </div>
  @endif

  {{-- Filter Card --}}
  <div class="row mb-4">
    <div class="col-12">
      <div class="page-card">
        <div class="bg-circle" style="opacity: 0.03;"></div>
        <div class="card-body py-4">
          <div class="d-flex align-items-center mb-4">
            <div class="badge-glass badge-glass-info p-2 me-3" style="border-radius: 12px;">
                <i class="mdi mdi-filter-variant mdi-24px"></i>
            </div>
            <div>
                <h5 class="mb-0 text-primary-val">Filter Berdasarkan Tanggal</h5>
                <p class="text-muted small mb-0">Tampilkan data pergerakan stok dalam rentang waktu tertentu</p>
            </div>
          </div>
          <form action="/stok/riwayat" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label text-muted small fw-bold">DARI TANGGAL</label>
              <div class="input-group">
                <span class="input-group-text bg-gradient-primary border-0 text-white"><i class="mdi mdi-calendar"></i></span>
                <input type="date" name="tgl_awal" class="form-control" value="{{ $awal ?? '' }}" required>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label text-muted small fw-bold">SAMPAI TANGGAL</label>
              <div class="input-group">
                <span class="input-group-text bg-gradient-primary border-0 text-white"><i class="mdi mdi-calendar"></i></span>
                <input type="date" name="tgl_akhir" class="form-control" value="{{ $akhir ?? '' }}" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="d-flex gap-2">
                <button type="submit" class="btn-glass-primary px-4 py-2 flex-grow-1">
                  <i class="mdi mdi-magnify"></i> Terapkan Filter
                </button>
                <a href="/stok/riwayat" class="btn btn-outline-light d-flex align-items-center justify-content-center" style="border-radius: 12px; width: 45px; height: 45px;">
                  <i class="mdi mdi-refresh"></i>
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- Main Table Table Card --}}
  <div class="row">
    <div class="col-12 grid-margin">
      <div class="page-card">
        <div class="bg-circle"></div>
        <div class="card-body">
          <div class="page-card-header">
            <div class="page-card-title">
              <div class="title-icon">
                <i class="mdi mdi-format-list-bulleted"></i>
              </div>
              <div>
                <h4>Log Pergerakan Barang</h4>
                <p>Mencatat setiap stok masuk dan keluar secara sistematis</p>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="page-table" id="mytable">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th>Tanggal</th>
                  <th>Nama Produk / Bahan</th>
                  <th class="text-center">Jenis</th>
                  <th class="text-center">Jumlah</th>
                  <th>Keterangan</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($riwayat as $index => $data)
                  <tr>
                    <td class="text-center text-muted small">{{ $index + 1 }}</td>
                    <td class="text-primary-val">
                      <div class="d-flex align-items-center">
                        <i class="mdi mdi-clock-outline me-2 text-muted"></i>
                        {{ \Carbon\Carbon::parse($data->tanggal)->format('d M Y') }}
                      </div>
                    </td>
                    <td>
                        <span class="fw-bold">{{ $data->nama_produk ?? $data->nama_stok }}</span>
                        <div class="extra-small text-muted mt-1">ID: {{ $data->id_stok }}</div>
                    </td>
                    <td class="text-center">
                      @if($data->jenis == 'masuk')
                        <span class="badge-glass badge-glass-success">
                          <i class="mdi mdi-arrow-down-bold me-1"></i> Stok Masuk
                        </span>
                      @else
                        <span class="badge-glass badge-glass-danger">
                          <i class="mdi mdi-arrow-up-bold me-1"></i> Stok Keluar
                        </span>
                      @endif
                    </td>
                    <td class="text-center fw-bold text-primary-val">
                        {{ number_format($data->jumlah, 0, ',', '.') }} <small class="text-muted">Unit</small>
                    </td>
                    <td>
                      <span class="text-muted small italic">{{ $data->keterangan ?? 'Manual Update' }}</span>
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

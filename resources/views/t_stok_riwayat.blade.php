@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/pages/stok_riwayat.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-history menu-icon"></i>
                </span> Riwayat Stok
            </h3>
        </div>

        @if (session('pesan_sukses'))
            <div class="sr-alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-circle"></i> {{ session('pesan_sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Filter Tanggal --}}
        <div class="sr-card">
            <form action="/stok/riwayat" method="GET" class="sr-filter-bar">
                <div class="sr-filter-field">
                    <label class="sr-filter-label">Tanggal Awal</label>
                    <input type="date" name="tgl_awal" class="sr-input" value="{{ $awal ?? '' }}" required>
                </div>
                <div class="sr-filter-field">
                    <label class="sr-filter-label">Tanggal Akhir</label>
                    <input type="date" name="tgl_akhir" class="sr-input" value="{{ $akhir ?? '' }}" required>
                </div>
                <button type="submit" class="sr-btn-filter">
                    <i class="mdi mdi-filter"></i> Filter
                </button>
                <a href="/stok/riwayat" class="sr-btn-reset">
                    <i class="mdi mdi-refresh"></i> Reset
                </a>
            </form>
        </div>

        {{-- Tabel Riwayat --}}
        <div class="sr-card">
            <div class="sr-title-row">
                <div class="sr-title-icon">
                    <i class="mdi mdi-history"></i>
                </div>
                <h4>Riwayat Transaksi Stok</h4>
            </div>

            <div class="table-responsive">
                <table class="sr-table" id="mytable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Produk</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayat as $index => $data)
                            <tr>
                                <td class="col-no">{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($data->tanggal)->format('d/m/Y') }}</td>
                                <td class="col-name">{{ $data->nama_stok }}</td>
                                <td>
                                    @if($data->jenis == 'masuk')
                                        <span class="sr-badge sr-badge-masuk">
                                            <i class="mdi mdi-arrow-down-circle"></i> Stok Masuk
                                        </span>
                                    @else
                                        <span class="sr-badge sr-badge-keluar">
                                            <i class="mdi mdi-arrow-up-circle"></i> Stok Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="col-qty">{{ $data->jumlah }}</td>
                                <td>{{ $data->satuan ?? '-' }}</td>
                                <td class="col-notes">{{ $data->keterangan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
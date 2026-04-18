@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/stok_informasi.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-trending-up menu-icon"></i>
                </span> Informasi Stok
            </h3>
        </div>

        {{-- Stat Cards Row --}}
        <div class="row">

            {{-- Stok Menipis --}}
            <div class="col-md-6 stretch-card grid-margin">
                <div class="si-stat-card card-menipis">
                    <div class="card-body">
                        <div class="si-card-head">
                            <div class="si-head-icon">
                                <i class="mdi mdi-alert"></i>
                            </div>
                            <h4>Stok Menipis (Bahan)</h4>
                        </div>
                        @if(count($stok_menipis) > 0)
                            <ul class="si-stat-list">
                                @foreach($stok_menipis as $item)
                                    <li>
                                        <span class="item-name">{{ $item->nama_stok }}</span>
                                        @if($item->stok < 5)
                                            <span class="si-item-badge si-badge-kritis">
                                                <i class="mdi mdi-alert-circle"></i> {{ $item->stok }} tersisa
                                            </span>
                                        @else
                                            <span class="si-item-badge si-badge-menipis">
                                                <i class="mdi mdi-alert"></i> {{ $item->stok }} tersisa
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="si-empty">
                                <i class="mdi mdi-check-circle-outline"></i>
                                Semua Stok Bahan Aman (&gt;10)
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Top 5 Terlaris --}}
            <div class="col-md-6 stretch-card grid-margin">
                <div class="si-stat-card card-terlaris">
                    <div class="card-body">
                        <div class="si-card-head">
                            <div class="si-head-icon">
                                <i class="mdi mdi-chart-line"></i>
                            </div>
                            <h4>Top 5 Menu Terlaris</h4>
                        </div>
                        @if(count($top_terlaris) > 0)
                            <ul class="si-stat-list">
                                @foreach($top_terlaris as $item)
                                    <li>
                                        <span class="item-name">{{ $item->nama_produk }}</span>
                                        <span class="si-item-badge si-badge-terlaris">
                                            <i class="mdi mdi-arrow-up"></i> {{ $item->total_terjual }} Keluar
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="si-empty">
                                <i class="mdi mdi-chart-bar"></i>
                                Belum ada data penjualan keluar.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Lengkap Stok --}}
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="si-table-card">
                    <div class="si-title-row">
                        <div class="si-title-icon">
                            <i class="mdi mdi-package-variant"></i>
                        </div>
                        <h4>Daftar Lengkap Stok Bahan Baku</h4>
                    </div>
                    <p class="si-table-description">Menampilkan seluruh ketersediaan bahan yang digunakan dalam menu.</p>

                    <div class="table-responsive">
                        <table class="si-table" id="mytable">
                            <thead>
                                <tr>
                                    <th>ID Bahan</th>
                                    <th>Nama Bahan Baku</th>
                                    <th>Jumlah Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stok_lengkap as $s)
                                <tr>
                                    <td class="col-id">{{ $s->id_stok }}</td>
                                    <td class="col-name">{{ $s->nama_stok }}</td>
                                    <td class="col-qty">{{ $s->stok }}</td>
                                    <td>
                                        @if($s->stok < 5)
                                            <span class="si-badge si-badge-danger">
                                                <i class="mdi mdi-alert-circle"></i> Kritis
                                            </span>
                                        @elseif($s->stok < 15)
                                            <span class="si-badge si-badge-warning">
                                                <i class="mdi mdi-alert"></i> Menipis
                                            </span>
                                        @else
                                            <span class="si-badge si-badge-success">
                                                <i class="mdi mdi-check-circle"></i> Tersedia
                                            </span>
                                        @endif
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
@endsection

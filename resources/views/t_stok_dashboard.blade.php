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

        @if(session('pesan_sukses'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('pesan_sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('pesan_error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('pesan_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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
        </div>

        {{-- Daftar Lengkap Stok --}}
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="si-table-card">
                    <div class="si-title-row">
                        <div class="si-title-icon">
                            <i class="mdi mdi-package-variant"></i>
                        </div>
                        <h4>Daftar Lengkap Stok</h4>
                    </div>
                    <p class="si-table-description">Menampilkan seluruh ketersediaan stok.</p>

                    <div class="table-responsive">
                        <table class="si-table" id="mytable">
                            <thead>
                                <tr>
                                    <th>ID Bahan</th>
                                    <th>Nama Bahan Baku</th>
                                    <th>Jumlah Stok</th>
                                    <th>Status</th>
                                    @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                                        <th class="text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stok_lengkap as $s)
                                <tr>
                                    <td class="col-id">{{ $s->id_stok }}</td>
                                    <td class="col-name">{{ $s->nama_stok }}</td>
                                    <td class="col-qty">{{ $s->stok }}</td>
                                    <td>
                                        @if($s->stok < 1)
                                            <span class="si-badge si-badge-danger">
                                                <i class="mdi mdi-alert-circle"></i> Tidak Tersedia
                                            </span>
                                        @else
                                            <span class="si-badge si-badge-success">
                                                <i class="mdi mdi-check-circle"></i> Tersedia
                                            </span>
                                        @endif
                                    </td>
                                    @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                                        <td class="text-center">
                                            <a href="/stok/edit/{{ $s->id_stok }}" class="si-btn si-btn-primary si-btn-sm" title="Edit Stok">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <a href="/stok/bahan/delete/{{ $s->id_stok }}" class="si-btn si-btn-danger si-btn-sm ms-1" onclick="return confirm('Apakah Anda yakin ingin menghapus stok ini?');" title="Hapus Stok">
                                                <i class="mdi mdi-delete"></i>
                                            </a>
                                        </td>
                                    @endif
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

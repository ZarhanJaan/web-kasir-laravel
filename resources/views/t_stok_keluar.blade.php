@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/stok_keluar.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-danger text-white me-2">
                    <i class="mdi mdi-export menu-icon"></i>
                </span> Stok Keluar
            </h3>
        </div>

        @if (session('pesan_sukses'))
            <div class="sk-alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-circle"></i> {{ session('pesan_sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('pesan_error'))
            <div class="sk-alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-alert-circle"></i> {{ session('pesan_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="sk-card">
            <div class="sk-title-action-row">
                <div class="sk-title-left">
                    <div class="sk-title-icon">
                        <i class="mdi mdi-clipboard-text"></i>
                    </div>
                    <h4>Data Stok Keluar (Dari Transaksi &amp; Manual)</h4>
                </div>
                <a href="/stok/keluar/add" class="sk-btn-header">
                    <i class="mdi mdi-plus"></i> Tambah Data Manual
                </a>
            </div>

            <div class="table-responsive">
                <table class="sk-table" id="mytable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Pelanggan</th>
                            <th>Nama Produk</th>
                            <th>Keterangan / Sumber</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayat_keluar as $index => $data)
                        <tr>
                            <td class="col-no">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($data->tanggal)->format('d-m-Y') }}</td>
                            <td>{{ $data->nama_pelanggan ?? '-' }}</td>
                            <td class="col-name">{{ $data->nama_produk ?? $data->nama_stok }}</td>
                            <td class="col-notes">{{ $data->keterangan ?? '-' }}</td>
                            <td class="text-center">
                                <a href="/stok/keluar/edit/{{ $data->id_riwayat }}"
                                    class="sk-btn sk-btn-primary sk-btn-sm" title="Edit Data">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                <a href="/stok/keluar/delete/{{ $data->id_riwayat }}"
                                    class="sk-btn sk-btn-danger sk-btn-sm ms-1"
                                    onclick="return confirm('Yakin ingin menghapus? Stok barang ini akan dikembalikan ke t_produk!')"
                                    title="Hapus Data">
                                    <i class="mdi mdi-delete"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

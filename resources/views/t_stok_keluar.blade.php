@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-danger text-white me-2">
                  <i class="mdi mdi-export menu-icon"></i>
            </span> Stok Keluar
          </h3>
        </div>
        <br>
        
        @if (session('pesan_sukses'))
        <div class="alert alert-success mt-3">
            <i class="fa fa-check-circle"></i> {{ session('pesan_sukses') }}
        </div>
        @endif
        @if (session('pesan_error'))
        <div class="alert alert-danger mt-3">
            <i class="fa fa-exclamation-triangle"></i> {{ session('pesan_error') }}
        </div>
        @endif
        
        <div class="card shadow p-3 mb-5 bg-body rounded">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title text-danger mb-0"><i class="mdi mdi-clipboard-text"></i> Data Stok Keluar (Dari Transaksi & Manual)</h4>
                    <a href="/stok/keluar/add" class="btn btn-sm btn-gradient-danger"><i class="mdi mdi-plus"></i> Tambah Data Manual</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mt-3" id="mytable">
                        <thead class="bg-light">
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
                            @forelse ($riwayat_keluar as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($data->tanggal)->format('d-m-Y') }}</td>
                                <td>{{ $data->nama_pelanggan ?? '-' }}</td>
                                <td class="font-weight-bold text-primary">{{ $data->nama_produk ?? $data->nama_stok }}</td>
                                <td>{{ $data->keterangan ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="/stok/keluar/edit/{{ $data->id_riwayat }}" class="btn btn-sm btn-primary py-1 px-2" title="Edit Data"><i class="mdi mdi-pencil"></i></a>
                                    <a href="/stok/keluar/delete/{{ $data->id_riwayat }}" class="btn btn-sm btn-danger py-1 px-2" onclick="return confirm('Yakin ingin menghapus? Stok barang ini akan dikembalikan ke t_produk!')" title="Hapus Data"><i class="mdi mdi-delete"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data stok keluar...</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

      </div>

@endsection

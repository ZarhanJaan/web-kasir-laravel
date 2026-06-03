@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/kategori.css') }}">

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-shape menu-icon"></i>
            </span> Manajemen Kategori
        </h3>
    </div>

    @if(session('pesan_sukses'))
        <div class="kt-alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle"></i>
            {{ session('pesan_sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('pesan_error'))
        <div class="kt-alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle"></i>
            {{ session('pesan_error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">

        {{-- Form Tambah Kategori --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="kt-card kt-card-form">
                <div class="card-body">
                    <div class="kt-card-head">
                        <div class="kt-head-icon">
                            <i class="mdi mdi-plus"></i>
                        </div>
                        <div>
                            <h4>Tambah Kategori</h4>
                            <small>Buat nama kategori baru</small>
                        </div>
                    </div>

                    <form action="/kategori/insert" method="POST">
                        @csrf
                        <div class="kt-form-group">
                            <label class="kt-form-label">
                                <i class="mdi mdi-tag-text-outline"></i> Nama Kategori
                            </label>
                            <input
                                type="text"
                                name="nama_kategori"
                                class="kt-input"
                                placeholder="Contoh: Coffee, Non Coffee..."
                                required
                            >
                            @error('nama_kategori')
                                <span class="kt-field-error">
                                    <i class="mdi mdi-alert-circle-outline"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="kt-btn-submit">
                            <i class="mdi mdi-content-save"></i> Simpan Kategori
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabel Daftar Kategori --}}
        <div class="col-md-8 grid-margin stretch-card">
            <div class="kt-card kt-card-table">
                <div class="card-body">
                    <div class="kt-card-head">
                        <div class="kt-head-icon">
                            <i class="mdi mdi-format-list-bulleted"></i>
                        </div>
                        <div>
                            <h4>Daftar Kategori</h4>
                            <small>Total: {{ count($kategori) }} kategori</small>
                        </div>
                        <span class="kt-count-badge">
                            <i class="mdi mdi-shape"></i> {{ count($kategori) }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="kt-table" id="mytable">
                            <thead>
                                <tr>
                                    <th class="col-id">ID</th>
                                    <th class="col-name">Nama Kategori</th>
                                    @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                                        <th class="text-center" style="width: 80px;">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kategori as $k)
                                <tr>
                                    <td class="col-id">#{{ $k->id_kategori }}</td>
                                    <td class="col-name">{{ $k->nama_kategori }}</td>
                                    @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                                        <td class="text-center">
                                            <a
                                                href="/kategori/delete/{{ $k->id_kategori }}"
                                                class="kt-btn-delete"
                                                onclick="return confirm('Hapus kategori \'{{ $k->nama_kategori }}\'?');"
                                                title="Hapus Kategori"
                                            >
                                                <i class="mdi mdi-delete"></i>
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3">
                                        <div class="kt-empty">
                                            <i class="mdi mdi-shape-outline"></i>
                                            <p>Belum ada data kategori.<br>Tambahkan kategori pertama Anda.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

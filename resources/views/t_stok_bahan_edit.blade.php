@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/stok_informasi.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-pencil menu-icon"></i>
                </span> Edit Bahan Baku
            </h3>
        </div>

        @if (session('pesan_error'))
            <div class="si-alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-alert-circle"></i> {{ session('pesan_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="si-table-card">
            <div class="si-title-row">
                <div class="si-title-icon">
                    <i class="mdi mdi-pencil-outline"></i>
                </div>
                <h4>Edit Data Stok Bahan Baku</h4>
            </div>
            <p class="si-table-description">Perbarui nama atau jumlah stok bahan baku.</p>

            <form action="/stok/bahan/update/{{ $bahan->id_stok }}" method="POST">
                @csrf

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <div class="si-form-group">
                            <label class="si-form-label">ID Bahan (Dikunci)</label>
                            <input type="text" class="si-input"
                                value="{{ $bahan->id_stok }}" readonly disabled>
                        </div>

                        <div class="si-form-group">
                            <label class="si-form-label">Nama Bahan Baku</label>
                            <input type="text" name="nama_stok" class="si-input"
                                value="{{ old('nama_stok', $bahan->nama_stok) }}" required>
                            @error('nama_stok')
                                <p class="si-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="si-form-group">
                            <label class="si-form-label">Jumlah Stok</label>
                            <input type="number" name="stok" class="si-input"
                                value="{{ old('stok', $bahan->stok) }}" min="0" required>
                            @error('stok')
                                <p class="si-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="si-form-actions">
                    <button type="submit" class="si-btn si-btn-primary">
                        <i class="mdi mdi-content-save"></i> Simpan Perubahan
                    </button>
                    <a href="/stok" class="si-btn si-btn-secondary">
                        <i class="mdi mdi-close"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

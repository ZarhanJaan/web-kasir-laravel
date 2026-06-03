@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/stok_edit.css') }}">

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-pencil menu-icon"></i>
            </span> Edit Informasi Stok
        </h3>
        <a href="/stok" class="sm-btn-back">
            <i class="mdi mdi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('pesan_error'))
        <div class="se-alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle"></i>
            {{ session('pesan_error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 grid-margin stretch-card">
            <div class="sm-form-card" style="width: 100%;">
                <div class="card-body">

                    {{-- Header --}}
                    <div class="sm-card-head">
                        <div class="sm-head-icon">
                            <i class="mdi mdi-package-variant-closed"></i>
                        </div>
                        <div>
                            <h4>Edit Data Stok</h4>
                            <small style="color: var(--text-muted); font-size: 12.5px;">Perbarui informasi item stok di bawah ini</small>
                        </div>
                    </div>

                    {{-- Info Hint --}}
                    <div class="sm-hint-box">
                        <i class="mdi mdi-information-outline"></i>
                        <p>ID Stok bersifat <strong style="color: var(--text-secondary);">read-only</strong> dan tidak dapat diubah. Anda hanya dapat memperbarui nama dan kategori stok.</p>
                    </div>

                    <form action="/stok/update/{{ $stok->id_stok }}" method="POST">
                        @csrf

                        {{-- ID Stok (Read Only) --}}
                        <div class="sm-form-group">
                            <label class="sm-form-label">
                                <i class="mdi mdi-identifier"></i> ID Stok
                            </label>
                            <div class="sm-id-badge">
                                <i class="mdi mdi-lock-outline"></i>
                                #{{ $stok->id_stok }}
                            </div>
                        </div>

                        {{-- Nama Stok --}}
                        <div class="sm-form-group">
                            <label class="sm-form-label">
                                <i class="mdi mdi-tag-text-outline"></i> Nama Stok
                            </label>
                            <input
                                type="text"
                                name="nama_stok"
                                class="sm-input"
                                value="{{ old('nama_stok', $stok->nama_stok) }}"
                                placeholder="Masukkan nama stok..."
                                required
                            >
                            @error('nama_stok')
                                <span class="sm-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Jumlah Stok --}}
                        <div class="sm-form-group">
                            <label class="sm-form-label">
                                <i class="mdi mdi-numeric"></i> Jumlah Stok
                            </label>
                            <input
                                type="number"
                                name="stok"
                                class="sm-input"
                                value="{{ old('stok', $stok->stok) }}"
                                placeholder="Masukkan jumlah stok..."
                                required
                                min="0"
                            >
                            @error('stok')
                                <span class="sm-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Kategori (Satuan) --}}
                        <div class="sm-form-group">
                            <label class="sm-form-label">
                                <i class="mdi mdi-shape-outline"></i> Kategori
                            </label>
                            <select name="satuan" class="sm-select">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategori as $k)
                                    <option value="{{ $k->nama_kategori }}" {{ old('satuan', $stok->satuan) == $k->nama_kategori ? 'selected' : '' }}>
                                        {{ $k->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('satuan')
                                <span class="sm-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="sm-action-bar">
                            <button type="submit" class="sm-btn-submit">
                                <i class="mdi mdi-content-save"></i> Simpan Perubahan
                            </button>
                            <a href="/stok" class="sm-btn-cancel">
                                <i class="mdi mdi-close"></i> Batal
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

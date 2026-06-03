@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/stok_keluar.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-pencil menu-icon"></i>
                </span> Edit Stok Keluar
            </h3>
        </div>
        <br>

        @if (session('pesan_error'))
            <div class="sk-alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-alert-circle"></i> {{ session('pesan_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="sk-card">
            <div class="sk-title-row">
                <div class="sk-title-icon sk-title-icon-primary">
                    <i class="mdi mdi-pencil-outline"></i>
                </div>
                <h4>Edit Data Stok Keluar</h4>
            </div>
            <p class="sk-card-description">Perubahan jumlah akan otomatis mensinkronkan stok produk terkait.</p>
            <hr class="sk-divider">

            <div class="sk-alert-warning">
                <i class="mdi mdi-information-outline"></i>
                <strong>Info:</strong> Mengubah jumlah stok di sini akan otomatis mensinkronkan ulang
                kuantitas pada gudang produk asli. Produk terkait:
                <strong style="color: var(--text-primary);">{{ $riwayat->nama_produk }}</strong>
            </div>

            <form action="/stok/keluar/update/{{ $riwayat->id_riwayat }}" method="POST">
                @csrf

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <div class="sk-form-group">
                            <label class="sk-form-label">Nama Produk (Dikunci)</label>
                            <input type="text" class="sk-input"
                                value="{{ $riwayat->nama_produk }}" readonly disabled>
                        </div>

                        <div class="sk-form-group">
                            <label class="sk-form-label">Kategori</label>
                            @php
                                $satuanSaatIni = old('satuan', $riwayat->satuan);
                                $adaDiKategori = $kategori->contains(fn ($k) => $k->nama_kategori === $satuanSaatIni);
                            @endphp
                            <select name="satuan" class="sk-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @if($satuanSaatIni && !$adaDiKategori)
                                    <option value="{{ $satuanSaatIni }}" selected>{{ $satuanSaatIni }}</option>
                                @endif
                                @foreach($kategori as $k)
                                    <option value="{{ $k->nama_kategori }}"
                                        {{ old('satuan', $riwayat->satuan) == $k->nama_kategori ? 'selected' : '' }}>
                                        {{ $k->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('satuan')
                                <p class="sk-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sk-form-group">
                            <label class="sk-form-label">Jumlah Keluar (Pcs/Unit)</label>
                            <input type="number" name="jumlah" class="sk-input"
                                value="{{ old('jumlah', $riwayat->jumlah) }}" min="1" required>
                            @error('jumlah')
                                <p class="sk-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="sk-form-group">
                            <label class="sk-form-label">Tanggal Transaksi</label>
                            <input type="date" name="tanggal" class="sk-input"
                                value="{{ old('tanggal', $riwayat->tanggal) }}" required>
                            @error('tanggal')
                                <p class="sk-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sk-form-group">
                            <label class="sk-form-label">Keterangan</label>
                            <textarea name="keterangan" class="sk-textarea" rows="5"
                                placeholder="Misal: Retur, Pembelian Baru, Rusak">{{ old('keterangan', $riwayat->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="sk-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="sk-form-actions">
                    <button type="submit" class="sk-btn sk-btn-primary">
                        <i class="mdi mdi-content-save"></i> Simpan Perubahan
                    </button>
                    <a href="/stok/keluar" class="sk-btn sk-btn-secondary">
                        <i class="mdi mdi-close"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

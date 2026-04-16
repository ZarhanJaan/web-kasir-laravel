@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/pages/riwayat_transaksi_form.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-plus-circle menu-icon"></i>
                </span> Tambah Transaksi
            </h3>
        </div>

        @if(session('pesan_sukses'))
            <div class="mu-alert-success">{{ session('pesan_sukses') }}</div>
        @endif
        @if(session('pesan_error'))
            <div class="mu-alert-danger">{{ session('pesan_error') }}</div>
        @endif
        @if($errors->any())
            <div class="mu-alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mu-form-card" data-aos="fade-up" data-aos-duration="800">
            <div class="card-body">
                <div class="card-title-row">
                    <div class="title-icon">
                        <i class="mdi mdi-receipt"></i>
                    </div>
                    <h4>Form Transaksi Baru</h4>
                </div>

                <form action="/riwayat-transaksi/insert" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="mu-form-group">
                                <label>ID Transaksi</label>
                                <input name="id_penjualan" class="mu-input" value="{{ old('id_penjualan') }}" placeholder="Masukkan ID transaksi">
                                @error('id_penjualan')
                                    <div class="mu-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mu-form-group">
                                <label>Tanggal</label>
                                <input name="tanggal" class="mu-input" type="date" value="{{ old('tanggal') }}">
                                @error('tanggal')
                                    <div class="mu-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mu-form-group">
                                <label>Nama Pelanggan</label>
                                <input name="nama_pelanggan" class="mu-input" value="{{ old('nama_pelanggan') }}" placeholder="Masukkan nama pelanggan">
                                @error('nama_pelanggan')
                                    <div class="mu-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="mu-form-group">
                                <label>Total</label>
                                <input name="total" class="mu-input" value="{{ old('total') }}" placeholder="Total harga">
                                @error('total')
                                    <div class="mu-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mu-form-group">
                                <label>Daftar Produk & Jumlah</label>
                                <div id="produk-list">
                                    <div class="mu-produk-row produk-row">
                                        <div class="produk-select">
                                            <select name="id_produk[]" class="mu-select">
                                                @foreach($produkList as $produk)
                                                    <option value="{{ $produk->id_produk }}">{{ $produk->nama_produk }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="produk-qty">
                                            <input type="number" name="jumlah_barang[]" class="mu-input" placeholder="Qty">
                                        </div>
                                        <button type="button" class="mu-btn-remove remove-produk">−</button>
                                    </div>
                                </div>
                                <button type="button" class="mu-btn-add-produk" id="add-produk">
                                    <i class="mdi mdi-plus"></i> Tambah Produk
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr class="mu-form-divider">

                    <div class="mu-form-actions">
                        <button type="submit" class="mu-btn-submit">
                            <i class="mdi mdi-content-save"></i> Simpan
                        </button>
                        <a href="/riwayat-transaksi" class="mu-btn-back">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

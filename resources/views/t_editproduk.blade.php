
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/menu.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/resep.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-silverware menu-icon"></i>
                </span> Edit Menu
            </h3>
            <nav aria-label="breadcrumb">
                <a href="/menu" class="resep-btn-back">
                    <i class="mdi mdi-arrow-left"></i> Kembali
                </a>
            </nav>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-7 mb-4" data-aos="fade-up" data-aos-duration="600">
                <div class="resep-info-card">
                    <div class="card-body">
                        <div class="resep-card-title-row">
                            <div class="title-icon">
                                <i class="mdi mdi-pencil"></i>
                            </div>
                            <h4>Edit Informasi Menu</h4>
                        </div>

                        <form action="/menu/update/{{ $produk->id_produk }}" method="POST">
                            @csrf

                            <div class="resep-form-group">
                                <label>ID Menu</label>
                                <input name="id_produk" class="resep-input" value="{{ $produk->id_produk }}" readonly>
                                <div class="resep-error">@error('id_produk') {{ $message }} @enderror</div>
                            </div>

                            <div class="resep-form-group">
                                <label>Nama Menu</label>
                                <input name="nama_produk" class="resep-input" value="{{ $produk->nama_produk }}" required>
                                <div class="resep-error">@error('nama_produk') {{ $message }} @enderror</div>
                            </div>

                            <div class="resep-form-group">
                                <label>Harga Jual</label>
                                <div class="resep-input-group">
                                    <span class="resep-input-prefix">Rp</span>
                                    <input name="harga_jual" class="resep-input" value="{{ $produk->harga_jual }}" required>
                                </div>
                                <div class="resep-error">@error('harga_jual') {{ $message }} @enderror</div>
                            </div>

                            <div class="resep-form-group">
                                <label>Kategori Menu</label>
                                <select name="kategori" class="resep-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Makanan" {{ $produk->kategori == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                                    <option value="Minuman" {{ $produk->kategori == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                                </select>
                                <div class="resep-error">@error('kategori') {{ $message }} @enderror</div>
                            </div>

                            <div class="resep-actions-bar" style="justify-content: flex-start;">
                                <a href="/menu" class="resep-btn-cancel">
                                    <i class="mdi mdi-close"></i> Batal
                                </a>
                                <button type="submit" class="resep-btn-save">
                                    <i class="mdi mdi-content-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/pages/riwayat_transaksi_form.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-pencil menu-icon"></i>
                </span> Edit Transaksi
            </h3>
        </div>

        <div class="mu-form-card" data-aos="fade-up" data-aos-duration="800">
            <div class="card-body">
                <div class="card-title-row">
                    <div class="title-icon">
                        <i class="mdi mdi-receipt"></i>
                    </div>
                    <h4>Edit Transaksi #{{ $penjualan->id_penjualan }}</h4>
                </div>

                <form action="/riwayat-transaksi/update/{{ $penjualan->id_penjualan }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="mu-form-group">
                                <label>ID Transaksi</label>
                                <input name="id_penjualan" class="mu-input" value="{{ $penjualan->id_penjualan }}" readonly>
                                @error('id_penjualan')
                                    <div class="mu-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mu-form-group">
                                <label>Tanggal</label>
                                <input name="tanggal" class="mu-input" type="date" value="{{ $penjualan->tanggal }}">
                                @error('tanggal')
                                    <div class="mu-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mu-form-group">
                                <label>Nama Pelanggan</label>
                                <input name="nama_pelanggan" class="mu-input" value="{{ $penjualan->nama_pelanggan }}" placeholder="Masukkan nama pelanggan">
                                @error('nama_pelanggan')
                                    <div class="mu-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="mu-form-group">
                                <label>Total</label>
                                <input name="total" class="mu-input" value="{{ $penjualan->total }}" placeholder="Total harga">
                                @error('total')
                                    <div class="mu-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mu-form-group">
                                <label>Daftar Produk & Jumlah</label>
                                <div id="produk-list">
                                    @php
                                        $produkIds = explode(',', $penjualan->id_produk);
                                        $jumlahs = is_array($penjualan->jumlah_barang) ? $penjualan->jumlah_barang : explode(',', $penjualan->jumlah_barang);
                                        if(count($jumlahs) < count($produkIds)) $jumlahs = array_pad($jumlahs, count($produkIds), '');
                                    @endphp
                                    @foreach($produkIds as $i => $id_produk)
                                        <div class="mu-produk-row produk-row">
                                            <div class="produk-select">
                                                <select name="id_produk[]" class="mu-select">
                                                    @foreach(App\Models\ProdukModel::all() as $produk)
                                                        <option value="{{ $produk->id_produk }}" @if($produk->id_produk == $id_produk) selected @endif>{{ $produk->nama_produk }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="produk-qty">
                                                <input type="number" name="jumlah_barang[]" class="mu-input" placeholder="Qty" value="{{ $jumlahs[$i] ?? '' }}">
                                            </div>
                                            <button type="button" class="mu-btn-remove remove-produk">−</button>
                                        </div>
                                    @endforeach
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

    @push('scripts')
    <script>
    window.onload = function() {
        var produkList = document.getElementById('produk-list');
        var addBtn = document.getElementById('add-produk');
        if (!produkList || !addBtn) return;
        addBtn.onclick = function(e) {
            e.preventDefault();
            var row = produkList.querySelector('.produk-row');
            if (!row) return;
            var clone = row.cloneNode(true);
            var selects = clone.querySelectorAll('select');
            for (var i = 0; i < selects.length; i++) selects[i].selectedIndex = 0;
            var inputs = clone.querySelectorAll('input');
            for (var i = 0; i < inputs.length; i++) inputs[i].value = '';
            produkList.appendChild(clone);
        };
        produkList.onclick = function(e) {
            e = e || window.event;
            var target = e.target || e.srcElement;
            if(target.classList.contains('remove-produk')) {
                e.preventDefault();
                var rows = produkList.querySelectorAll('.produk-row');
                if(rows.length > 1) {
                    target.closest('.produk-row').remove();
                }
            }
        };
    };
    </script>
    @endpush

@endsection

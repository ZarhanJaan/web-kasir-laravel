@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/stok_keluar.css') }}">

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-danger text-white me-2">
                <i class="mdi mdi-export menu-icon"></i>
            </span> Tambah Stok Keluar
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
            <div class="sk-title-icon">
                <i class="mdi mdi-cart-arrow-right"></i>
            </div>
            <h4>Stok Keluar</h4>
        </div>
        <p class="sk-card-description">Input data barang yang keluar secara manual dari gudang.</p>
        <hr class="sk-divider">

        <form id="pos-form" action="/stok/keluar/insert" method="POST">
            @csrf

            <div class="row g-3 mb-2">
                <div class="col-12 col-md-3">
                    <div class="sk-form-group">
                        <label class="sk-form-label">ID Transaksi (Manual)</label>
                        <input type="number" name="id_riwayat_base" class="sk-input"
                            placeholder="Contoh: 9001" required>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="sk-form-group">
                        <label class="sk-form-label">Tgl. Keluar</label>
                        <input type="date" name="tanggal" class="sk-input"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="sk-form-group">
                        <label class="sk-form-label">Satuan</label>
                        <select name="satuan" id="satuan" class="sk-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k->nama_kategori }}" {{ old('satuan') == $k->nama_kategori ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('satuan')
                            <p class="sk-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="sk-form-group">
                        <label class="sk-form-label">Keterangan / Sumber</label>
                        <input type="text" name="keterangan" id="keterangan"
                            class="sk-input" placeholder="Misal: Barang Rusak, Hibah"
                            value="Stok Keluar Manual">
                    </div>
                </div>
            </div>

            <p class="sk-section-title">
                <i class="mdi mdi-format-list-bulleted"></i> Daftar Barang Keluar
            </p>

            <div id="sk-keranjang">
                <div class="sk-item-row">
                    <select name="id_produk[]" class="sk-select produk-select" required>
                        <option value="" data-harga="0">-- Pilih Produk --</option>
                                @foreach($produk as $p)
                                    <option value="{{ $p->id_stok }}"
                                        data-nama="{{ $p->nama_stok }}">
                                        {{ $p->nama_stok }} (Stok: {{ $p->stok }} Pcs)
                                    </option>
                                @endforeach
                    </select>
                    <input type="number" name="jumlah_barang[]"
                        class="sk-input jumlah-input"
                        value="1" min="1" placeholder="Qty" required>
                    <button type="button" class="sk-btn-remove remove-item" style="display:none;">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
            </div>

            <button type="button" class="sk-btn-add-row" id="add-item">
                <i class="mdi mdi-plus"></i> Tambah Baris Barang
            </button>

            <div class="sk-form-actions">
                <a href="/stok/keluar" class="sk-btn sk-btn-secondary">
                    <i class="mdi mdi-close"></i> Batal
                </a>
                <button type="submit" class="sk-btn sk-btn-danger" id="btn-submit-form">
                    <i class="mdi mdi-content-save"></i> Proses Pengeluaran Stok
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const keranjang = document.getElementById('sk-keranjang');
        const btnAdd    = document.getElementById('add-item');

        btnAdd.addEventListener('click', function() {
            const firstRow = document.querySelector('.sk-item-row');
            const clone    = firstRow.cloneNode(true);
            clone.querySelector('.produk-select').selectedIndex = 0;
            clone.querySelector('.jumlah-input').value = 1;
            clone.querySelector('.remove-item').style.display = 'inline-flex';

            clone.querySelector('.remove-item').addEventListener('click', function() {
                clone.remove();
                updateRemoveButtons();
            });

            keranjang.appendChild(clone);
            updateRemoveButtons();
        });

        function updateRemoveButtons() {
            const btns = document.querySelectorAll('.remove-item');
            btns.forEach(btn => btn.style.display = btns.length > 1 ? 'inline-flex' : 'none');
        }
    });
</script>
@endsection

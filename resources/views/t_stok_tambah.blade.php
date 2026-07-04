@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/stok_masuk.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-import menu-icon"></i>
                </span> Stok Masuk
            </h3>
        </div>
        <br>

        @if (session('pesan_error'))
            <div class="sm-alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-alert-circle"></i> {{ session('pesan_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="sm-card">
            <div class="sm-title-row">
                <div class="sm-title-icon">
                    <i class="mdi mdi-import"></i>
                </div>
                <h4>Form Stok Masuk</h4>
            </div>
            <p class="sm-card-description">Tambahkan data barang yang masuk ke gudang atau input bahan baku baru.</p>
            <hr class="sm-divider">

            <form action="/stok/insert" method="POST">
                @csrf
                <div class="row g-4">

                    <div class="col-12">
                        <div class="sm-form-group">
                            <label class="sm-form-label">ID Riwayat (Manual)</label>
                            <input type="number" name="id_riwayat" class="sm-input"
                                placeholder="Contoh: 5001" required>
                        </div>
                    </div>

                    {{-- Kolom Kiri --}}
                    <div class="col-12 col-md-6">
                        <div class="sm-form-group">
                            <label class="sm-form-label">Pilih Bahan Baku (Ketik ID atau Nama)</label>
                            <input list="bahan_list" name="id_produk" class="sm-input"
                                placeholder="Contoh: Minyak Goreng" required>
                            <datalist id="bahan_list">
                                @foreach($produk as $p)
                                    <option value="{{ $p->id_stok }}">{{ $p->nama_stok }} (ID: {{ $p->id_stok }})</option>
                                @endforeach
                            </datalist>
                            @error('id_produk')
                                <p class="sm-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm-info-box">
                            <p class="sm-info-box-title">
                                <i class="mdi mdi-plus-circle-outline"></i> Informasi Bahan Baru (Opsional)
                            </p>
                            <p class="sm-info-box-desc">Hanya isi jika Anda mengetik nama bahan baru yang belum terdaftar.</p>
                            <label class="sm-info-box-label">ID Bahan Baru</label>
                            <input name="id_stok_baru" class="sm-input" placeholder="Contoh: 9001">
                        </div>

                        <input type="hidden" name="jenis" value="masuk">

                        <div class="sm-form-group">
                            <label class="sm-form-label">Jumlah</label>
                            <input type="number" name="jumlah" class="sm-input"
                                value="{{ old('jumlah') }}" min="1" required>
                            @error('jumlah')
                                <p class="sm-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm-form-group">
                            <label class="sm-form-label">Kategori</label>
                            <select name="satuan" id="satuan_stok" class="form-select sm-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategori as $k)
                                    <option value="{{ $k->nama_kategori }}" {{ old('satuan') == $k->nama_kategori ? 'selected' : '' }}>
                                        {{ $k->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm-form-group" id="isi_pcs_container" style="display: none;">
                            <label class="sm-form-label">Isi Pcs per Box</label>
                            <input type="number" name="isi_pcs_per_box" class="sm-input" 
                                placeholder="Contoh: 10" min="1">
                            <small class="text-muted" style="font-size: 10px;">* Stok di gudang akan otomatis dikalikan</small>
                        </div>

                        <div class="sm-form-group">
                            <label class="sm-form-label">Harga Beli (Per Item)</label>
                            <input type="number" name="harga_beli" class="sm-input"
                                value="{{ old('harga_beli') }}" min="0" required>
                            @error('harga_beli')
                                <p class="sm-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Kolom Kanan --}}
                    <div class="col-12 col-md-6">
                        <div class="sm-form-group">
                            <label class="sm-form-label">Tanggal Transaksi</label>
                            <input type="date" name="tanggal" class="sm-input"
                                value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal')
                                <p class="sm-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm-form-group">
                            <label class="sm-form-label">Keterangan (Opsional)</label>
                            <textarea name="keterangan" class="sm-textarea" rows="5"
                                placeholder="Misal: Retur, Pembelian Baru, dll. Jika dikosongkan otomatis terisi 'Stok Masuk'">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="sm-field-error"><i class="mdi mdi-alert-circle-outline"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="sm-form-actions">
                    <button type="submit" class="sm-btn-submit">
                        <i class="mdi mdi-content-save"></i> Simpan Stok
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectSatuan = document.getElementById('satuan_stok');
            const container = document.getElementById('isi_pcs_container');
            const inputIsi = container.querySelector('input');

            function toggleIsiPcs() {
                if (selectSatuan.value === 'box') {
                    container.style.display = 'block';
                    inputIsi.setAttribute('required', 'required');
                } else {
                    container.style.display = 'none';
                    inputIsi.removeAttribute('required');
                    inputIsi.value = '';
                }
            }

            selectSatuan.addEventListener('change', toggleIsiPcs);
            toggleIsiPcs(); // Run on load
        });
    </script>
@endsection

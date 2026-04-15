@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-danger text-white me-2">
                <i class="mdi mdi-export menu-icon"></i>
            </span> Stok Keluar
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('stok.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Stok Keluar</li>
            </ol>
        </nav>
    </div>

    @if (session('pesan_error'))
    <div class="alert alert-danger" role="alert">
        <i class="mdi mdi-alert-circle"></i>
        {{ session('pesan_error') }}
    </div>
    @endif

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card shadow-sm border-0 bg-body rounded">
                <div class="card-body p-lg-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0 text-danger"><i class="mdi mdi-cart-outline"></i> Form Pengeluaran Stok</h4>
                        <span class="badge bg-gradient-warning text-dark">Keranjang Keluar</span>
                    </div>
                    <p class="card-description text-muted mb-5">Gunakan formulir ini untuk mencatat barang yang keluar dari gudang (Retur, Rusak, Hibah, dll).</p>

                    <form id="pos-form" action="/stok/keluar/insert" method="POST" class="forms-sample">
                        @csrf
                        
                        <div class="row mb-5">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">ID Transaksi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-danger text-white"><i class="mdi mdi-numeric"></i></span>
                                        </div>
                                        <input type="number" name="id_riwayat_base" class="form-control" placeholder="Contoh: 9001" value="{{ old('id_riwayat_base') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Tgl. Keluar <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-danger text-white"><i class="mdi mdi-calendar"></i></span>
                                        </div>
                                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Nama Pelanggan/Entitas</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-danger text-white"><i class="mdi mdi-account"></i></span>
                                        </div>
                                        <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control" value="{{ old('nama_pelanggan', 'Umum') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Keterangan</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-gradient-danger text-white"><i class="mdi mdi-note-text"></i></span>
                                        </div>
                                        <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Misal: Barang Rusak" value="{{ old('keterangan', 'Stok Keluar Manual') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="font-weight-bold mb-0"><i class="mdi mdi-format-list-bulleted text-danger"></i> Daftar Barang Keluar</h5>
                            <button type="button" class="btn btn-inverse-info btn-sm" id="add-item">
                                <i class="mdi mdi-plus"></i> Tambah Baris Barang
                            </button>
                        </div>
                        
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 70%">Produk / Bahan Baku</th>
                                        <th style="width: 20%">Jumlah (Qty)</th>
                                        <th style="width: 10%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="keranjang">
                                    <tr class="item-row">
                                        <td>
                                            <select name="id_produk[]" class="form-control produk-select" required>
                                                <option value="" data-harga="0">-- Pilih Produk --</option>
                                                @foreach($produk as $p)
                                                    <option value="{{ $p->id_stok }}" data-nama="{{ $p->nama_stok }}">
                                                        {{ $p->nama_stok }} (Tersisa: {{ $p->stok }} Unit)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" name="jumlah_barang[]" class="form-control jumlah-input text-center" value="1" min="1" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Unit</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item" style="display:none;">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <hr class="mt-5">
                        <div class="mt-4 d-flex justify-content-end">
                            <a href="/stok/keluar" class="btn btn-light me-2">Batal</a>
                            <button type="submit" class="btn btn-gradient-danger btn-icon-text" id="btn-submit-form">
                                <i class="mdi mdi-file-check btn-icon-prepend"></i> Proses Pengeluaran Stok
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const keranjang = document.getElementById('keranjang');
        const btnAdd = document.getElementById('add-item');

        // Add Row
        btnAdd.addEventListener('click', function() {
            const firstRow = document.querySelector('.item-row');
            const clone = firstRow.cloneNode(true);
            
            // Clear selections and inputs in the clone
            clone.querySelector('.produk-select').selectedIndex = 0;
            clone.querySelector('.jumlah-input').value = 1;
            clone.querySelector('.remove-item').style.display = 'inline-block';
            
            // Add remove event to the new button
            clone.querySelector('.remove-item').addEventListener('click', function() {
                clone.remove();
                updateRemoveButtons();
            });
            
            keranjang.appendChild(clone);
            updateRemoveButtons();
        });

        // Initialize remove buttons for existing rows if any
        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.item-row');
            const btns = document.querySelectorAll('.remove-item');
            if(rows.length > 1) {
                btns.forEach(btn => btn.style.display = 'inline-block');
            } else {
                btns.forEach(btn => btn.style.display = 'none');
            }
        }
        
        // Setup initial remove button
        updateRemoveButtons();

        // Delegate remove event for initial row
        keranjang.addEventListener('click', function(e) {
            if(e.target.closest('.remove-item')) {
                const row = e.target.closest('tr');
                const rows = document.querySelectorAll('.item-row');
                if(rows.length > 1) {
                    row.remove();
                    updateRemoveButtons();
                }
            }
        });
    });
</script>

@endsection

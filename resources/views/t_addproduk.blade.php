@extends('layouts.app')

@section('content')

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-book-plus menu-icon"></i>
                </span> Tambah Menu & Resep
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/resep">Manajemen Resep</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Baru</li>
                </ol>
            </nav>
        </div>

        @if (session('pesan_error'))
        <div class="alert alert-danger" role="alert">
            <i class="mdi mdi-alert-circle"></i>
            {{ session('pesan_error') }}
        </div>
        @endif

        <form action="/resep/insert" method="POST" class="forms-sample">
            @csrf
            <div class="row">
                <!-- Column 1: Basic Info -->
                <div class="col-md-5 grid-margin stretch-card">
                    <div class="card shadow-sm border-0 bg-body rounded">
                        <div class="card-body p-4">
                            <h4 class="card-title text-primary mb-4"><i class="mdi mdi-information-outline"></i> Informasi Menu Utama</h4>
                            <p class="card-description text-muted mb-4">Detail dasar untuk menu yang akan dijual.</p>
                            
                            <div class="form-group mb-4">
                                <label class="font-weight-bold">ID Menu (Manual) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-gradient-primary text-white"><i class="mdi mdi-numeric"></i></span>
                                    </div>
                                    <input name="id_produk" class="form-control" value="{{ old('id_produk') }}" placeholder="Contoh: 2001" required>
                                </div>
                                @error('id_produk') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Nama Menu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-gradient-primary text-white"><i class="mdi mdi-food"></i></span>
                                    </div>
                                    <input name="nama_produk" class="form-control" value="{{ old('nama_produk') }}" placeholder="Contoh: Nasi Goreng Spesial" required>
                                </div>
                                @error('nama_produk') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Harga Jual <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-gradient-primary text-white">Rp</span>
                                    </div>
                                    <input name="harga_jual" class="form-control" value="{{ old('harga_jual') }}" placeholder="0" required>
                                </div>
                                @error('harga_jual') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Kategori Menu <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-control border-primary" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Makanan" {{ old('kategori') == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                                    <option value="Minuman" {{ old('kategori') == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                                </select>
                                @error('kategori') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Ingredients (Resep) -->
                <div class="col-md-7 grid-margin stretch-card">
                    <div class="card shadow-sm border-0 bg-body rounded">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title text-success mb-0"><i class="mdi mdi-flask-outline"></i> Komposisi Resep</h4>
                                <button type="button" class="btn btn-inverse-success btn-sm" id="btn-add-bahan">
                                    <i class="mdi mdi-plus"></i> Tambah Bahan
                                </button>
                            </div>
                            <p class="text-muted small mb-4">Tentukan bahan baku yang digunakan. Stok akan berkurang otomatis saat menu terjual.</p>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 60%">Bahan Baku</th>
                                            <th style="width: 30%">Jumlah /Qty</th>
                                            <th style="width: 10%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resep-body">
                                        <tr class="resep-row">
                                            <td>
                                                <select name="id_stok[]" class="form-control form-control-sm select-bahan" required>
                                                    <option value="">-- Pilih Bahan --</option>
                                                    @foreach($stok_items as $item)
                                                        <option value="{{ $item->id_stok }}">{{ $item->nama_stok }} (Tersedia: {{ $item->stok }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="jumlah_resep[]" class="form-control text-center" step="0.01" min="0.01" required>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" style="display:none;">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-danger small mt-3">
                                @if($errors->has('id_stok.*') || $errors->has('jumlah_resep.*'))
                                    <i class="mdi mdi-alert-circle"></i> Ada kesalahan pada data resep. Pastikan semua kolom terisi.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 d-flex justify-content-center">
                    <a href="/menu" class="btn btn-light me-3 px-4">Batal</a>
                    <button type="submit" class="btn btn-gradient-primary btn-icon-text px-5">
                        <i class="mdi mdi-checkbox-marked-circle-outline btn-icon-prepend"></i> Simpan Menu & Resep
                    </button>
                </div>
            </div>
        </form>
    </div>

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const body = document.getElementById('resep-body');
                const btnAdd = document.getElementById('btn-add-bahan');

                // Add row
                btnAdd.addEventListener('click', function () {
                    const firstRow = body.querySelector('.resep-row');
                    const newRow = firstRow.cloneNode(true);

                    // Clear inputs
                    newRow.querySelector('select').selectedIndex = 0;
                    newRow.querySelector('input').value = '';
                    newRow.querySelector('.btn-remove-row').style.display = 'inline-block';

                    body.appendChild(newRow);
                    updateRemoveButtons();
                });

                // Remove row event delegation
                body.addEventListener('click', function (e) {
                    if (e.target.closest('.btn-remove-row')) {
                        const row = e.target.closest('tr');
                        const rows = body.querySelectorAll('.resep-row');
                        if (rows.length > 1) {
                            row.remove();
                            updateRemoveButtons();
                        }
                    }
                });

                function updateRemoveButtons() {
                    const rows = body.querySelectorAll('.resep-row');
                    const btns = body.querySelectorAll('.btn-remove-row');
                    if (rows.length > 1) {
                        btns.forEach(btn => btn.style.display = 'inline-block');
                    } else {
                        btns.forEach(btn => btn.style.display = 'none');
                    }
                }
                
                updateRemoveButtons();
            });
        </script>
    @endsection
@endsection
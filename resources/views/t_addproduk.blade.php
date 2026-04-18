@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/resep.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-table-large menu-icon"></i>
                </span> Tambah Resep Menu
            </h3>
        </div>

        <form action="/resep/insert" method="POST">
            @csrf
            <div class="row">
                <!-- Column 1: Basic Info -->
                <div class="col-md-5 mb-4" data-aos="fade-up" data-aos-duration="600">
                    <div class="resep-info-card">
                        <div class="card-body">
                            <div class="resep-card-title-row">
                                <div class="title-icon">
                                    <i class="mdi mdi-information-outline"></i>
                                </div>
                                <h4>Informasi Menu</h4>
                            </div>

                            <div class="resep-form-group">
                                <label>ID Menu (Manual)</label>
                                <input name="id_produk" class="resep-input" value="{{ old('id_produk') }}"
                                    placeholder="Contoh: 2001" required>
                                <div class="resep-error">@error('id_produk') {{ $message }} @enderror</div>
                            </div>

                            <div class="resep-form-group">
                                <label>Nama Menu</label>
                                <input name="nama_produk" class="resep-input" value="{{ old('nama_produk') }}"
                                    placeholder="Contoh: Indomie Goreng Spesial" required>
                                <div class="resep-error">@error('nama_produk') {{ $message }} @enderror</div>
                            </div>

                            <div class="resep-form-group">
                                <label>Harga Jual</label>
                                <div class="resep-input-group">
                                    <span class="resep-input-prefix">Rp</span>
                                    <input name="harga_jual" class="resep-input" value="{{ old('harga_jual') }}"
                                        placeholder="Contoh: 15000" required>
                                </div>
                                <div class="resep-error">@error('harga_jual') {{ $message }} @enderror</div>
                            </div>

                            <div class="resep-form-group">
                                <label>Kategori Menu</label>
                                <select name="kategori" class="resep-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Makanan" {{ old('kategori') == 'Makanan' ? 'selected' : '' }}>Makanan
                                    </option>
                                    <option value="Minuman" {{ old('kategori') == 'Minuman' ? 'selected' : '' }}>Minuman
                                    </option>
                                </select>
                                <div class="resep-error">@error('kategori') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Ingredients (Resep) -->
                <div class="col-md-7 mb-4" data-aos="fade-up" data-aos-duration="600" data-aos-delay="150">
                    <div class="resep-bahan-card">
                        <div class="card-body">
                            <div class="resep-bahan-header">
                                <div class="resep-card-title-row" style="margin-bottom: 0;">
                                    <div class="title-icon" style="background: linear-gradient(135deg, var(--success-color), #4facfe);">
                                        <i class="mdi mdi-flask-outline"></i>
                                    </div>
                                    <h4>Resep (Bahan Baku)</h4>
                                </div>
                                <button type="button" class="resep-btn-add-bahan" id="btn-add-bahan">
                                    <i class="mdi mdi-plus"></i> Tambah Bahan
                                </button>
                            </div>
                            <p class="resep-bahan-desc">Tentukan bahan baku yang digunakan untuk membuat menu ini.
                                Setiap penggunaan akan mengurangi stok bahan otomatis saat terjual.</p>

                            <div class="table-responsive">
                                <table class="resep-add-table" id="table-resep">
                                    <thead>
                                        <tr>
                                            <th>Bahan Baku</th>
                                            <th style="width: 30%">Jumlah</th>
                                            <th style="width: 50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="resep-body">
                                        <tr class="resep-row">
                                            <td>
                                                <select name="id_stok[]" class="resep-select select-bahan" required>
                                                    <option value="">-- Pilih Bahan --</option>
                                                    @foreach($stok_items as $item)
                                                        <option value="{{ $item->id_stok }}">{{ $item->nama_stok }} (ID:
                                                            {{ $item->id_stok }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="jumlah_resep[]" class="resep-input"
                                                    step="0.01" min="0.01" required>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="resep-btn-remove-row btn-remove-row"
                                                    style="display:none;"><i class="mdi mdi-delete"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @if($errors->has('id_stok.*') || $errors->has('jumlah_resep.*') || $errors->has('id_resep.*'))
                                <div class="resep-error-box">
                                    <i class="mdi mdi-alert-circle"></i> Ada kesalahan pada data resep. Pastikan semua kolom terisi dan unik.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="resep-actions-bar">
                        <a href="/menu" class="resep-btn-cancel">
                            <i class="mdi mdi-close"></i> Batal
                        </a>
                        <button type="submit" class="resep-btn-save">
                            <i class="mdi mdi-content-save"></i> Simpan Menu & Resep
                        </button>
                    </div>
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
                    const lastRow = body.querySelector('.resep-row');
                    const newRow = lastRow.cloneNode(true);

                    // Clear inputs
                    newRow.querySelectorAll('input').forEach(input => input.value = '');
                    newRow.querySelector('select').selectedIndex = 0;
                    newRow.querySelector('.btn-remove-row').style.display = 'inline-block';

                    body.appendChild(newRow);
                    updateRemoveButtons();
                });

                // Remove row
                body.addEventListener('click', function (e) {
                    if (e.target.closest('.btn-remove-row')) {
                        const row = e.target.closest('tr');
                        row.remove();
                        updateRemoveButtons();
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
            });
        </script>
    @endsection

@endsection

@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-table-large menu-icon"></i>
            </span> Add Produk
          </h3>
        </div>
        <br><br>
        {{-- add produk --}}
        <form action="/produk/insert" method="POST">
            @csrf
            <div class="row">
                <!-- Column 1: Basic Info -->
                <div class="col-md-5">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary"><i class="mdi mdi-information-outline"></i> Informasi Menu</h4>
                            <hr>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">ID Menu (Manual)</label>
                                <input name="id_produk" class="form-control" value="{{ old('id_produk') }}" placeholder="Contoh: 2001" required>
                                <div class="text-danger small">@error('id_produk') {{ $message }} @enderror</div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Nama Menu</label>
                                <input name="nama_produk" class="form-control" value="{{ old('nama_produk') }}" placeholder="Contoh: Indomie Goreng Spesial" required>
                                <div class="text-danger small">@error('nama_produk') {{ $message }} @enderror</div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input name="harga_jual" class="form-control" value="{{ old('harga_jual') }}" placeholder="Contoh: 15000" required>
                                </div>
                                <div class="text-danger small">@error('harga_jual') {{ $message }} @enderror</div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Kategori Menu</label>
                                <select name="kategori" class="form-control" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Makanan" {{ old('kategori') == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                                    <option value="Minuman" {{ old('kategori') == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                                    <option value="Sembako" {{ old('kategori') == 'Sembako' ? 'selected' : '' }}>Sembako</option>
                                    <option value="Bumbu" {{ old('kategori') == 'Bumbu' ? 'selected' : '' }}>Bumbu</option>
                                </select>
                                <div class="text-danger small">@error('kategori') {{ $message }} @enderror</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Ingredients (Resep) -->
                <div class="col-md-7">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title text-success mb-0"><i class="mdi mdi-flask-outline"></i> Resep (Bahan Baku)</h4>
                                <button type="button" class="btn btn-sm btn-inverse-success" id="btn-add-bahan"><i class="mdi mdi-plus"></i> Tambah Bahan</button>
                            </div>
                            <hr>
                            <p class="text-muted small mb-4">Tentukan bahan baku yang digunakan untuk membuat menu ini. Setiap penggunaan akan mengurangi stok bahan otomatis saat terjual.</p>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="table-resep">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Bahan Baku</th>
                                            <th style="width: 30%">Jumlah</th>
                                            <th style="width: 50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="resep-body">
                                        <tr class="resep-row">
                                            <td>
                                                <select name="id_stok[]" class="form-control form-control-sm select-bahan" required>
                                                    <option value="">-- Pilih Bahan --</option>
                                                    @foreach($stok_items as $item)
                                                        <option value="{{ $item->id_stok }}">{{ $item->nama_stok }} (ID: {{ $item->id_stok }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="jumlah_resep[]" class="form-control form-control-sm" step="0.01" min="0.01" required>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-inverse-danger btn-remove-row" style="display:none;"><i class="mdi mdi-delete"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-danger small mt-2">
                                @if($errors->has('id_stok.*') || $errors->has('jumlah_resep.*') || $errors->has('id_resep.*'))
                                    Ada kesalahan pada data resep. Pastikan semua kolom terisi dan unik.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12 text-center">
                    <hr>
                    <a href="/produk" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn bg-gradient-info text-white px-5">Simpan Menu & Resep</button>
                </div>
            </div>
        </form>
    </div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const body = document.getElementById('resep-body');
        const btnAdd = document.getElementById('btn-add-bahan');

        // Add row
        btnAdd.addEventListener('click', function() {
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
        body.addEventListener('click', function(e) {
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
        
       
      </div>

      
    
@endsection

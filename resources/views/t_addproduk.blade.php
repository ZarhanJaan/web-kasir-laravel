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

        <form action="/resep/insert" method="POST" id="form-tambah-menu">
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

                            {{-- Kategori dipilih DULU agar ID bisa di-generate otomatis --}}
                            <div class="resep-form-group">
                                <label>Kategori Menu</label>
                                <select name="kategori" id="select-kategori" class="resep-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($kategori_list as $kat)
                                        <option value="{{ $kat }}" {{ old('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                    @endforeach
                                </select>
                                <div class="resep-error">@error('kategori') {{ $message }} @enderror</div>
                            </div>

                            {{-- Hidden field aktual yang dikirim ke server --}}
                            <input type="hidden" name="id_produk" id="input-id-produk">

                            <div class="resep-form-group">
                                <label>
                                    ID Menu
                                    <span id="id-hint" class="id-hint-badge" style="display:none;"></span>
                                </label>

                                {{-- Input terbagi: prefix terkunci + suffix editable --}}
                                <div class="id-split-wrapper" id="id-split-wrapper">
                                    <span class="id-prefix-locked" id="id-prefix-locked" title="Angka prefix dikunci sesuai kategori">
                                        <i class="mdi mdi-lock" style="font-size:11px;"></i>
                                        <span id="prefix-display">–</span>
                                    </span>
                                    <input type="text"
                                        id="input-id-suffix"
                                        class="id-suffix-input"
                                        maxlength="5"
                                        placeholder="pilih kategori"
                                        disabled
                                        autocomplete="off">
                                    <span id="id-loading" style="padding-right:10px; display:none; color:#888;">
                                        <i class="mdi mdi-loading mdi-spin"></i>
                                    </span>
                                </div>

                                <div class="resep-error">@error('id_produk') {{ $message }} @enderror</div>
                                <small id="id-full-preview" style="display:none; margin-top:5px; font-size:12px; color:#6366f1; font-weight:600;">
                                    <i class="mdi mdi-identifier"></i> ID lengkap: <span id="id-full-value"></span>
                                </small>
                                <div id="id-suggestion-box" class="id-suggestion-box" style="display:none; margin-top:8px;">
                                    <i class="mdi mdi-lightbulb-outline"></i>
                                    Saran ID berikutnya: <strong id="id-suggestion-value"></strong>
                                </div>
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

                            {{-- Tanggal Input Otomatis (read-only, info saja) --}}
                            <div class="resep-form-group">
                                <label>
                                    <i class="mdi mdi-calendar-clock" style="color: var(--primary-color);"></i>
                                    Tanggal Input (Otomatis)
                                </label>
                                <input class="resep-input"
                                    id="display-created-at"
                                    value="{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY — HH:mm') }}"
                                    readonly
                                    style="background: rgba(99,102,241,0.07); color: #6366f1; font-weight: 600; cursor: not-allowed;">
                                <small style="color: #94a3b8; font-size: 11px; margin-top: 4px; display:block;">
                                    <i class="mdi mdi-information-outline"></i>
                                    Waktu pembuatan diisi otomatis oleh sistem.
                                </small>
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

    <style>
        /* ── Badge hint prefix ── */
        .id-hint-badge {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            margin-left: 8px;
            vertical-align: middle;
            letter-spacing: 0.5px;
        }

        /* ── Split input: prefix terkunci + suffix editable ── */
        .id-split-wrapper {
            display: flex;
            align-items: center;
            border: 1.5px solid rgba(99,102,241,0.3);
            border-radius: 10px;
            overflow: hidden;
            background: var(--card-bg, #1e293b);
            transition: border-color 0.2s;
        }
        .id-split-wrapper:focus-within {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }
        .id-prefix-locked {
            display: flex;
            align-items: center;
            gap: 5px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            font-weight: 800;
            font-size: 20px;
            padding: 10px 14px;
            user-select: none;
            white-space: nowrap;
            min-width: 52px;
            justify-content: center;
            cursor: not-allowed;
            border-right: 2px solid rgba(255,255,255,0.15);
        }
        .id-prefix-locked i.mdi-lock {
            opacity: 0.7;
        }
        .id-suffix-input {
            flex: 1;
            border: none !important;
            outline: none !important;
            background: transparent !important;
            color: inherit;
            font-size: 18px;
            font-weight: 700;
            padding: 10px 12px;
            letter-spacing: 2px;
            width: 100%;
            box-shadow: none !important;
        }
        .id-suffix-input:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        .id-suffix-input::placeholder {
            font-size: 12px;
            font-weight: 400;
            letter-spacing: 0;
            opacity: 0.5;
        }

        /* ── Kotak saran ID berikutnya ── */
        .id-suggestion-box {
            background: linear-gradient(135deg, rgba(99,102,241,0.10), rgba(139,92,246,0.07));
            border: 1px dashed #6366f1;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 12.5px;
            color: #6366f1;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .id-suggestion-box strong {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #8b5cf6;
        }
    </style>

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const resepBody    = document.getElementById('resep-body');
                const btnAdd       = document.getElementById('btn-add-bahan');
                const selectKat    = document.getElementById('select-kategori');
                const hiddenId     = document.getElementById('input-id-produk');
                const suffixInput  = document.getElementById('input-id-suffix');
                const prefixDisplay= document.getElementById('prefix-display');
                const idHint       = document.getElementById('id-hint');
                const idLoading    = document.getElementById('id-loading');
                const fullPreview  = document.getElementById('id-full-preview');
                const fullValue    = document.getElementById('id-full-value');
                const suggestionBox= document.getElementById('id-suggestion-box');
                const suggestionVal= document.getElementById('id-suggestion-value');
                const form         = document.getElementById('form-tambah-menu');

                let currentPrefix = '';

                // ── Update hidden field & preview setiap suffix berubah ──
                suffixInput.addEventListener('input', function () {
                    // Hanya angka yang boleh masuk
                    this.value = this.value.replace(/[^0-9]/g, '');

                    const combined = currentPrefix + this.value;
                    hiddenId.value = combined;

                    if (currentPrefix && this.value.length > 0) {
                        fullValue.textContent = combined;
                        fullPreview.style.display = 'block';
                    } else {
                        fullPreview.style.display = 'none';
                    }

                    // Sembunyikan saran jika user sudah mengubah suffix dari saran
                    if (suggestionVal.textContent && this.value !== suggestionVal.textContent.slice(currentPrefix.length)) {
                        suggestionBox.style.display = 'none';
                    }
                });

                // ── Auto-generate ID saat kategori dipilih ──
                selectKat.addEventListener('change', function () {
                    const kat = this.value;

                    if (!kat) {
                        idHint.style.display = 'none';
                        fullPreview.style.display = 'none';
                        suggestionBox.style.display = 'none';
                        prefixDisplay.textContent = '–';
                        currentPrefix = '';
                        suffixInput.value = '';
                        suffixInput.disabled = true;
                        suffixInput.placeholder = 'pilih kategori';
                        hiddenId.value = '';
                        return;
                    }

                    idLoading.style.display = 'inline';
                    suffixInput.disabled = true;

                    fetch(`/menu/get-next-id?kategori=${encodeURIComponent(kat)}`)
                        .then(r => r.json())
                        .then(data => {
                            idLoading.style.display = 'none';

                            if (data.success) {
                                currentPrefix = data.prefix;
                                const nextId  = data.next_id;                 // mis. "1001"
                                const suffix  = nextId.slice(currentPrefix.length); // mis. "001"

                                // Tampilkan prefix terkunci
                                prefixDisplay.textContent = currentPrefix;

                                // Isi suffix otomatis
                                suffixInput.value = suffix;
                                suffixInput.disabled = false;
                                suffixInput.placeholder = '001';

                                // Update hidden field
                                hiddenId.value = nextId;

                                // Badge & preview
                                idHint.textContent = `Prefix: ${currentPrefix}`;
                                idHint.style.display = 'inline-block';

                                fullValue.textContent = nextId;
                                fullPreview.style.display = 'block';

                                // Tampilkan kotak saran ID lengkap
                                suggestionVal.textContent = nextId;
                                suggestionBox.style.display = 'flex';
                            }
                        })
                        .catch(() => {
                            idLoading.style.display = 'none';
                        });
                });

                // Jika ada old value kategori saat validasi gagal, trigger ulang
                if (selectKat.value) {
                    selectKat.dispatchEvent(new Event('change'));
                }

                // ── Validasi sebelum submit ──
                form.addEventListener('submit', function (e) {
                    if (!currentPrefix || !suffixInput.value) {
                        e.preventDefault();
                        alert('Silakan pilih kategori terlebih dahulu agar ID Menu dapat dibuat.');
                        return;
                    }
                    // Pastikan hidden field terisi
                    hiddenId.value = currentPrefix + suffixInput.value;
                });

                // ── Tambah baris resep ──
                btnAdd.addEventListener('click', function () {
                    const lastRow = resepBody.querySelector('.resep-row');
                    const newRow  = lastRow.cloneNode(true);

                    newRow.querySelectorAll('input').forEach(input => input.value = '');
                    newRow.querySelector('select').selectedIndex = 0;
                    newRow.querySelector('.btn-remove-row').style.display = 'inline-block';

                    resepBody.appendChild(newRow);
                    updateRemoveButtons();
                });

                // ── Hapus baris resep ──
                resepBody.addEventListener('click', function (e) {
                    if (e.target.closest('.btn-remove-row')) {
                        e.target.closest('tr').remove();
                        updateRemoveButtons();
                    }
                });

                function updateRemoveButtons() {
                    const rows = resepBody.querySelectorAll('.resep-row');
                    const btns = resepBody.querySelectorAll('.btn-remove-row');
                    btns.forEach(btn => btn.style.display = rows.length > 1 ? 'inline-block' : 'none');
                }
            });
        </script>
    @endsection

@endsection
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/kasir.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-success text-white me-2">
                    <i class="mdi mdi-calculator menu-icon"></i>
                </span> Kasir (Point of Sales)
            </h3>
        </div>
        <br>

        <div class="row">

            {{-- ===== LEFT: Form Detail Pesanan ===== --}}
            <div class="col-md-8 grid-margin stretch-card">
                <div class="pos-form-card">
                    <form id="pos-form" action="/kasir/insert" method="POST">
                        @csrf

                        {{-- Card Title --}}
                        <div class="pos-card-title-row">
                            <div class="pos-title-icon">
                                <i class="mdi mdi-cart-outline"></i>
                            </div>
                            <h4>Detail Pesanan</h4>
                        </div>
                        <p class="pos-card-description">Isi informasi transaksi dan daftar item belanja pelanggan.</p>
                        <hr class="pos-divider">

                        {{-- Tanggal Transaksi --}}
                        <div class="pos-form-group">
                            <label>Tgl. Transaksi</label>
                            <div class="pos-form-col">
                                <input type="date" name="tanggal"
                                    class="pos-input"
                                    value="{{ date('Y-m-d') }}"
                                    required readonly>
                            </div>
                        </div>

                        {{-- Nama Pelanggan --}}
                        <div class="pos-form-group">
                            <label>Nama Pelanggan / Kasir</label>
                            <div class="pos-form-col">
                                <input type="text" name="nama_pelanggan" id="nama_pelanggan"
                                    class="pos-input"
                                    value="Pelanggan Umum" required>
                            </div>
                        </div>

                        {{-- Metode Pembayaran --}}
                        <div class="pos-form-group">
                            <label>Metode Pembayaran</label>
                            <div class="pos-form-col">
                                <select name="metode_pembayaran" id="metode_pembayaran"
                                    class="pos-select" required>
                                    <option value="Cash" selected>Cash / Tunai</option>
                                    <option value="Qris">QRIS / E-Wallet</option>
                                </select>
                                <button type="button" class="pos-btn-qris" id="btn-show-qris"
                                    style="display:none;"
                                    data-bs-toggle="modal" data-bs-target="#qrisModal">
                                    <i class="mdi mdi-qrcode-scan"></i> Lihat QRIS
                                </button>
                            </div>
                        </div>

                        {{-- ID Transaksi --}}
                        <div class="pos-form-group">
                            <label>ID Transaksi (Manual)</label>
                            <div class="pos-form-col">
                                <input type="number" name="id_penjualan" id="id_penjualan"
                                    class="pos-input"
                                    placeholder="Contoh: 10001" required>
                            </div>
                        </div>

                        {{-- Daftar Belanja --}}
                        <p class="pos-section-title">
                            <i class="mdi mdi-format-list-bulleted"></i> Daftar Belanja
                        </p>

                        <div id="pos-keranjang">
                            <div class="pos-item-row">
                                <select name="id_produk[]" class="pos-select produk-select" required>
                                    <option value="" data-harga="0">-- Pilih Produk --</option>
                                    @foreach($produk as $p)
                                        <option value="{{ $p->id_produk }}"
                                            data-harga="{{ $p->harga_jual }}"
                                            data-nama="{{ $p->nama_produk }}"
                                            {{ $p->is_available ? '' : 'disabled' }}
                                            style="{{ $p->is_available ? '' : 'color: #ff6b6b;' }}">
                                            {{ $p->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="number" name="jumlah_barang[]"
                                    class="pos-input jumlah-input"
                                    value="1" min="1" placeholder="Qty" required>
                                <span class="pos-subtotal-label subtotal-label">Rp 0</span>
                                <button type="button" class="pos-btn-remove remove-item"
                                    style="display:none;">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </div>
                        </div>

                        <button type="button" class="pos-btn-add-item" id="add-item">
                            <i class="mdi mdi-plus"></i> Tambah Item
                        </button>

                        {{-- Hidden total input --}}
                        <input type="hidden" name="total" id="total_input" value="0">
                    </form>
                </div>
            </div>

            {{-- ===== RIGHT: Summary Tagihan ===== --}}
            <div class="col-md-4 grid-margin stretch-card">
                <div class="pos-summary-card">
                    <div class="card-body">
                        <p class="pos-summary-label">
                            <i class="mdi mdi-receipt"></i> Total Tagihan
                        </p>
                        <div class="pos-grand-total" id="grand-total-display">Rp 0</div>
                        <button type="button" class="pos-btn-bayar" id="btn-verifikasi">
                            <i class="mdi mdi-credit-card-outline"></i> BAYAR SEKARANG
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== Modal QRIS ===== --}}
    <div class="modal fade" id="qrisModal" tabindex="-1" aria-labelledby="qrisModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header pos-modal-header-qris">
                    <h5 class="modal-title" id="qrisModalLabel">
                        <i class="mdi mdi-qrcode-scan"></i> Scan QRIS
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if(isset($qris_image) && file_exists(public_path($qris_image)))
                        @if(isset($qris_name))
                            <p class="pos-qris-name">{{ $qris_name }}</p>
                        @endif
                        <img src="{{ asset($qris_image) }}" alt="QRIS" class="img-fluid pos-qris-img">
                        <p class="pos-qris-caption">Silahkan scan QR code di atas untuk melakukan pembayaran.</p>
                    @else
                        <div class="mu-alert-danger" style="border-radius:14px; padding:16px; text-align:left;">
                            <i class="mdi mdi-alert"></i> Gambar QRIS belum dikonfigurasi di menu Setting.
                        </div>
                    @endif
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="pos-modal-btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close"></i> Tutup
                    </button>
                    <button type="button" class="pos-modal-btn-teal" id="btn-submit-qris" style="display:none;">
                        <i class="mdi mdi-check-circle"></i> Selesai Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Modal Verifikasi ===== --}}
    <div class="modal fade" id="verifikasiModal" tabindex="-1" aria-labelledby="verifikasiModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verifikasiModalLabel">
                        <i class="mdi mdi-check-circle"></i> Konfirmasi Pembayaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="pos-v-info-label">Pelanggan / Kasir</p>
                    <p class="pos-v-info-value" id="v-pelanggan"></p>

                    <p class="pos-v-info-label" style="margin-top:12px;">Metode Pembayaran</p>
                    <p class="pos-v-info-value" id="v-metode"></p>

                    <hr class="pos-v-divider">
                    <p class="pos-v-info-label">Detail Pesanan:</p>
                    <ul id="v-items" class="list-group mb-2" style="list-style:none; padding:0;">
                        {{-- Items appended via JS --}}
                    </ul>
                    <div class="pos-v-total">Total: <span id="v-total"></span></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="pos-modal-btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close"></i> Kembali (Batal)
                    </button>
                    <button type="button" class="pos-modal-btn-primary" id="btn-submit-form">
                        <i class="mdi mdi-content-save"></i> Lanjutkan Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Modal Sukses Pembayaran ===== --}}
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header pos-modal-header-success">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="padding: 10px 32px 32px !important;">
                    <i class="mdi mdi-check-circle-outline pos-success-icon"></i>
                    <h2 class="pos-success-title">Pembayaran Berhasil!</h2>
                    <p class="pos-success-desc">Transaksi telah berhasil diproses dan dicatat dalam sistem.</p>
                </div>
                <div class="modal-footer border-0 justify-content-end">
                    <button type="button" class="pos-modal-btn-success" id="btn-cetak-struk-modal">
                        <i class="mdi mdi-printer"></i> Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const keranjang = document.getElementById('pos-keranjang');
            const grandTotalDisplay = document.getElementById('grand-total-display');
            const totalInput = document.getElementById('total_input');
            const btnAdd = document.getElementById('add-item');

            const metodePembayaran = document.getElementById('metode_pembayaran');
            const btnShowQris = document.getElementById('btn-show-qris');

            metodePembayaran.addEventListener('change', function() {
                btnShowQris.style.display = this.value === 'Qris' ? 'inline-flex' : 'none';
            });

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }

            function hitungTotal() {
                let total = 0;
                document.querySelectorAll('.pos-item-row').forEach(row => {
                    const select = row.querySelector('.produk-select');
                    const qtyInput = row.querySelector('.jumlah-input');
                    const subtotalLabel = row.querySelector('.subtotal-label');
                    const harga = parseFloat(select.options[select.selectedIndex].getAttribute('data-harga')) || 0;
                    const qty = parseInt(qtyInput.value) || 0;
                    const subtotal = harga * qty;
                    subtotalLabel.textContent = formatRupiah(subtotal);
                    total += subtotal;
                });
                grandTotalDisplay.textContent = formatRupiah(total);
                totalInput.value = total;
            }

            // Event delegation for dynamically added rows
            keranjang.addEventListener('change', function (e) {
                if (e.target.classList.contains('produk-select') || e.target.classList.contains('jumlah-input')) {
                    hitungTotal();
                }
            });
            keranjang.addEventListener('keyup', function (e) {
                if (e.target.classList.contains('jumlah-input')) hitungTotal();
            });

            // Add Row
            btnAdd.addEventListener('click', function () {
                const firstRow = document.querySelector('.pos-item-row');
                const clone = firstRow.cloneNode(true);
                clone.querySelector('.produk-select').selectedIndex = 0;
                clone.querySelector('.jumlah-input').value = 1;
                clone.querySelector('.subtotal-label').textContent = 'Rp 0';
                clone.querySelector('.remove-item').style.display = 'inline-flex';

                clone.querySelector('.remove-item').addEventListener('click', function () {
                    clone.remove();
                    hitungTotal();
                    updateRemoveButtons();
                });

                keranjang.appendChild(clone);
                updateRemoveButtons();
            });

            function updateRemoveButtons() {
                const btns = document.querySelectorAll('.remove-item');
                btns.forEach(btn => btn.style.display = btns.length > 1 ? 'inline-flex' : 'none');
            }

            // Verification Modal
            const btnVerifikasi = document.getElementById('btn-verifikasi');
            const posForm = document.getElementById('pos-form');
            const verifikasiModal = new bootstrap.Modal(document.getElementById('verifikasiModal'));

            btnVerifikasi.addEventListener('click', function () {
                if (posForm.reportValidity()) {
                    document.getElementById('v-pelanggan').textContent = document.getElementById('nama_pelanggan').value || 'Tanpa Nama';
                    document.getElementById('v-metode').textContent = document.getElementById('metode_pembayaran').value;
                    document.getElementById('v-total').textContent = formatRupiah(totalInput.value);

                    const vItems = document.getElementById('v-items');
                    vItems.innerHTML = '';
                    document.querySelectorAll('.pos-item-row').forEach(row => {
                        const select = row.querySelector('.produk-select');
                        const qty = row.querySelector('.jumlah-input').value;
                        if (select.selectedIndex > 0 && qty > 0) {
                            const nama = select.options[select.selectedIndex].getAttribute('data-nama');
                            const harga = select.options[select.selectedIndex].getAttribute('data-harga');
                            const li = document.createElement('li');
                            li.className = 'list-group-item d-flex justify-content-between align-items-center';
                            li.innerHTML = `${nama} x${qty} <span>${formatRupiah(harga * qty)}</span>`;
                            vItems.appendChild(li);
                        }
                    });
                    verifikasiModal.show();
                }
            });

            // Submit Logic
            document.getElementById('btn-submit-form').addEventListener('click', function () {
                if (totalInput.value == 0) {
                    alert('Belum ada produk yang dipilih!');
                    return;
                }
                if (document.getElementById('metode_pembayaran').value === 'Qris') {
                    const vModal = bootstrap.Modal.getInstance(document.getElementById('verifikasiModal'));
                    if (vModal) vModal.hide();
                    document.getElementById('btn-submit-qris').style.display = 'inline-flex';
                    const qrisModal = new bootstrap.Modal(document.getElementById('qrisModal'));
                    qrisModal.show();
                } else {
                    posForm.submit();
                }
            });

            document.getElementById('btn-submit-qris').addEventListener('click', function () {
                posForm.submit();
            });
        });
    </script>

    @if(session('pesan_sukses_trx'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();

                document.getElementById('btn-cetak-struk-modal').addEventListener('click', function () {
                    const trxId = "{{ session('pesan_sukses_trx') }}";
                    const width = 400, height = 600;
                    const left = (screen.width / 2) - (width / 2);
                    const top  = (screen.height / 2) - (height / 2);
                    window.open('/struk/' + trxId, 'Struk Pembayaran', `width=${width},height=${height},top=${top},left=${left}`);
                });
            });
        </script>
    @endif

@endsection
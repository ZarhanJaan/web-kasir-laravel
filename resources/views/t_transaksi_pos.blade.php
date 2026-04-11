@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-success text-white me-2">
                <i class="mdi mdi-calculator menu-icon"></i>
            </span> Kasir (Point of Sales)
        </h3>
    </div>
    <br>

    @if (session('pesan_error'))
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i> {{ session('pesan_error') }}
    </div>
    @endif

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card shadow p-4 mb-5 bg-body rounded">
                <form id="pos-form" action="/pos/insert" method="POST">
                    @csrf
                    <h4 class="card-title text-primary"><i class="mdi mdi-cart-outline"></i> Detail Pesanan</h4>
                    <hr>
                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Tgl. Transaksi</label>
                        <div class="col-sm-9">
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required readonly>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Nama Pelanggan / Kasir</label>
                        <div class="col-sm-9">
                            <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control" value="Pelanggan Umum" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Metode Pembayaran</label>
                        <div class="col-sm-9">
                            <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                                <option value="Cash">Cash / Tunai</option>
                                <option value="Qris">QRIS / E-Wallet</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3">Daftar Belanja</h5>
                    <div id="keranjang">
                        <div class="row mb-2 item-row">
                            <div class="col-6">
                                <select name="id_produk[]" class="form-control produk-select" required>
                                    <option value="" data-harga="0">-- Pilih Produk --</option>
                                    @foreach($produk as $p)
                                        <option value="{{ $p->id_produk }}" data-harga="{{ $p->harga_jual }}" data-nama="{{ $p->nama_produk }}">
                                            {{ $p->nama_produk }} (Stok: {{ $p->stok }} | Rp{{ number_format($p->harga_jual,0,',','.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="number" name="jumlah_barang[]" class="form-control jumlah-input" value="1" min="1" placeholder="Qty" required>
                            </div>
                            <div class="col-3 text-right">
                                <span class="subtotal-label font-weight-bold" style="line-height:2.5;">Rp 0</span>
                                <button type="button" class="btn btn-sm btn-danger remove-item float-right ml-2" style="display:none;"><i class="mdi mdi-delete"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-inverse-success btn-sm mt-2" id="add-item"><i class="mdi mdi-plus"></i> Tambah Item</button>
                    
                    <!-- Hidden auto generated values -->
                    <input type="hidden" name="id_penjualan" value="{{ rand(10000,99999) }}">
                    <input type="hidden" name="total" id="total_input" value="0">
                </form>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card bg-gradient-info text-white shadow p-4 mb-5 rounded" style="max-height: 250px;">
                <div class="card-body">
                    <h4 class="font-weight-normal mb-3">Total Tagihan</h4>
                    <h2 class="mb-5" style="font-size: 2.5rem;" id="grand-total-display">Rp 0</h2>
                    <button type="button" class="btn btn-warning btn-lg btn-block text-dark mt-4 shadow font-weight-bold" id="btn-verifikasi" data-bs-toggle="modal" data-bs-target="#verifikasiModal">BAYAR SEKARANG</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Verifikasi -->
<div class="modal fade" id="verifikasiModal" tabindex="-1" aria-labelledby="verifikasiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title" id="verifikasiModalLabel"><i class="mdi mdi-check-circle"></i> Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="mb-3">Informasi Pelanggan: <span id="v-pelanggan" class="text-primary font-weight-bold"></span></h6>
                <p>Metode Pembayaran: <strong id="v-metode"></strong></p>
                <hr>
                <h6>Detail Pesanan:</h6>
                <ul id="v-items" class="list-group mb-3">
                    <!-- Items appended via JS -->
                </ul>
                <h4 class="text-right mt-3 text-danger">Total: <span id="v-total"></span></h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="mdi mdi-close"></i> Kembali (Batal)</button>
                <button type="button" class="btn btn-success" id="btn-submit-form"><i class="mdi mdi-content-save"></i> Lanjutkan Pembayaran</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const keranjang = document.getElementById('keranjang');
        const grandTotalDisplay = document.getElementById('grand-total-display');
        const totalInput = document.getElementById('total_input');
        const btnAdd = document.getElementById('add-item');

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }

        function hitungTotal() {
            let total = 0;
            const rows = document.querySelectorAll('.item-row');
            rows.forEach(row => {
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
        keranjang.addEventListener('change', function(e) {
            if(e.target.classList.contains('produk-select') || e.target.classList.contains('jumlah-input')) {
                hitungTotal();
            }
        });
        keranjang.addEventListener('keyup', function(e) {
            if(e.target.classList.contains('jumlah-input')) {
                hitungTotal();
            }
        });

        // Add Row
        btnAdd.addEventListener('click', function() {
            const firstRow = document.querySelector('.item-row');
            const clone = firstRow.cloneNode(true);
            clone.querySelector('.produk-select').selectedIndex = 0;
            clone.querySelector('.jumlah-input').value = 1;
            clone.querySelector('.subtotal-label').textContent = 'Rp 0';
            clone.querySelector('.remove-item').style.display = 'inline-block';
            
            // Add remove event
            clone.querySelector('.remove-item').addEventListener('click', function() {
                clone.remove();
                hitungTotal();
                updateRemoveButtons();
            });
            
            keranjang.appendChild(clone);
            updateRemoveButtons();
        });

        function updateRemoveButtons() {
            const btns = document.querySelectorAll('.remove-item');
            if(btns.length > 1) {
                btns.forEach(btn => btn.style.display = 'inline-block');
            } else {
                btns[0].style.display = 'none';
            }
        }

        // Verification Modal Logic
        const btnVerifikasi = document.getElementById('btn-verifikasi');
        btnVerifikasi.addEventListener('click', function() {
            document.getElementById('v-pelanggan').textContent = document.getElementById('nama_pelanggan').value || 'Tanpa Nama';
            document.getElementById('v-metode').textContent = document.getElementById('metode_pembayaran').value;
            document.getElementById('v-total').textContent = formatRupiah(totalInput.value);
            
            const vItems = document.getElementById('v-items');
            vItems.innerHTML = '';
            
            const rows = document.querySelectorAll('.item-row');
            rows.forEach(row => {
                const select = row.querySelector('.produk-select');
                const qty = row.querySelector('.jumlah-input').value;
                if(select.selectedIndex > 0 && qty > 0) {
                    const nama = select.options[select.selectedIndex].getAttribute('data-nama');
                    const harga = select.options[select.selectedIndex].getAttribute('data-harga');
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                    li.innerHTML = `${nama} x${qty} <span>${formatRupiah(harga * qty)}</span>`;
                    vItems.appendChild(li);
                }
            });
        });

        // Submit Logic Form
        document.getElementById('btn-submit-form').addEventListener('click', function() {
            if(totalInput.value == 0) {
                alert('Belum ada produk yang dipilih!');
            } else {
                document.getElementById('pos-form').submit();
            }
        });
    });
</script>

@if(session('pesan_sukses_trx'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show success alert
        alert("Transaksi Pembayaran Berhasil! Mengeluarkan Struk...");
        
        // Open receipt in new popup window
        const trxId = "{{ session('pesan_sukses_trx') }}";
        const width = 400;
        const height = 600;
        const left = (screen.width/2)-(width/2);
        const top = (screen.height/2)-(height/2);
        
        window.open('/struk/' + trxId, 'Struk Pembayaran', 'width='+width+',height='+height+',top='+top+',left='+left);
    });
</script>
@endif

@endsection

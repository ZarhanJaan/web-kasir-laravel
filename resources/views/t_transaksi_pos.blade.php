@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/pages.css') }}">

<div class="content-wrapper">

  {{-- Page Header --}}
  <div class="page-header">
    <h3 class="page-title">
      <span class="page-title-icon bg-gradient-success text-white me-2">
        <i class="mdi mdi-calculator menu-icon"></i>
      </span> Kasir (Point of Sales)
    </h3>
  </div>

  <div class="row" style="margin-top: 8px;">

    {{-- LEFT: Order Form --}}
    <div class="col-md-8 grid-margin stretch-card">
      <div class="page-card" style="width: 100%;">
        <div class="bg-circle"></div>
        <div class="card-body">

          <div class="page-card-header">
            <div class="page-card-title">
              <div class="title-icon" style="background: linear-gradient(135deg, #51cf66, #2f9e44); box-shadow: 0 4px 14px rgba(81,207,102,0.3);">
                <i class="mdi mdi-cart-outline"></i>
              </div>
              <div>
                <h4>Detail Pesanan</h4>
                <p>Isi form dan tambah item belanja</p>
              </div>
            </div>
          </div>

          <form id="pos-form" action="/kasir/insert" method="POST">
            @csrf

            <div class="pos-form-group">
              <label>Tanggal Transaksi</label>
              <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required readonly>
            </div>

            <div class="pos-form-group">
              <label>Nama Pelanggan / Kasir</label>
              <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control"
                value="Pelanggan Umum" required>
            </div>

            <div class="pos-form-group">
              <label>Metode Pembayaran</label>
              <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                <option value="Cash">Cash / Tunai</option>
                <option value="Qris">QRIS / E-Wallet</option>
              </select>
            </div>

            <div class="pos-form-group">
              <label>ID Transaksi (Manual)</label>
              <input type="number" name="id_penjualan" id="id_penjualan" class="form-control"
                placeholder="Contoh: 10001" required>
            </div>

            <div class="pos-section-title">
              <i class="mdi mdi-format-list-bulleted" style="color: var(--accent-start);"></i>
              Daftar Belanja
            </div>

            <div id="keranjang">
              <div class="pos-item-row item-row">
                <div>
                  <select name="id_produk[]" class="form-control produk-select" required>
                    <option value="" data-harga="0">-- Pilih Produk --</option>
                    @foreach($produk as $p)
                      <option value="{{ $p->id_produk }}"
                        data-harga="{{ $p->harga_jual }}"
                        data-nama="{{ $p->nama_produk }}"
                        {{ $p->is_available ? '' : 'disabled' }}
                        style="{{ $p->is_available ? '' : 'color:#ff6b6b;' }}">
                        {{ $p->nama_produk }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div>
                  <input type="number" name="jumlah_barang[]" class="form-control jumlah-input"
                    value="1" min="1" placeholder="Qty" required>
                </div>
                <div class="pos-subtotal subtotal-label">Rp 0</div>
                <div>
                  <button type="button" class="btn-act btn-act-delete remove-item" style="display:none;">
                    <i class="mdi mdi-delete"></i>
                  </button>
                </div>
              </div>
            </div>

            <button type="button" class="btn-act btn-act-view mt-2" id="add-item" style="padding: 8px 16px;">
              <i class="mdi mdi-plus-circle"></i> Tambah Item
            </button>

            {{-- Hidden total --}}
            <input type="hidden" name="total" id="total_input" value="0">
          </form>

        </div>
      </div>
    </div>

    {{-- RIGHT: Total Card --}}
    <div class="col-md-4 grid-margin stretch-card">
      <div class="pos-total-card" style="width: 100%;">
        <div class="card-body">
          <div class="pos-total-label"><i class="mdi mdi-receipt me-1"></i> Total Tagihan</div>
          <div class="pos-total-amount" id="grand-total-display">Rp 0</div>
          <button type="button" class="btn-bayar" id="btn-verifikasi">
            <i class="mdi mdi-cash-register me-2"></i> BAYAR SEKARANG
          </button>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Modal Verifikasi --}}
<div class="modal fade" id="verifikasiModal" tabindex="-1" aria-labelledby="verifikasiModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header modal-header-success">
        <h5 class="modal-title" id="verifikasiModalLabel">
          <i class="mdi mdi-check-circle me-1"></i> Konfirmasi Pembayaran
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6 style="color: var(--text-secondary); margin-bottom: 6px;">
          Pelanggan: <span id="v-pelanggan" style="color: var(--accent-start); font-weight: 700;"></span>
        </h6>
        <p style="color: var(--text-muted);">Metode: <strong id="v-metode" style="color: var(--text-secondary);"></strong></p>
        <hr style="border-color: rgba(255,255,255,0.08);">
        <h6 style="color: var(--text-muted); font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Detail Pesanan:</h6>
        <ul id="v-items" style="list-style: none; padding: 0; margin: 10px 0 16px;">
          {{-- Items appended via JS --}}
        </ul>
        <div style="background: rgba(102,126,234,0.1); border-radius: 12px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center;">
          <span style="color: var(--text-muted); font-size: 13px;">Total Pembayaran</span>
          <span id="v-total" style="color: var(--accent-start); font-weight: 800; font-size: 18px;"></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-act btn-act-delete" data-bs-dismiss="modal" style="padding: 10px 18px;">
          <i class="mdi mdi-close"></i> Batal
        </button>
        <button type="button" class="btn-glass-success" id="btn-submit-form">
          <i class="mdi mdi-content-save"></i> Lanjutkan Pembayaran
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Modal Sukses Pembayaran --}}
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true"
  data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="p-3 d-flex justify-content-end">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center" style="padding: 10px 40px 40px !important;">
        <div style="width: 90px; height: 90px; background: linear-gradient(135deg, #51cf66, #2f9e44);
          border-radius: 50%; display: flex; align-items: center; justify-content: center;
          margin: 0 auto 24px; box-shadow: 0 8px 32px rgba(81,207,102,0.4);">
          <i class="mdi mdi-check" style="font-size: 48px; color: #fff;"></i>
        </div>
        <h3 style="color: var(--text-primary); font-weight: 800; margin-bottom: 10px;">Pembayaran Berhasil!</h3>
        <p style="color: var(--text-muted);">Transaksi telah berhasil diproses dan dicatat dalam sistem.</p>
      </div>
      <div class="modal-footer" style="justify-content: center;">
        <button type="button" class="btn-glass-success" id="btn-cetak-struk-modal">
          <i class="mdi mdi-printer me-1"></i> Cetak Struk
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const keranjang = document.getElementById('keranjang');
    const grandTotalDisplay = document.getElementById('grand-total-display');
    const totalInput = document.getElementById('total_input');
    const btnAdd = document.getElementById('add-item');

    function formatRupiah(number) {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }

    function hitungTotal() {
      let total = 0;
      document.querySelectorAll('.item-row').forEach(row => {
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

    keranjang.addEventListener('change', e => {
      if (e.target.classList.contains('produk-select') || e.target.classList.contains('jumlah-input')) hitungTotal();
    });
    keranjang.addEventListener('keyup', e => {
      if (e.target.classList.contains('jumlah-input')) hitungTotal();
    });

    btnAdd.addEventListener('click', function () {
      const firstRow = document.querySelector('.item-row');
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
        document.querySelectorAll('.item-row').forEach(row => {
          const select = row.querySelector('.produk-select');
          const qty = row.querySelector('.jumlah-input').value;
          if (select.selectedIndex > 0 && qty > 0) {
            const nama = select.options[select.selectedIndex].getAttribute('data-nama');
            const harga = select.options[select.selectedIndex].getAttribute('data-harga');
            const li = document.createElement('li');
            li.style.cssText = 'display:flex; justify-content:space-between; align-items:center; padding: 8px 12px; border-radius: 10px; background: rgba(255,255,255,0.04); margin-bottom: 6px;';
            li.innerHTML = `<span style="color: var(--text-secondary);">${nama} <span style="color: var(--text-muted);">x${qty}</span></span><span style="color: var(--accent-start); font-weight: 700;">${formatRupiah(harga * qty)}</span>`;
            vItems.appendChild(li);
          }
        });
        verifikasiModal.show();
      }
    });

    document.getElementById('btn-submit-form').addEventListener('click', function () {
      if (totalInput.value == 0) {
        alert('Belum ada produk yang dipilih!');
      } else {
        posForm.submit();
      }
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
        const top = (screen.height / 2) - (height / 2);
        window.open('/struk/' + trxId, 'Struk Pembayaran', 'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left);
      });
    });
  </script>
@endif

@endsection
@extends('layouts.app')

@section('content')

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
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i> {{ session('pesan_error') }}
    </div>
    @endif

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card shadow p-4 mb-5 bg-body rounded">
                <form id="pos-form" action="/stok/keluar/insert" method="POST">
                    @csrf
                    <h4 class="card-title text-danger"><i class="mdi mdi-cart-outline"></i> Keranjang Keluar Manjal</h4>
                    <hr>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Tgl. Keluar</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Pelanggan / Entitas</label>
                            <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control" value="Umum" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Keterangan / Sumber Batal</label>
                            <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Misal: Barang Rusak, Hibah" value="Stok Keluar Manual">
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3">Daftar Barang Keluar</h5>
                    <div id="keranjang">
                        <div class="row mb-2 item-row">
                            <div class="col-7">
                                <select name="id_produk[]" class="form-control produk-select" required>
                                    <option value="" data-harga="0">-- Pilih Produk --</option>
                                    @foreach($produk as $p)
                                        <option value="{{ $p->id_produk }}" data-harga="{{ $p->harga_jual }}" data-nama="{{ $p->nama_produk }}">
                                            {{ $p->nama_produk }} (Stok saat ini: {{ $p->stok }} Pcs)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="number" name="jumlah_barang[]" class="form-control jumlah-input" value="1" min="1" placeholder="Qty Keluar" required>
                            </div>
                            <div class="col-2 text-right">
                                <button type="button" class="btn btn-sm btn-danger remove-item float-right ml-2" style="display:none;"><i class="mdi mdi-delete"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-inverse-danger btn-sm mt-2 mb-4" id="add-item"><i class="mdi mdi-plus"></i> Tambah Baris Barang</button>
                    
                    <hr>
                    <div class="d-flex justify-content-end">
                        <a href="/stok/keluar" class="btn btn-secondary me-2"><i class="mdi mdi-close"></i> Batal</a>
                        <button type="submit" class="btn btn-danger" id="btn-submit-form"><i class="mdi mdi-content-save"></i> Proses Pengeluaran Stok</button>
                    </div>
                </form>
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
            clone.querySelector('.produk-select').selectedIndex = 0;
            clone.querySelector('.jumlah-input').value = 1;
            clone.querySelector('.remove-item').style.display = 'inline-block';
            
            // Add remove event
            clone.querySelector('.remove-item').addEventListener('click', function() {
                clone.remove();
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
    });
</script>

@endsection

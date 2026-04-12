@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-table-large menu-icon"></i>
            </span> Edit Transaksi
          </h3>
        </div>
        <br><br>
        {{-- Edit penjualan --}}
        <form action="/riwayat-transaksi/update/{{ $penjualan->id_penjualan }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="">ID Transaksi</label>
                    <input name="id_penjualan" class="form-control" value="{{ $penjualan->id_penjualan }}" readonly>
                    <div class="text-danger">
                        @error('id_penjualan')
                            {{ $message }}
                        @enderror
                    </div>
                    <br>
                    <label for="">Tanggal</label>
                    <input name="tanggal" class="form-control" type="date" value="{{ $penjualan->tanggal }}">
                    <div class="text-danger">
                        @error('tanggal')
                            {{ $message }}
                        @enderror
                    </div>
                    <br>
                    <label for="">Nama Pelanggan</label>
                    <input name="nama_pelanggan" class="form-control" value="{{ $penjualan->nama_pelanggan }}">
                    <div class="text-danger">
                        @error('nama_pelanggan')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="">Total</label>
                    <input name="total" class="form-control" value="{{ $penjualan->total }}">
                    <div class="text-danger">
                        @error('total')
                            {{ $message }}
                        @enderror
                    </div>
                    <br>
                    <label for="">Daftar Produk & Jumlah</label>
                    <div id="produk-list">
                        @php
                            $produkIds = explode(',', $penjualan->id_produk);
                            $jumlahs = is_array($penjualan->jumlah_barang) ? $penjualan->jumlah_barang : explode(',', $penjualan->jumlah_barang);
                            if(count($jumlahs) < count($produkIds)) $jumlahs = array_pad($jumlahs, count($produkIds), '');
                        @endphp
                        @foreach($produkIds as $i => $id_produk)
                        <div class="row mb-2 produk-row">
                            <div class="col-7">
                                <select name="id_produk[]" class="form-control">
                                    @foreach(App\Models\ProdukModel::all() as $produk)
                                        <option value="{{ $produk->id_produk }}" @if($produk->id_produk == $id_produk) selected @endif>{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <input type="number" name="jumlah_barang[]" class="form-control" placeholder="Jumlah" value="{{ $jumlahs[$i] ?? '' }}">
                            </div>
                            <div class="col-1 d-flex align-items-center">
                                <button type="button" class="btn btn-danger btn-sm remove-produk">-</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-success btn-sm mt-2" id="add-produk">+ Tambah Produk</button>
                    <br>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-12 col-md-6">
                    <button class="btr btn bg-gradient-info text-white">Simpan</button>
                </div>
            </div>
        </form>

        @push('scripts')
        <script>
        window.onload = function() {
            var produkList = document.getElementById('produk-list');
            var addBtn = document.getElementById('add-produk');
            if (!produkList || !addBtn) return;
            addBtn.onclick = function(e) {
                e.preventDefault();
                var row = produkList.querySelector('.produk-row');
                if (!row) return;
                var clone = row.cloneNode(true);
                // Reset all input/select in the new row
                var selects = clone.querySelectorAll('select');
                for (var i = 0; i < selects.length; i++) selects[i].selectedIndex = 0;
                var inputs = clone.querySelectorAll('input');
                for (var i = 0; i < inputs.length; i++) inputs[i].value = '';
                produkList.appendChild(clone);
            };
            produkList.onclick = function(e) {
                e = e || window.event;
                var target = e.target || e.srcElement;
                if(target.classList.contains('remove-produk')) {
                    e.preventDefault();
                    var rows = produkList.querySelectorAll('.produk-row');
                    if(rows.length > 1) {
                        target.closest('.produk-row').remove();
                    }
                }
            };
        };
        </script>
        @endpush

      </div>

      
    
@endsection

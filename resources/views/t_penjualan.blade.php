@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/pages.css') }}">

<div class="content-wrapper">

  {{-- Page Header --}}
  <div class="page-header">
    <h3 class="page-title">
      <span class="page-title-icon bg-gradient-primary text-white me-2">
        <i class="mdi mdi-table-large menu-icon"></i>
      </span> Riwayat Transaksi
    </h3>
    @if(Auth::user()->hasAnyRole(['owner', 'admin']))
      <nav aria-label="breadcrumb">
        <a href="/riwayat-transaksi/add" class="btn-glass-primary">
          <i class="mdi mdi-plus"></i> Tambah Transaksi
        </a>
      </nav>
    @endif
  </div>

  {{-- Flash Messages --}}
  @if(session('pesan_sukses'))
    <div class="page-alert-success">
      <strong><i class="mdi mdi-check-circle me-1"></i>Berhasil!</strong> {{ session('pesan_sukses') }}
    </div>
  @endif
  @if(session('pesan_hapus'))
    <div class="page-alert-danger">
      <strong><i class="mdi mdi-delete me-1"></i>Dihapus!</strong> {{ session('pesan_hapus') }}
    </div>
  @endif

  {{-- Table Card --}}
  <div class="row">
    <div class="col-12 grid-margin">
      <div class="page-card">
        <div class="bg-circle"></div>
        <div class="card-body">

          <div class="page-card-header">
            <div class="page-card-title">
              <div class="title-icon">
                <i class="mdi mdi-receipt"></i>
              </div>
              <div>
                <h4>Daftar Riwayat Transaksi</h4>
                <p>Semua transaksi penjualan yang telah dicatat</p>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="page-table" id="mytable">
              <thead>
                <tr>
                  <th>ID Transaksi</th>
                  <th>Tanggal</th>
                  <th>Nama Pelanggan</th>
                  <th>Jumlah Item</th>
                  <th>Total Harga</th>
                  <th>Produk</th>
                  @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                    <th>Aksi</th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @foreach ($penjualan as $data)
                  <tr>
                    <td><span class="badge-glass badge-glass-primary">{{ $data->id_penjualan }}</span></td>
                    <td style="color: var(--text-muted);">{{ $data->tanggal }}</td>
                    <td class="text-primary-val">{{ $data->nama_pelanggan }}</td>
                    <td>
                      <span class="badge-glass badge-glass-info">{{ $data->jumlah_barang }} item</span>
                    </td>
                    <td style="color: var(--success-color); font-weight: 700;">
                      Rp {{ number_format($data->total, 0, ',', '.') }}
                    </td>
                    <td style="color: var(--text-secondary); font-size: 13px;">
                      @php
                        $produkIds  = explode(',', $data->id_produk);
                        $namaProduk = \App\Models\ProdukModel::whereIn('id_produk', $produkIds)->pluck('nama_produk')->toArray();
                        echo implode(', ', $namaProduk);
                      @endphp
                    </td>
                    @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                      <td>
                        <a href="/riwayat-transaksi/edit/{{ $data->id_penjualan }}" class="btn-act btn-act-edit">
                          <i class="mdi mdi-pencil"></i> Edit
                        </a>
                        <button type="button" class="btn-act btn-act-delete ms-1"
                          data-bs-toggle="modal" data-bs-target="#delete{{ $data->id_penjualan }}">
                          <i class="mdi mdi-delete"></i> Hapus
                        </button>
                      </td>
                    @endif
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>

</div>

{{-- Delete Modals --}}
@if(Auth::user()->hasAnyRole(['owner', 'admin']))
  @foreach ($penjualan as $data)
    <div class="modal fade" id="delete{{ $data->id_penjualan }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header modal-header-danger">
            <h5 class="modal-title">
              <i class="mdi mdi-alert me-1"></i> Hapus Transaksi
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus transaksi atas nama:</p>
            <p style="color: var(--text-primary); font-weight: 700; font-size: 16px;">{{ $data->nama_pelanggan }}</p>
            <p style="color: var(--error-color); font-size: 13px;">
              <i class="mdi mdi-alert-circle me-1"></i> Tindakan ini tidak dapat dibatalkan.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-act btn-act-view" data-bs-dismiss="modal" style="padding: 10px 18px;">
              <i class="mdi mdi-close"></i> Batal
            </button>
            <a href="/riwayat-transaksi/delete/{{ $data->id_penjualan }}" class="btn-glass-danger">
              <i class="mdi mdi-delete"></i> Hapus
            </a>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endif

@endsection

@section('scripts')
<script>
  $(document).ready(function () {
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
      var min  = $('#min-date').val();
      var max  = $('#max-date').val();
      var date = data[1];
      if (!min && !max) return true;
      if (min && !max && date >= min) return true;
      if (!min && max && date <= max) return true;
      if (min && max && date >= min && date <= max) return true;
      return false;
    });

    var customFilter = `
      <div class="dt-custom-filter me-3" style="display:inline-flex; align-items:center; gap: 8px;">
        <label class="mb-0" style="white-space:nowrap;">Filter Tanggal:</label>
        <input type="date" id="min-date" class="form-control form-control-sm" style="width:auto;">
        <span style="color: var(--text-muted);">-</span>
        <input type="date" id="max-date" class="form-control form-control-sm" style="width:auto;">
      </div>
    `;

    var checkExist = setInterval(function () {
      var target = $('.dt-search').length ? $('.dt-search') : $('.dataTables_filter');
      if (target.length) {
        target.prepend(customFilter);
        target.css({ display: 'flex', alignItems: 'center', justifyContent: 'flex-end', flexWrap: 'wrap' });
        $('#min-date, #max-date').on('change', function () { $('#mytable').DataTable().draw(); });
        clearInterval(checkExist);
      }
    }, 100);
  });
</script>
@endsection
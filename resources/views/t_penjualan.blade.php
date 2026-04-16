@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/pages/riwayat_transaksi.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-table-large menu-icon"></i>
                </span> Riwayat Transaksi
            </h3>
            @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                <nav aria-label="breadcrumb">
                    <a href="/riwayat-transaksi/add" class="mu-btn-header">
                        <i class="mdi mdi-plus"></i> Tambah
                    </a>
                </nav>
            @endif
        </div>

        @if (session('pesan_sukses'))
            <div class="mu-alert-success">
                <i class="mdi mdi-check-circle"></i> {{ session('pesan_sukses') }}
            </div>
        @endif

        @if (session('pesan_hapus'))
            <div class="mu-alert-danger">
                <i class="mdi mdi-delete"></i> {{ session('pesan_hapus') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="mu-table-card" data-aos="fade-up" data-aos-duration="800">
                    <div class="card-body">
                        <div class="card-title-row">
                            <div class="title-icon">
                                <i class="mdi mdi-history"></i>
                            </div>
                            <h4>Daftar Transaksi</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="mu-table" id="mytable">
                                <thead>
                                    <tr>
                                        <th>ID Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Nama Pelanggan</th>
                                        <th>Jumlah Barang</th>
                                        <th>Total Harga</th>
                                        <th>Produk</th>
                                        @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                                            <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($penjualan as $data)
                                        <tr>
                                            <td>{{ $data->id_penjualan }}</td>
                                            <td>{{ $data->tanggal }}</td>
                                            <td class="col-name">{{ $data->nama_pelanggan }}</td>
                                            <td>{{ $data->jumlah_barang }}</td>
                                            <td class="col-price">Rp.{{ $data->total }}</td>
                                            <td class="col-products">
                                                @php
                                                    $produkIds = explode(',', $data->id_produk);
                                                    $namaProduk = \App\Models\ProdukModel::whereIn('id_produk', $produkIds)->pluck('nama_produk')->toArray();
                                                    echo implode(', ', $namaProduk);
                                                @endphp
                                            </td>
                                            @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                                                <td>
                                                    <a href="/riwayat-transaksi/edit/{{ $data->id_penjualan }}"
                                                        class="mu-btn mu-btn-warning" title="Edit">
                                                        <i class="mdi mdi-border-color"></i>
                                                    </a>
                                                    <button type="button" class="mu-btn mu-btn-danger" data-bs-toggle="modal"
                                                        data-bs-target="#delete{{ $data->id_penjualan }}" title="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                                @foreach ($penjualan as $data)
                                    <!-- Modal -->
                                    <div class="modal fade" id="delete{{ $data->id_penjualan }}" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="delete">Pelanggan:
                                                        {{ $data->nama_pelanggan }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Apakah Anda yakin ingin menghapus data tersebut?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <a href="/riwayat-transaksi/delete/{{ $data->id_penjualan }}"
                                                        class="btn btn-gradient-danger">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Add custom filter logic for DataTables
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                var min = $('#min-date').val();
                var max = $('#max-date').val();
                var date = data[1]; // Kolom index 1 adalah Tanggal

                if (!min && !max) { return true; }
                if (min && !max && date >= min) { return true; }
                if (!min && max && date <= max) { return true; }
                if (min && max && date >= min && date <= max) { return true; }

                return false;
            });

            // Create the filter HTML elements
            var customFilter = `
                    <div class="dt-custom-filter me-3" style="display:inline-flex; align-items:center;">
                        <label class="mb-0 me-2">Filter Tanggal:</label>
                        <input type="date" id="min-date" class="form-control form-control-sm" style="width: auto;" placeholder="Awal">
                        <span class="mx-2">-</span>
                        <input type="date" id="max-date" class="form-control form-control-sm" style="width: auto;" placeholder="Akhir">
                    </div>
                `;

            // DataTables 2.0 uses .dt-search, older versions use .dataTables_filter
            var checkExist = setInterval(function () {
                var targetContainer = $('.dt-search').length ? $('.dt-search') : $('.dataTables_filter');

                if (targetContainer.length) {
                    targetContainer.prepend(customFilter);
                    targetContainer.css({
                        'display': 'flex',
                        'align-items': 'center',
                        'justify-content': 'flex-end',
                        'flex-wrap': 'wrap'
                    });

                    // Trigger redraw the table when dates change
                    $('#min-date, #max-date').on('change', function () {
                        $('#mytable').DataTable().draw();
                    });

                    clearInterval(checkExist);
                }
            }, 100);
        });
    </script>
@endsection
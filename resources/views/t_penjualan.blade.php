@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
            <div class="page-header">
                  <h3 class="page-title">
                        <span class="page-title-icon bg-gradient-primary text-white me-2">
                              <i class="mdi mdi-table-large menu-icon"></i>
                        </span> Riwayat Transaksi
                  </h3>
                  <nav aria-label="breadcrumb">
                        <a href="/riwayat-transaksi/add" class="btn bg-gradient-primary text-white ">
                              Add +
                        </a>
                  </nav>
            </div>
            @if (session('pesan_sukses'))
                  <div class="alert alert-success" role="alert">
                        <i class="fa fa-edit"></i>
                        {{session('pesan_sukses')}}
                  </div>
            @endif
            @if (session('pesan_hapus'))
                  <div class="alert alert-danger" role="alert">
                        <i class="fa fa-trash-o"></i>
                        {{session('pesan_hapus')}}
                  </div>
            @endif
            {{-- table produk --}}
            <div class="card shadow p-3 mb-5 bg-body rounded">
                  <div class="container">
                        <div class="table-responsive">
                              <table class="table table-bordered" id="mytable">
                                    <thead>
                                          <tr>
                                                <th>ID Transaksi</th>
                                                <th>Tanggal</th>
                                                <th>Nama Pelanggan</th>
                                                <th>Jumlah Barang</th>
                                                <th>Total Harga</th>
                                                <th>Produk</th>
                                                <th>Action</th>
                                          </tr>
                                    </thead>
                                    <tbody>
                                          @foreach ($penjualan as $data)
                                                <tr>
                                                      <td>{{$data->id_penjualan}}</td>
                                                      <td>{{$data->tanggal}}</td>
                                                      <td>{{$data->nama_pelanggan}}</td>
                                                      <td>{{$data->jumlah_barang}}</td>
                                                      <td>Rp.{{$data->total}}</td>
                                                      <td>
                                                            @php
                                                                  $produkIds = explode(',', $data->id_produk);
                                                                  $namaProduk = \App\Models\ProdukModel::whereIn('id_produk', $produkIds)->pluck('nama_produk')->toArray();
                                                                  echo implode(', ', $namaProduk);
                                                            @endphp
                                                      </td>
                                                      <td>
                                                            <a href="/riwayat-transaksi/edit/{{ $data->id_penjualan }}"
                                                                  class="btn btn-gradient-warning">
                                                                  <i class="fa fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-gradient-danger"
                                                                  data-bs-toggle="modal"
                                                                  data-bs-target="#delete{{ $data->id_penjualan }}">
                                                                  <i class="fa fa-trash-o"></i>
                                                            </button>
                                                      </td>
                                                </tr>
                                          @endforeach
                                    </tbody>
                              </table>

                              @foreach ($penjualan as $data)
                                    <!-- Modal -->
                                    <div class="modal fade" id="delete{{ $data->id_penjualan }}" tabindex="-1"
                                          aria-labelledby="exampleModalLabel" aria-hidden="true">
                                          <div class="modal-dialog">
                                                <div class="modal-content">
                                                      <div class="modal-header bg-gradient-primary text-white">
                                                            <h5 class="modal-title" id="delete">Pelanggan:
                                                                  {{ $data->nama_pelanggan }}</h5>
                                                            <button type="button" class="btn-close bg-light" data-bs-dismiss="modal"
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
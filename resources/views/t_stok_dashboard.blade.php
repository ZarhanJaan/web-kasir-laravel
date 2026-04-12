@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-trending-up menu-icon"></i>
            </span> Dashboard Stok & Bahan
          </h3>
        </div>
        
        <div class="row">
          <div class="col-md-6 stretch-card grid-margin">
            <div class="card bg-gradient-danger card-img-holder text-white shadow">
              <div class="card-body">
                <h4 class="font-weight-normal mb-3">Stok Menipis (Bahan) <i class="mdi mdi-alert mdi-24px float-right"></i></h4>
                @if(count($stok_menipis) > 0)
                  <ul class="list-unstyled">
                    @foreach($stok_menipis as $item)
                      <li><h5>{{ $item->nama_stok }} <span class="badge bg-white text-danger float-end">{{ $item->stok }} {{ $item->satuan }} tersisa</span></h5></li>
                      <hr>
                    @endforeach
                  </ul>
                @else
                  <h4 class="mb-5">Semua Stok Bahan Aman (>10)</h4>
                @endif
              </div>
            </div>
          </div>
          
          <div class="col-md-6 stretch-card grid-margin">
            <div class="card bg-gradient-success card-img-holder text-white shadow">
              <div class="card-body">
                <h4 class="font-weight-normal mb-3">Top 5 Menu Terlaris <i class="mdi mdi-chart-line mdi-24px float-right"></i></h4>
                @if(count($top_terlaris) > 0)
                  <ul class="list-unstyled">
                    @foreach($top_terlaris as $item)
                      <li><h5>{{ $item->nama_produk }} <span class="badge bg-white text-success float-end">{{ $item->total_terjual }} Keluar</span></h5></li>
                      <hr>
                    @endforeach
                  </ul>
                @else
                  <h4 class="mb-5">Belum ada data penjualan keluar.</h4>
                @endif
              </div>
            </div>
          </div>
        </div>

        {{-- Daftar Lengkap Stok (Request User #2) --}}
        <div class="row">
          <div class="col-12 grid-margin">
            <div class="card shadow p-3 mb-5 bg-body rounded">
              <div class="card-body">
                <h4 class="card-title text-primary"><i class="mdi mdi-package-variant"></i> Daftar Lengkap Stok Bahan Baku</h4>
                <p class="text-muted">Menampilkan seluruh ketersediaan bahan yang digunakan dalam menu.</p>
                <div class="table-responsive">
                  <table class="table table-bordered table-hover" id="mytable">
                        <thead class="table-light">
                              <tr>
                                    <th>ID Bahan</th>
                                    <th>Nama Bahan Baku</th>
                                    <th>Jumlah Stok</th>
                                    <th>Satuan</th>
                                    <th>Status</th>
                              </tr>
                        </thead>
                        <tbody>
                        @foreach ($stok_lengkap as $s)
                              <tr>
                                    <td>{{ $s->id_stok }}</td>
                                    <td class="font-weight-bold">{{ $s->nama_stok }}</td>
                                    <td>{{ $s->stok }}</td>
                                    <td>{{ $s->satuan }}</td>
                                    <td>
                                          @if($s->stok < 5)
                                                <span class="badge bg-danger">Kritis</span>
                                          @elseif($s->stok < 15)
                                                <span class="badge bg-warning text-dark">Menipis</span>
                                          @else
                                                <span class="badge bg-success">Tersedia</span>
                                          @endif
                                    </td>
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

@endsection

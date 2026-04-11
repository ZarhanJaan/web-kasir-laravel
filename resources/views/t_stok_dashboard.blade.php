@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-trending-up menu-icon"></i>
            </span> Dashboard Stok
          </h3>
        </div>
        
        <div class="row">
          <div class="col-md-6 stretch-card grid-margin">
            <div class="card bg-gradient-danger card-img-holder text-white">
              <div class="card-body">
                <h4 class="font-weight-normal mb-3">Produk Stok Menipis <i class="mdi mdi-alertmdi-24px float-right"></i></h4>
                @if(count($stok_menipis) > 0)
                  <ul class="list-unstyled">
                    @foreach($stok_menipis as $item)
                      <li><h5>{{ $item->nama_produk }} <span class="badge bg-white text-danger float-end">{{ $item->stok }} tersisa</span></h5></li>
                      <hr>
                    @endforeach
                  </ul>
                @else
                  <h4 class="mb-5">Stok Semua Produk Aman (>10)</h4>
                @endif
              </div>
            </div>
          </div>
          
          <div class="col-md-6 stretch-card grid-margin">
            <div class="card bg-gradient-success card-img-holder text-white">
              <div class="card-body">
                <h4 class="font-weight-normal mb-3">Top 5 Produk Terlaris <i class="mdi mdi-chart-line mdi-24px float-right"></i></h4>
                @if(count($top_terlaris) > 0)
                  <ul class="list-unstyled">
                    @foreach($top_terlaris as $item)
                      <li><h5>{{ $item->nama_produk }} <span class="badge bg-white text-success float-end">{{ $item->total_terjual }} Keluar</span></h5></li>
                      <hr>
                    @endforeach
                  </ul>
                @else
                  <h4 class="mb-5">Belum ada data stok keluar bulan ini.</h4>
                @endif
              </div>
            </div>
          </div>
        </div>

      </div>

@endsection

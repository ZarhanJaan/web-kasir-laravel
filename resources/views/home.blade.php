@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
              <i class="mdi mdi-home"></i>
            </span> Dashboard
          </h3>
          <nav aria-label="breadcrumb">
          </nav>
        </div>

        @if(count($stok_menipis) > 0)
        <div class="row">
          <div class="col-12 grid-margin">
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
              <strong><i class="mdi mdi-alert"></i> Perhatian!</strong> Ada <b>{{ count($stok_menipis) }}</b> bahan baku dengan stok menipis (< 10).
              <hr>
              <ul class="mb-0">
                @foreach($stok_menipis as $item)
                  <li>{{ $item->nama_stok }} (Sisa: <b>{{ $item->stok }}</b>)</li>
                @endforeach
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="this.parentElement.style.display='none';"></button>
            </div>
          </div>
        </div>
        @endif
        <div class="row ">
          <div class="col-md-4 stretch-card grid-margin">
            <div data-aos-easing="linear" data-aos-duration="1500" data-aos="flip-left" class="card bg-gradient-danger card-img-holder text-white shadow mb-5 bg-body rounded">
              <div class="card-body">
                <img src="{{asset('template/')}}/assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                <h4 class="font-weight-normal mb-3">Total Menu<i class="mdi mdi-silverware mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $totalProduk }}</h2>
                <h6 class="card-text">Total Jenis Bahan Baku: {{ count(DB::table('t_stok_item')->get()) }}</h6>
              </div>
            </div>
          </div>
          <div data-aos-easing="linear" data-aos-duration="1500" data-aos="flip-up" class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-info card-img-holder text-white shadow mb-5 bg-body rounded">
              <div class="card-body">
                <img src="{{asset('template/')}}/assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                <h4 class="font-weight-normal mb-3">Total Penjualan<i class="mdi mdi-bookmark-outline mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">Rp. {{ $totalPenjualan }}</h2>
                <h6 class="card-text">Total Data Penjualan {{ $totalData }}</h6>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div data-aos-easing="linear" data-aos-duration="1500" data-aos="flip-right" class="card text-white shadow mb-5 bg-body rounded">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d495.60727140211736!2d106.87810106457734!3d-6.412263032950024!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ebb42c11205d%3A0x322eeb498a4a200f!2sWarung%20sembako%20Lina!5e0!3m2!1sen!2sid!4v1748267883906!5m2!1sen!2sid" width="375px" height="230px" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
        </div>
      </div>
     
    @endsection

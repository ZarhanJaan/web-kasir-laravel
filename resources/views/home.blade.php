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

        @if(Auth::user()->role_id == null)
        <div class="row">
          <div class="col-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body text-center p-5">
                <i class="mdi mdi-account-clock text-primary" style="font-size: 64px;"></i>
                <h2 class="mt-4">Akun Menunggu Verifikasi</h2>
                <p class="text-secondary lead">Akun Anda berhasil didaftarkan. Harap tunggu Administrator untuk memberikan akses (Role) agar Anda dapat menggunakan fitur aplikasi.</p>
              </div>
            </div>
          </div>
        </div>
        @else
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
          <div class="col-md-6 stretch-card grid-margin">
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
          {{-- Total Penjualan and Maps removed --}}
        </div>

        <div class="row">
          <div class="col-md-12 grid-margin stretch-card">
            <div class="card shadow mb-5 bg-body rounded">
              <div class="card-body">
                <h4 class="card-title text-primary"><i class="mdi mdi-star"></i> Menu Terlaris</h4>
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Peringkat</th>
                        <th>Nama Menu</th>
                        <th>Total Terjual</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($menu_terlaris as $index => $menu)
                      <tr>
                        <td>
                          @if($index == 0)
                            <i class="mdi mdi-trophy text-warning" style="font-size: 20px;"></i> #1
                          @elseif($index == 1)
                            <i class="mdi mdi-medal text-secondary" style="font-size: 18px;"></i> #2
                          @elseif($index == 2)
                            <i class="mdi mdi-medal text-bronze" style="font-size: 16px; color: #cd7f32;"></i> #3
                          @else
                            #{{ $index + 1 }}
                          @endif
                        </td>
                        <td>{{ $menu->nama_produk }}</td>
                        <td>
                          <div class="badge badge-gradient-success">{{ $menu->total_terjual }} terjual</div>
                        </td>
                      </tr>
                      @endforeach
                      @if(count($menu_terlaris) == 0)
                      <tr>
                        <td colspan="3" class="text-center text-muted">Belum ada data penjualan.</td>
                      </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        </div>
      </div>
     
    @endsection

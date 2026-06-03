@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/dashboard.css') }}">

  <div class="content-wrapper">
    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
          <i class="mdi mdi-home"></i>
        </span> Dashboard
      </h3>
    </div>

    @if(Auth::user()->role_id == null)
      {{-- Akun Menunggu Verifikasi --}}
      <div class="row">
        <div class="col-12 grid-margin stretch-card">
          <div class="waiting-card">
            <div class="card-body text-center">
              <div class="waiting-icon">
                <i class="mdi mdi-account-clock"></i>
              </div>
              <h2>Akun Menunggu Verifikasi</h2>
              <p>Akun Anda berhasil didaftarkan. Harap tunggu Administrator untuk memberikan akses (Role) agar Anda dapat menggunakan fitur aplikasi.</p>
            </div>
          </div>
        </div>
      </div>
    @else

      {{-- Warning Stok Menipis --}}
      @if(count($stok_menipis) > 0)
        <div class="row">
          <div class="col-12 grid-margin">
            <div class="dash-alert alert alert-dismissible fade show" role="alert">
              <strong><i class="mdi mdi-alert"></i> Perhatian!</strong> Ada <b>{{ count($stok_menipis) }}</b> bahan baku dengan stok menipis (< 10).
              <hr style="border-color: rgba(252,196,25,0.15);">
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

      <style>
        .stat-cards-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 16px; /* Jarak konsisten yang kecil antar box */
            margin-bottom: 24px;
        }
        .stat-card-item {
            flex: 1 1 calc(25% - 16px);
            min-width: 220px;
            max-width: 320px;
            display: flex;
        }
        .stat-card-item .dash-stat-card {
            width: 100%;
        }
        @media (max-width: 991px) {
            .stat-card-item {
                flex: 1 1 calc(33.333% - 16px);
            }
        }
        @media (max-width: 768px) {
            .stat-card-item {
                flex: 1 1 calc(50% - 16px);
                max-width: 100%;
            }
        }
        @media (max-width: 480px) {
            .stat-card-item {
                flex: 1 1 100%;
            }
        }
      </style>

      {{-- Stat Cards --}}
      <div class="stat-cards-wrapper">
        <div class="stat-card-item">
          <div class="dash-stat-card">
            <div class="bg-circle"></div>
            <div class="card-body">
              <div class="stat-icon">
                <i class="mdi mdi-package-variant"></i>
              </div>
              <div class="stat-label">Total Stok Keseluruhan</div>
              <div class="stat-value">{{ $totalStok }}</div>
              <div class="stat-sub">Total jenis stok: <span>{{ $totalJenis }}</span></div>
            </div>
          </div>
        </div>

        {{-- Total Stok Masuk & Keluar --}}
        <div class="stat-card-item">
          <div class="dash-stat-card">
            <div class="bg-circle" style="background: linear-gradient(135deg, #4e73df, #224abe);"></div>
            <div class="card-body">
              <div class="stat-icon" style="background: linear-gradient(135deg, #4e73df, #224abe); box-shadow: 0 8px 24px rgba(78, 115, 223, 0.3);">
                <i class="mdi mdi-arrow-down-bold-circle-outline"></i>
              </div>
              <div class="stat-label">Total Stok Masuk</div>
              <div class="stat-value">{{ $totalStokMasuk ?? 0 }}</div>
              <div class="stat-sub">Akumulasi barang masuk</div>
            </div>
          </div>
        </div>

        <div class="stat-card-item">
          <div class="dash-stat-card">
            <div class="bg-circle" style="background: linear-gradient(135deg, #e74a3b, #be2617);"></div>
            <div class="card-body">
              <div class="stat-icon" style="background: linear-gradient(135deg, #e74a3b, #be2617); box-shadow: 0 8px 24px rgba(231, 74, 59, 0.3);">
                <i class="mdi mdi-arrow-up-bold-circle-outline"></i>
              </div>
              <div class="stat-label">Total Stok Keluar</div>
              <div class="stat-value">{{ $totalStokKeluar ?? 0 }}</div>
              <div class="stat-sub">Akumulasi barang keluar</div>
            </div>
          </div>
        </div>

        {{-- Kategori Menu Breakdown --}}
        @foreach($kategoriMenu as $index => $kat)
        <div class="stat-card-item">
          <div class="dash-stat-card">
            <div class="bg-circle" style="background: linear-gradient(135deg, #11998e, #38ef7d);"></div>
            <div class="card-body">
              <div class="stat-icon" style="background: linear-gradient(135deg, #11998e, #38ef7d); box-shadow: 0 8px 24px rgba(17, 153, 142, 0.3);">
                <i class="mdi mdi-shape"></i>
              </div>
              <div class="stat-label">Kategori: {{ $kat->kategori ?: 'Tanpa Kategori' }}</div>
              <div class="stat-value">{{ $kat->total }}</div>
              <div class="stat-sub">Total jenis stok kategori ini</div>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      {{-- Menu Terlaris Table --}}
      <div class="row">
        <div class="col-12 grid-margin">
          <div class="dash-table-card">
            <div class="card-body">
              <div class="card-title-row">
                <div class="title-icon">
                  <i class="mdi mdi-package-up"></i>
                </div>
                <h4>Stok Paling Banyak</h4>
              </div>
              <div class="table-responsive">
                <table class="dash-table">
                  <thead>
                    <tr>
                      <th style="width: 80px;">Peringkat</th>
                      <th>Nama Stok</th>
                      <th style="width: 160px;">Jumlah Stok</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($stok_terbanyak as $index => $stok)
                      <tr>
                        <td>
                          @if($index == 0)
                            <span class="rank-badge gold">1</span>
                          @elseif($index == 1)
                            <span class="rank-badge silver">2</span>
                          @elseif($index == 2)
                            <span class="rank-badge bronze">3</span>
                          @else
                            <span class="rank-badge normal">{{ $index + 1 }}</span>
                          @endif
                        </td>
                        <td style="color: var(--text-primary); font-weight: 500;">{{ $stok->nama_stok }}</td>
                        <td>
                          <span class="sold-badge" style="background-color: rgba(54, 185, 204, 0.1); color: #36b9cc; border: 1px solid rgba(54, 185, 204, 0.2);">
                            <i class="mdi mdi-package"></i>
                            {{ $stok->stok }} {{ $stok->satuan }}
                          </span>
                        </td>
                      </tr>
                    @endforeach
                    @if(count($stok_terbanyak) == 0)
                      <tr>
                        <td colspan="3" class="no-data">
                          <i class="mdi mdi-information-outline" style="font-size: 24px; display: block; margin-bottom: 8px; color: var(--text-muted);"></i>
                          Belum ada data stok.
                        </td>
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

@endsection
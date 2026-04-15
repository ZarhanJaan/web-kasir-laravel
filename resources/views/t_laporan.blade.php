@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/pages.css') }}">

<div class="content-wrapper">

  {{-- Page Header --}}
  <div class="page-header">
    <h3 class="page-title">
      <span class="page-title-icon bg-gradient-primary text-white me-2">
        <i class="mdi mdi-chart-bar"></i>
      </span> Laporan Penjualan
    </h3>
  </div>

  {{-- 1. Penjualan Harian --}}
  <div class="row mb-3">
    <div class="col-12 grid-margin stretch-card">
      <div class="laporan-chart-card" style="width:100%;">
        <div class="card-body">
          <div class="laporan-chart-header">
            <div class="laporan-chart-title">
              <div class="chart-icon chart-icon-blue">
                <i class="mdi mdi-chart-line"></i>
              </div>
              <h4>Penjualan Hari Ini (Per Jam)</h4>
            </div>
            <a href="{{ route('datapenjualan_tgl_pdf') }}" class="btn-glass-danger" style="font-size: 13px; padding: 9px 16px;">
              <i class="mdi mdi-file-pdf"></i> Cetak PDF Pertanggal
            </a>
          </div>
          <div style="position: relative; height: 300px;">
            <canvas id="chartPenjualanHarian"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- 2. Penjualan Bulanan --}}
  <div class="row mb-3">
    <div class="col-12 grid-margin stretch-card">
      <div class="laporan-chart-card" style="width:100%;">
        <div class="card-body">
          <div class="laporan-chart-header">
            <div class="laporan-chart-title">
              <div class="chart-icon chart-icon-purple">
                <i class="mdi mdi-chart-bar"></i>
              </div>
              <h4>Penjualan Bulanan (Tahun {{ date('Y') }})</h4>
            </div>
            <div style="display:flex; gap: 10px; flex-wrap: wrap;">
              <a href="{{ route('exportpdf') }}" class="btn-glass-danger" style="font-size: 13px; padding: 9px 16px;">
                <i class="mdi mdi-file-pdf"></i> Cetak PDF Semua
              </a>
              <a href="{{ route('exportexcel') }}" class="btn-glass-success" style="font-size: 13px; padding: 9px 16px;">
                <i class="mdi mdi-file-excel"></i> Cetak Excel
              </a>
            </div>
          </div>
          <div style="position: relative; height: 300px;">
            <canvas id="chartPenjualanBulanan"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">

    {{-- 3. Menu Terlaris --}}
    <div class="col-md-6 grid-margin stretch-card">
      <div class="laporan-chart-card" style="width:100%;">
        <div class="card-body">
          <div class="laporan-chart-header">
            <div class="laporan-chart-title">
              <div class="chart-icon chart-icon-yellow">
                <i class="mdi mdi-star"></i>
              </div>
              <h4>Menu Terlaris</h4>
            </div>
            <a href="{{ route('export-terlaris-pdf') }}" class="btn-glass-danger" style="font-size: 13px; padding: 9px 16px;">
              <i class="mdi mdi-file-pdf"></i> Cetak PDF
            </a>
          </div>
          <div style="position: relative; height: 240px; margin-bottom: 20px;">
            <canvas id="chartMenuTerlaris"></canvas>
          </div>
          <div class="table-responsive">
            <table class="page-table">
              <thead>
                <tr>
                  <th>Nama Menu</th>
                  <th style="text-align:right;">Terjual</th>
                </tr>
              </thead>
              <tbody>
                @foreach($menu_terlaris as $item)
                  <tr>
                    <td class="text-primary-val">{{ $item->nama_produk }}</td>
                    <td style="text-align:right;">
                      <span class="badge-glass badge-glass-success">{{ $item->total_terjual }} terjual</span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- 4. Stok Masuk / Keluar --}}
    <div class="col-md-6 grid-margin stretch-card">
      <div class="laporan-chart-card" style="width:100%;">
        <div class="card-body">
          <div class="laporan-chart-header">
            <div class="laporan-chart-title">
              <div class="chart-icon chart-icon-teal">
                <i class="mdi mdi-swap-vertical"></i>
              </div>
              <h4>Stok Masuk &amp; Keluar (Harian)</h4>
            </div>
            <a href="{{ route('export-stok-pdf') }}" class="btn-glass-danger" style="font-size: 13px; padding: 9px 16px;">
              <i class="mdi mdi-file-pdf"></i> Cetak PDF
            </a>
          </div>
          <div style="position: relative; height: 240px; margin-bottom: 20px;">
            <canvas id="chartStok"></canvas>
          </div>
          <div class="table-responsive">
            <table class="page-table">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Jenis</th>
                  <th style="text-align:right;">Qty</th>
                </tr>
              </thead>
              <tbody>
                @foreach($stok_in_out->sortByDesc('tgl')->take(10) as $item)
                  <tr>
                    <td style="color: var(--text-muted);">{{ date('d/m/y', strtotime($item->tgl)) }}</td>
                    <td>
                      <span class="badge-glass {{ $item->jenis == 'masuk' ? 'badge-glass-success' : 'badge-glass-danger' }}">
                        <i class="mdi mdi-{{ $item->jenis == 'masuk' ? 'arrow-down-circle' : 'arrow-up-circle' }}"></i>
                        {{ strtoupper($item->jenis) }}
                      </span>
                    </td>
                    <td style="text-align:right; font-weight:700; color: var(--text-primary);">{{ $item->qty }}</td>
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

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const dataHarian   = @json($penjualan_hari_ini);
    const dataBulanan  = @json($penjualan_bulanan);
    const dataTerlaris = @json($menu_terlaris);
    const dataStok     = @json($stok_in_out);

    const gridColor  = 'rgba(255,255,255,0.06)';
    const tickColor  = 'rgba(255,255,255,0.4)';
    const labelColor = 'rgba(255,255,255,0.7)';

    const defaultOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { labels: { color: labelColor, font: { family: 'Inter' } } }
      },
      scales: {
        x: { ticks: { color: tickColor }, grid: { color: gridColor } },
        y: { ticks: { color: tickColor }, grid: { color: gridColor } }
      }
    };

    // 1. Harian
    new Chart(document.getElementById('chartPenjualanHarian').getContext('2d'), {
      type: 'line',
      data: {
        labels: dataHarian.map(d => d.jam + ':00'),
        datasets: [{
          label: 'Total Penjualan (Rp)',
          data: dataHarian.map(d => d.total_penjualan),
          borderColor: '#4dabf7',
          backgroundColor: 'rgba(77,171,247,0.12)',
          borderWidth: 2, fill: true, tension: 0.4,
          pointBackgroundColor: '#4dabf7',
          pointRadius: 4
        }]
      },
      options: defaultOptions
    });

    // 2. Bulanan
    const namaBulan = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"];
    let labelBulanan = [], totalBulanan = [];
    dataBulanan.forEach(d => { labelBulanan.push(namaBulan[d.bulan-1]); totalBulanan.push(d.total_penjualan); });

    new Chart(document.getElementById('chartPenjualanBulanan').getContext('2d'), {
      type: 'line',
      data: {
        labels: labelBulanan,
        datasets: [{
          label: 'Total Penjualan (Rp)',
          data: totalBulanan,
          borderColor: '#9775fa',
          backgroundColor: 'rgba(151,117,250,0.12)',
          borderWidth: 2, fill: true, tension: 0.4,
          pointBackgroundColor: '#9775fa',
          pointRadius: 4
        }]
      },
      options: defaultOptions
    });

    // 3. Terlaris Doughnut
    new Chart(document.getElementById('chartMenuTerlaris').getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: dataTerlaris.map(d => d.nama_produk),
        datasets: [{
          data: dataTerlaris.map(d => d.total_terjual),
          backgroundColor: ['#667eea','#fcc419','#51cf66','#ff6b6b','#74c0fc','#f59f00'],
          borderWidth: 2,
          borderColor: 'rgba(15,12,41,0.8)'
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { labels: { color: labelColor, font: { family: 'Inter' } } } }
      }
    });

    // 4. Stok Bar
    let labelStok = [], dataMasuk = {}, dataKeluar = {};
    dataStok.forEach(d => {
      if (!labelStok.includes(d.tgl)) labelStok.push(d.tgl);
      if (d.jenis === 'masuk') dataMasuk[d.tgl] = d.qty;
      else dataKeluar[d.tgl] = d.qty;
    });
    labelStok.sort();

    new Chart(document.getElementById('chartStok').getContext('2d'), {
      type: 'bar',
      data: {
        labels: labelStok,
        datasets: [
          { label: 'Stok Masuk',  backgroundColor: 'rgba(81,207,102,0.7)', data: labelStok.map(t => dataMasuk[t]  || 0) },
          { label: 'Stok Keluar', backgroundColor: 'rgba(255,107,107,0.7)', data: labelStok.map(t => dataKeluar[t] || 0) }
        ]
      },
      options: {
        ...defaultOptions,
        scales: {
          x: { ticks: { color: tickColor }, grid: { color: gridColor }, stacked: false },
          y: { ticks: { color: tickColor }, grid: { color: gridColor }, stacked: false }
        }
      }
    });
  });
</script>
@endsection

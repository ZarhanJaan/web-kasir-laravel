@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/laporan_penjualan.css') }}">

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-chart-bar"></i>
            </span> Laporan Penjualan
        </h3>
    </div>

    {{-- 1. Penjualan Harian --}}
    <div class="row mb-2">
        <div class="col-12 grid-margin stretch-card">
            <div class="lp-card">
                <div class="lp-card-header">
                    <div class="lp-title-left">
                        <div class="lp-title-icon">
                            <i class="mdi mdi-chart-line"></i>
                        </div>
                        <h4>Penjualan Hari Ini (Per Jam)</h4>
                    </div>
                    <div class="lp-btn-group">
                        <a href="{{ route('datapenjualan_tgl_pdf') }}" class="lp-btn lp-btn-pdf">
                            <i class="mdi mdi-file-pdf"></i> Cetak PDF Pertanggal
                        </a>
                    </div>
                </div>
                <div class="lp-chart-wrap" style="height:300px;">
                    <canvas id="chartPenjualanHarian"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Penjualan Bulanan --}}
    <div class="row mb-2">
        <div class="col-12 grid-margin stretch-card">
            <div class="lp-card">
                <div class="lp-card-header">
                    <div class="lp-title-left">
                        <div class="lp-title-icon">
                            <i class="mdi mdi-chart-areaspline"></i>
                        </div>
                        <h4>Penjualan Bulanan (Tahun {{ date('Y') }})</h4>
                    </div>
                    <div class="lp-btn-group">
                        <a href="{{ route('exportpdf') }}" class="lp-btn lp-btn-pdf">
                            <i class="mdi mdi-file-pdf"></i> Cetak PDF Semua
                        </a>
                        <a href="{{ route('exportexcel') }}" class="lp-btn lp-btn-excel">
                            <i class="mdi mdi-file-excel"></i> Cetak Excel
                        </a>
                    </div>
                </div>
                <div class="lp-chart-wrap" style="height:300px;">
                    <canvas id="chartPenjualanBulanan"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 3. Menu Terlaris --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="lp-card">
                <div class="lp-card-header">
                    <div class="lp-title-left">
                        <div class="lp-title-icon icon-warning">
                            <i class="mdi mdi-trophy"></i>
                        </div>
                        <h4>Menu Terlaris</h4>
                    </div>
                    <div class="lp-btn-group">
                        <a href="{{ route('export-terlaris-pdf') }}" class="lp-btn lp-btn-pdf">
                            <i class="mdi mdi-file-pdf"></i> Cetak PDF
                        </a>
                    </div>
                </div>

                <div class="lp-chart-wrap" style="height:250px;">
                    <canvas id="chartMenuTerlaris"></canvas>
                </div>

                <hr class="lp-divider">

                <div class="table-responsive">
                    <table class="lp-table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th class="col-right">Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menu_terlaris as $item)
                            <tr>
                                <td class="col-name">{{ $item->nama_produk }}</td>
                                <td class="col-qty col-right">{{ $item->total_terjual }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 4. Laporan Stok Masuk & Keluar --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="lp-card">
                <div class="lp-card-header">
                    <div class="lp-title-left">
                        <div class="lp-title-icon icon-teal">
                            <i class="mdi mdi-swap-vertical"></i>
                        </div>
                        <h4>Laporan Stok Masuk &amp; Keluar (Bulanan)</h4>
                    </div>
                    <div class="lp-btn-group">
                        <a href="{{ route('export-stok-pdf') }}" class="lp-btn lp-btn-pdf">
                            <i class="mdi mdi-file-pdf"></i> Cetak PDF
                        </a>
                    </div>
                </div>

                <div class="lp-chart-wrap" style="height:280px; overflow-x: auto; overflow-y: hidden;">
                    <div id="containerChartStok" style="height: 100%; min-width: 100%;">
                        <canvas id="chartStok"></canvas>
                    </div>
                </div>

                <hr class="lp-divider">

                <div class="table-responsive">
                    <table class="lp-table">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Jenis</th>
                                <th class="col-right">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stok_in_out->sortByDesc('bulan')->take(10) as $item)
                            <tr>
                                <td>
                                    @php
                                        $parts = explode('-', $item->bulan);
                                        $monthName = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"];
                                        echo $monthName[(int)$parts[1] - 1] . ' ' . $parts[0];
                                    @endphp
                                </td>
                                <td>
                                    @if($item->jenis == 'masuk')
                                        <span class="lp-badge lp-badge-masuk">
                                            <i class="mdi mdi-arrow-down"></i> Masuk
                                        </span>
                                    @else
                                        <span class="lp-badge lp-badge-keluar">
                                            <i class="mdi mdi-arrow-up"></i> Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="col-qty col-right">{{ $item->qty }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataHarian  = @json($penjualan_hari_ini);
        const dataBulanan = @json($penjualan_bulanan);
        const dataTerlaris = @json($menu_terlaris);
        const dataStok    = @json($stok_in_out);

        // Default Chart.js dark theme defaults
        Chart.defaults.color = 'rgba(255,255,255,0.55)';
        Chart.defaults.borderColor = 'rgba(255,255,255,0.07)';
        Chart.defaults.font.family = "'Inter', 'Nunito', sans-serif";

        // --- 1. Chart Penjualan Harian (Line) ---
        new Chart(document.getElementById('chartPenjualanHarian').getContext('2d'), {
            type: 'line',
            data: {
                labels: dataHarian.map(d => d.jam + ':00'),
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: dataHarian.map(d => d.total_penjualan),
                    borderColor: 'rgba(102, 126, 234, 1)',
                    backgroundColor: 'rgba(102, 126, 234, 0.15)',
                    borderWidth: 2.5,
                    pointBackgroundColor: 'rgba(102, 126, 234, 1)',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: 'rgba(255,255,255,0.6)' } }
                },
                scales: {
                    x: {
                        ticks: { color: 'rgba(255,255,255,0.45)' },
                        grid:  { color: 'rgba(255,255,255,0.06)' }
                    },
                    y: {
                        ticks: { color: 'rgba(255,255,255,0.45)' },
                        grid:  { color: 'rgba(255,255,255,0.06)' }
                    }
                }
            }
        });

        // --- 2. Chart Penjualan Bulanan (Line) ---
        const namaBulan = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"];
        let labelBulanan = [], totalBulanan = [];
        dataBulanan.forEach(d => {
            labelBulanan.push(namaBulan[d.bulan - 1]);
            totalBulanan.push(d.total_penjualan);
        });

        new Chart(document.getElementById('chartPenjualanBulanan').getContext('2d'), {
            type: 'line',
            data: {
                labels: labelBulanan,
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: totalBulanan,
                    borderColor: 'rgba(118, 75, 162, 1)',
                    backgroundColor: 'rgba(118, 75, 162, 0.15)',
                    borderWidth: 2.5,
                    pointBackgroundColor: 'rgba(118, 75, 162, 1)',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: 'rgba(255,255,255,0.6)' } }
                },
                scales: {
                    x: {
                        ticks: { color: 'rgba(255,255,255,0.45)' },
                        grid:  { color: 'rgba(255,255,255,0.06)' }
                    },
                    y: {
                        ticks: { color: 'rgba(255,255,255,0.45)' },
                        grid:  { color: 'rgba(255,255,255,0.06)' }
                    }
                }
            }
        });

        // --- 3. Menu Terlaris (Doughnut) ---
        new Chart(document.getElementById('chartMenuTerlaris').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: dataTerlaris.map(d => d.nama_produk),
                datasets: [{
                    label: 'Terjual (Porsi/Item)',
                    data: dataTerlaris.map(d => d.total_terjual),
                    backgroundColor: [
                        'rgba(102,126,234,0.85)',
                        'rgba(118,75,162,0.85)',
                        'rgba(255,107,107,0.85)',
                        'rgba(32,201,151,0.85)',
                        'rgba(252,196,25,0.85)'
                    ],
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: 'rgba(255,255,255,0.6)',
                            padding: 14,
                            font: { size: 12 }
                        }
                    }
                }
            }
        });

        // --- 4. Chart Stok Masuk/Keluar (Bar Grouped) ---
        let labelStok = [], dataMasuk = {}, dataKeluar = {};
        dataStok.forEach(d => {
            if (!labelStok.includes(d.bulan)) labelStok.push(d.bulan);
            if (d.jenis === 'masuk') dataMasuk[d.bulan] = d.qty;
            else dataKeluar[d.bulan] = d.qty;
        });
        labelStok.sort();

        // Dynamic width for scrollable chart
        const containerStok = document.getElementById('containerChartStok');
        if (labelStok.length > 3) {
            containerStok.style.width = (labelStok.length * 150) + 'px';
        }

        const formatBulan = (val) => {
            const p = val.split('-');
            const m = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"];
            return m[parseInt(p[1]) - 1] + ' ' + p[0];
        };

        new Chart(document.getElementById('chartStok').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelStok.map(formatBulan),
                datasets: [
                    {
                        label: 'Stok Masuk',
                        backgroundColor: 'rgba(32,201,151,0.75)',
                        borderColor:     'rgba(32,201,151,1)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        data: labelStok.map(b => dataMasuk[b] || 0)
                    },
                    {
                        label: 'Stok Keluar',
                        backgroundColor: 'rgba(255,107,107,0.75)',
                        borderColor:     'rgba(255,107,107,1)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        data: labelStok.map(b => dataKeluar[b] || 0)
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: 'rgba(255,255,255,0.6)' } }
                },
                scales: {
                    x: {
                        ticks: { color: 'rgba(255,255,255,0.45)' },
                        grid:  { color: 'rgba(255,255,255,0.06)' }
                    },
                    y: {
                        ticks: { color: 'rgba(255,255,255,0.45)' },
                        grid:  { color: 'rgba(255,255,255,0.06)' }
                    }
                }
            }
        });
    });
</script>
@endsection

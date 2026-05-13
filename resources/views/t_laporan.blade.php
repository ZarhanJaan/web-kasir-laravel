@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/laporan_penjualan.css') }}">
<style>
    .stok-filter-select {
        background-color: #302b63 !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
        font-size: 0.85rem !important;
        transition: all 0.3s ease;
    }
    .stok-filter-select:focus {
        border-color: var(--accent-start) !important;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25) !important;
    }
    .stok-filter-select option {
        background-color: #0f0c29 !important;
        color: #ffffff !important;
        padding: 10px !important;
    }
    
    /* Scrollable Table for Recent Stok */
    .stok-recent-scroll {
        max-height: 280px;
        overflow-y: auto;
        padding-right: 5px;
    }
    .stok-recent-scroll::-webkit-scrollbar { width: 4px; }
    .stok-recent-scroll::-webkit-scrollbar-track { background: transparent; }
    .stok-recent-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    .stok-recent-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
</style>

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
                        <h4>Penjualan Hari Ini (Per Jam) <span class="badge badge-outline-warning ms-2" style="font-size: 12px; border: 1px solid #ffcc00; color: #ffcc00; padding: 2px 8px; border-radius: 4px;">Total: Rp. {{ number_format($penjualan_hari_ini->sum('total_penjualan'), 0, ',', '.') }}</span></h4>
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
                        <h4>Penjualan Bulanan (Tahun {{ date('Y') }}) <span class="badge badge-outline-info ms-2" style="font-size: 12px; border: 1px solid #00d2ff; color: #00d2ff; padding: 2px 8px; border-radius: 4px;">Total: Rp. {{ number_format($penjualan_bulanan->sum('total_penjualan'), 0, ',', '.') }}</span></h4>
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
                <div class="lp-card-header" style="align-items: flex-start;">
                    <div class="lp-title-left" style="align-items: flex-start; margin-right: 10px;">
                        <div class="lp-title-icon icon-teal">
                            <i class="mdi mdi-swap-vertical"></i>
                        </div>
                        <h4 style="font-size: 15px; line-height: 1.4; margin-top: 4px;">
                            Laporan Stok Masuk <br>
                            &amp; Keluar
                        </h4>
                    </div>
                    <div class="lp-btn-group" style="flex-wrap: wrap; justify-content: flex-end; margin-top: 4px;">
                        {{-- 
                        <a href="{{ route('export-stok-pdf') }}" class="lp-btn lp-btn-pdf">
                            <i class="mdi mdi-file-pdf"></i> Cetak PDF
                        </a>
                        --}}
                        <a href="{{ route('export-stok-masuk-pdf') }}" class="lp-btn lp-btn-pdf" style="margin-right: 5px;">
                            <i class="mdi mdi-file-pdf"></i> Cetak PDF Stok Masuk
                        </a>
                        <a href="{{ route('export-stok-keluar-pdf') }}" class="lp-btn lp-btn-pdf">
                            <i class="mdi mdi-file-pdf"></i> Cetak PDF Stok Keluar
                        </a>
                    </div>
                </div>
                
                <div class="d-flex gap-2 mb-3 mt-1" style="padding: 0 4px;">
                    <select id="filterTypeStok" class="form-select stok-filter-select" style="cursor: pointer; width: auto; min-width: 140px;">
                        <option value="bulanan">Mode: Bulanan</option>
                        <option value="mingguan">Mode: Mingguan</option>
                    </select>
                    
                    <div id="monthFilterContainer" style="display: none;">
                        <select id="filterMonthStok" class="form-select stok-filter-select" style="cursor: pointer; width: auto; min-width: 120px;">
                            @foreach($available_months as $m)
                                @php
                                    $p = explode('-', $m->bulan);
                                    $monthNames = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"];
                                    $label = $monthNames[(int)$p[1] - 1] . ' ' . $p[0];
                                @endphp
                                <option value="{{ $m->bulan }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="lp-chart-wrap" style="height:280px; overflow-x: auto; overflow-y: hidden;">
                    <div id="containerChartStok" style="height: 100%; min-width: 100%;">
                        <canvas id="chartStok"></canvas>
                    </div>
                </div>

                <hr class="lp-divider">

                <div class="stok-recent-scroll">
                    <table class="lp-table">
                        <thead>
                            <tr>
                                <th>Tgl</th>
                                <th>Barang</th>
                                <th>Jenis</th>
                                <th class="col-right">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayat_stok_recent as $item)
                            <tr>
                                <td style="font-size: 11px; white-space: nowrap;">{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                                <td class="col-name" style="max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $item->nama_stok ?? $item->nama_produk }}
                                </td>
                                <td>
                                    @if($item->jenis == 'masuk')
                                        <span class="lp-badge lp-badge-masuk">IN</span>
                                    @else
                                        <span class="lp-badge lp-badge-keluar">OUT</span>
                                    @endif
                                </td>
                                <td class="col-qty col-right" style="font-size: 12px;">
                                    {{ $item->jumlah }} <small class="text-muted">{{ $item->satuan }}</small>
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
        let chartStokInstance = null;

        function updateChartStok(labels, dataMasukArray, dataKeluarArray) {
            const containerStok = document.getElementById('containerChartStok');
            if (labels.length > 4) {
                containerStok.style.width = (labels.length * 120) + 'px';
            } else {
                containerStok.style.width = '100%';
            }

            if (chartStokInstance) {
                chartStokInstance.destroy();
            }

            chartStokInstance = new Chart(document.getElementById('chartStok').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Stok Masuk',
                            backgroundColor: 'rgba(32,201,151,0.75)',
                            borderColor:     'rgba(32,201,151,1)',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            data: dataMasukArray
                        },
                        {
                            label: 'Stok Keluar',
                            backgroundColor: 'rgba(255,107,107,0.75)',
                            borderColor:     'rgba(255,107,107,1)',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            data: dataKeluarArray
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
        }

        // Initial Data (Bulanan)
        const initBulanan = () => {
            let labelStok = [], dataMasuk = {}, dataKeluar = {};
            dataStok.forEach(d => {
                if (!labelStok.includes(d.bulan)) labelStok.push(d.bulan);
                if (d.jenis === 'masuk') dataMasuk[d.bulan] = d.qty;
                else dataKeluar[d.bulan] = d.qty;
            });
            labelStok.sort();

            const formatBulan = (val) => {
                const p = val.split('-');
                const m = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"];
                return m[parseInt(p[1]) - 1] + ' ' + p[0];
            };

            updateChartStok(
                labelStok.map(formatBulan),
                labelStok.map(b => dataMasuk[b] || 0),
                labelStok.map(b => dataKeluar[b] || 0)
            );
        };

        initBulanan();

        // Listeners
        const filterType = document.getElementById('filterTypeStok');
        const monthFilterContainer = document.getElementById('monthFilterContainer');
        const filterMonth = document.getElementById('filterMonthStok');

        filterType.addEventListener('change', function() {
            if (this.value === 'mingguan') {
                monthFilterContainer.style.display = 'block';
                fetchWeeklyData(filterMonth.value);
            } else {
                monthFilterContainer.style.display = 'none';
                initBulanan();
            }
        });

        filterMonth.addEventListener('change', function() {
            fetchWeeklyData(this.value);
        });

        function fetchWeeklyData(bulan) {
            fetch(`/get-weekly-stok?bulan=${bulan}`)
                .then(res => res.json())
                .then(data => {
                    let labelMinggu = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5'];
                    let dMasuk = [0,0,0,0,0], dKeluar = [0,0,0,0,0];

                    data.forEach(d => {
                        if (d.jenis === 'masuk') dMasuk[d.minggu - 1] = d.qty;
                        else dKeluar[d.minggu - 1] = d.qty;
                    });

                    updateChartStok(labelMinggu, dMasuk, dKeluar);
                });
        }
    });
</script>
@endsection

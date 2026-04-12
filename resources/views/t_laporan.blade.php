@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-chart-bar"></i>
            </span> Laporan Penjualan
        </h3>
    </div>

    <!-- 1. Penjualan Harian -->
    <div class="row mb-4">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Penjualan Hari Ini (Per Jam)</h4>
                        <a href="{{ route('datapenjualan_tgl_pdf') }}" class="btn btn-sm btn-danger">
                            <i class="mdi mdi-file-pdf"></i> Cetak PDF Pertanggal
                        </a>
                    </div>
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="chartPenjualanHarian"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Penjualan Bulanan -->
    <div class="row mb-4">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Penjualan Bulanan (Tahun {{ date('Y') }})</h4>
                        <div>
                            <a href="{{ route('exportpdf') }}" class="btn btn-sm btn-danger me-2">
                                <i class="mdi mdi-file-pdf"></i> Cetak PDF Semua
                            </a>
                            <a href="{{ route('exportexcel') }}" class="btn btn-sm btn-success">
                                <i class="mdi mdi-file-excel"></i> Cetak Excel
                            </a>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="chartPenjualanBulanan"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 3. Menu Terlaris -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Menu Terlaris</h4>
                        <a href="{{ route('export-terlaris-pdf') }}" class="btn btn-sm btn-danger">
                            <i class="mdi mdi-file-pdf"></i> Cetak PDF
                        </a>
                    </div>
                    <div class="chart-container mb-4" style="position: relative; height:250px;">
                        <canvas id="chartMenuTerlaris"></canvas>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr class="bg-light">
                                    <th>Menu</th>
                                    <th class="text-end">Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($menu_terlaris as $item)
                                <tr>
                                    <td>{{ $item->nama_produk }}</td>
                                    <td class="text-end font-weight-bold">{{ $item->total_terjual }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Laporan Stok Masuk/Keluar -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Laporan Stok Masuk & Keluar (Harian)</h4>
                        <a href="{{ route('export-stok-pdf') }}" class="btn btn-sm btn-danger">
                            <i class="mdi mdi-file-pdf"></i> Cetak PDF
                        </a>
                    </div>
                    <div class="chart-container mb-4" style="position: relative; height:300px;">
                        <canvas id="chartStok"></canvas>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr class="bg-light">
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th class="text-end">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stok_in_out->sortByDesc('tgl')->take(10) as $item)
                                <tr>
                                    <td>{{ date('d/m/y', strtotime($item->tgl)) }}</td>
                                    <td>
                                        <span class="badge {{ $item->jenis == 'masuk' ? 'bg-success' : 'bg-danger' }} text-white" style="font-size: 9px;">
                                            {{ strtoupper($item->jenis) }}
                                        </span>
                                    </td>
                                    <td class="text-end font-weight-bold">{{ $item->qty }}</td>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari Controller
        const dataHarian = @json($penjualan_hari_ini);
        const dataBulanan = @json($penjualan_bulanan);
        const dataTerlaris = @json($menu_terlaris);
        const dataStok = @json($stok_in_out);

        // --- 1. Chart Penjualan Harian (Line) ---
        const ctxHarian = document.getElementById('chartPenjualanHarian').getContext('2d');
        new Chart(ctxHarian, {
            type: 'line',
            data: {
                labels: dataHarian.map(d => d.jam + ':00'),
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: dataHarian.map(d => d.total_penjualan),
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // --- 2. Chart Penjualan Bulanan (Bar) ---
        const namaBulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];
        const ctxBulanan = document.getElementById('chartPenjualanBulanan').getContext('2d');
        
        let labelBulanan = [];
        let totalBulanan = [];
        dataBulanan.forEach(d => {
            labelBulanan.push(namaBulan[d.bulan - 1]);
            totalBulanan.push(d.total_penjualan);
        });

        new Chart(ctxBulanan, {
            type: 'line',
            data: {
                labels: labelBulanan,
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: totalBulanan,
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // --- 3. Menu Terlaris (Doughnut) ---
        const ctxTerlaris = document.getElementById('chartMenuTerlaris').getContext('2d');
        new Chart(ctxTerlaris, {
            type: 'doughnut',
            data: {
                labels: dataTerlaris.map(d => d.nama_produk),
                datasets: [{
                    label: 'Terjual (Porsi/Item)',
                    data: dataTerlaris.map(d => d.total_terjual),
                    backgroundColor: [
                        '#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff'
                    ],
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // --- 4. Chart Stok Masuk/Keluar (Bar Stacked atau Grouped) ---
        const ctxStok = document.getElementById('chartStok').getContext('2d');
        
        // Memisahkan data masuk & keluar berdasarkan tgl
        let labelStok = [];
        let dataMasuk = {};
        let dataKeluar = {};

        dataStok.forEach(d => {
            if(!labelStok.includes(d.tgl)) labelStok.push(d.tgl);
            if(d.jenis === 'masuk') {
                dataMasuk[d.tgl] = d.qty;
            } else {
                dataKeluar[d.tgl] = d.qty;
            }
        });

        labelStok.sort(); // Urutkan tanggal

        let objMasukArr = labelStok.map(tgl => dataMasuk[tgl] || 0);
        let objKeluarArr = labelStok.map(tgl => dataKeluar[tgl] || 0);

        new Chart(ctxStok, {
            type: 'bar',
            data: {
                labels: labelStok,
                datasets: [
                    {
                        label: 'Stok Masuk',
                        backgroundColor: '#4bc0c0',
                        data: objMasukArr
                    },
                    {
                        label: 'Stok Keluar',
                        backgroundColor: '#ff6384',
                        data: objKeluarArr
                    }
                ]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: false },
                    y: { stacked: false }
                }
            }
        });
    });
</script>
@endsection

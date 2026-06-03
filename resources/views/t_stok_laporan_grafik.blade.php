@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/stok_laporan_grafik.css') }}">
<!-- Menyertakan library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-chart-line menu-icon"></i>
            </span> Grafik & Laporan Stok
        </h3>
    </div>

    <!-- Panel Filter Tanggal & Aksi -->
    <div class="slg-card slg-card-filter">
        <div class="card-body">
            <div class="slg-card-head">
                <div class="slg-card-head-left">
                    <div class="slg-head-icon">
                        <i class="mdi mdi-calendar-search"></i>
                    </div>
                    <div>
                        <h4>Filter Laporan</h4>
                        <small>Sesuaikan rentang tanggal data pergerakan stok</small>
                    </div>
                </div>
                <div class="slg-pdf-actions">
                    <a href="{{ route('stok.laporan_grafik_pdf_masuk', ['tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]) }}" class="slg-btn-pdf slg-btn-pdf-masuk" title="Cetak Rekap PDF Stok Masuk">
                        <i class="mdi mdi-file-pdf-box"></i> PDF Stok Masuk
                    </a>
                    <a href="{{ route('stok.laporan_grafik_pdf_keluar', ['tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]) }}" class="slg-btn-pdf slg-btn-pdf-keluar" title="Cetak Rekap PDF Stok Keluar">
                        <i class="mdi mdi-file-pdf-box"></i> PDF Stok Keluar
                    </a>
                </div>
            </div>

            <form action="{{ route('stok.laporan_grafik') }}" method="GET">
                <div class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="slg-form-label">Tanggal Mulai</label>
                        <input type="date" name="tgl_awal" class="slg-input" value="{{ $tgl_awal }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="slg-form-label">Tanggal Sampai</label>
                        <input type="date" name="tgl_akhir" class="slg-input" value="{{ $tgl_akhir }}" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="slg-btn-filter w-100">
                            <i class="mdi mdi-filter"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Panel Grafik -->
    <div class="slg-card slg-card-chart">
        <div class="card-body">
            <div class="slg-card-head">
                <div class="slg-card-head-left">
                    <div class="slg-head-icon">
                        <i class="mdi mdi-finance"></i>
                    </div>
                    <div>
                        <h4>Grafik Stok Harian</h4>
                        <small>Perbandingan volume Barang Masuk vs Barang Keluar per hari</small>
                    </div>
                </div>
            </div>
            
            <div class="slg-chart-container">
                <canvas id="stokChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Panel Tabel Rekapitulasi (yang dicetak di PDF) -->
    <div class="slg-card slg-card-table">
        <div class="card-body">
            <div class="slg-card-head">
                <div class="slg-card-head-left">
                    <div class="slg-head-icon">
                        <i class="mdi mdi-table-large"></i>
                    </div>
                    <div>
                        <h4>Rekapitulasi Per Barang</h4>
                        <small>Total volume pergerakan masing-masing item (Cetak terpisah via PDF Stok Masuk / Keluar)</small>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="slg-table">
                    <thead>
                        <tr>
                            <th>ID Stok</th>
                            <th>Nama Barang</th>
                            <th class="text-center">Total Masuk (Pcs)</th>
                            <th class="text-center">Total Keluar (Pcs)</th>
                            <th class="text-center">Selisih Bersih (Pcs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekap_barang as $rekap)
                        <tr>
                            <td>#{{ $rekap->id_stok }}</td>
                            <td class="slg-col-name">{{ $rekap->nama_stok }}</td>
                            <td class="text-center slg-col-qty slg-text-masuk">+{{ $rekap->total_masuk }}</td>
                            <td class="text-center slg-col-qty slg-text-keluar">-{{ $rekap->total_keluar }}</td>
                            <td class="text-center slg-col-qty" style="color: {{ ($rekap->total_masuk - $rekap->total_keluar) >= 0 ? 'var(--success-color)' : 'var(--warning-color)' }}">
                                {{ $rekap->total_masuk - $rekap->total_keluar }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="slg-empty">
                                    <i class="mdi mdi-archive-off"></i>
                                    <p>Tidak ada pergerakan stok pada periode ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Script inisialisasi Chart.js -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('stokChart').getContext('2d');
    
    // Data dari Controller Laravel
    const labels = {!! json_encode($labels) !!};
    const dataMasuk = {!! json_encode($data_masuk) !!};
    const dataKeluar = {!! json_encode($data_keluar) !!};

    const gradientMasuk = ctx.createLinearGradient(0, 0, 0, 400);
    gradientMasuk.addColorStop(0, 'rgba(81, 207, 102, 0.4)');
    gradientMasuk.addColorStop(1, 'rgba(81, 207, 102, 0.0)');

    const gradientKeluar = ctx.createLinearGradient(0, 0, 0, 400);
    gradientKeluar.addColorStop(0, 'rgba(255, 107, 107, 0.4)');
    gradientKeluar.addColorStop(1, 'rgba(255, 107, 107, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Stok Masuk',
                    data: dataMasuk,
                    borderColor: '#51cf66',
                    backgroundColor: gradientMasuk,
                    borderWidth: 3,
                    pointBackgroundColor: '#51cf66',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // kurva halus
                },
                {
                    label: 'Stok Keluar',
                    data: dataKeluar,
                    borderColor: '#ff6b6b',
                    backgroundColor: gradientKeluar,
                    borderWidth: 3,
                    pointBackgroundColor: '#ff6b6b',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: 'rgba(255,255,255,0.7)', font: { family: 'sans-serif' } }
                },
                tooltip: {
                    mode: 'index', intersect: false,
                    backgroundColor: 'rgba(30, 30, 46, 0.9)',
                    titleColor: '#fff', bodyColor: '#ccc',
                    borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1
                }
            },
            interaction: { mode: 'nearest', axis: 'x', intersect: false },
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false },
                    ticks: { color: 'rgba(255,255,255,0.5)' }
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false },
                    ticks: { color: 'rgba(255,255,255,0.5)', stepSize: 5 }
                }
            }
        }
    });
});
</script>
@endsection

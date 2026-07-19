@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/laporan_penjualan.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/cetak_tgl_pdf.css') }}">

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-printer"></i>
            </span> Laporan Penjualan Bulanan
        </h3>
    </div>

    <div class="row mb-2">
        <div class="col-12 grid-margin stretch-card">
            <div class="lp-card">
                <div class="lp-card-header">
                    <div class="lp-title-left">
                        <div class="lp-title-icon icon-danger">
                            <i class="mdi mdi-calendar-range"></i>
                        </div>
                        <h4>Pilih Filter Cetak PDF</h4>
                    </div>
                </div>

                <div class="ctgl-mode-tabs">
                    <button type="button" class="ctgl-mode-btn active" data-mode="tanggal" id="btnModeTanggal">
                        <i class="mdi mdi-calendar"></i> Per Tanggal
                    </button>
                    <button type="button" class="ctgl-mode-btn" data-mode="bulan" id="btnModeBulan">
                        <i class="mdi mdi-calendar-month"></i> Per Bulan
                    </button>
                </div>

                {{-- Filter Per Tanggal --}}
                <div id="filterTanggal" class="ctgl-filter-panel">
                    <div class="ctgl-form-grid">
                        <div class="ctgl-field-group">
                            <label class="ctgl-label" for="tglawal">
                                <i class="mdi mdi-calendar-start"></i> Tanggal Awal
                            </label>
                            <input name="tglawal" id="tglawal" class="ctgl-input" type="date">
                        </div>
                        <div class="ctgl-field-group">
                            <label class="ctgl-label" for="tglakhir">
                                <i class="mdi mdi-calendar-end"></i> Tanggal Akhir
                            </label>
                            <input name="tglakhir" id="tglakhir" class="ctgl-input" type="date">
                        </div>
                    </div>
                </div>

                {{-- Filter Per Bulan --}}
                <div id="filterBulan" class="ctgl-filter-panel" style="display: none;">
                    <div class="ctgl-form-grid">
                        <div class="ctgl-field-group">
                            <label class="ctgl-label" for="bulan">
                                <i class="mdi mdi-calendar-month"></i> Bulan
                            </label>
                            <select name="bulan" id="bulan" class="ctgl-input">
                                @php
                                    $namaBulan = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                    ];
                                    $bulanSekarang = (int) date('n');
                                @endphp
                                @foreach ($namaBulan as $num => $nama)
                                    <option value="{{ $num }}" {{ $num === $bulanSekarang ? 'selected' : '' }}>
                                        {{ $nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ctgl-field-group">
                            <label class="ctgl-label" for="tahun">
                                <i class="mdi mdi-calendar"></i> Tahun
                            </label>
                            <select name="tahun" id="tahun" class="ctgl-input">
                                @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="ctgl-action">
                    <a href="" id="btnCetak"
                       onclick="return handleCetak(this)"
                       class="lp-btn lp-btn-pdf ctgl-btn-cetak">
                        <i class="mdi mdi-printer"></i> Cetak Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let modeAktif = 'tanggal';

    document.querySelectorAll('.ctgl-mode-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            modeAktif = this.dataset.mode;
            document.querySelectorAll('.ctgl-mode-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            document.getElementById('filterTanggal').style.display = modeAktif === 'tanggal' ? '' : 'none';
            document.getElementById('filterBulan').style.display = modeAktif === 'bulan' ? '' : 'none';
        });
    });

    function handleCetak(el) {
        if (modeAktif === 'tanggal') {
            const tglawal  = document.getElementById('tglawal').value;
            const tglakhir = document.getElementById('tglakhir').value;

            if (!tglawal || !tglakhir) {
                alert('Harap isi Tanggal Awal dan Tanggal Akhir terlebih dahulu.');
                return false;
            }
            if (tglawal > tglakhir) {
                alert('Tanggal Awal tidak boleh lebih besar dari Tanggal Akhir.');
                return false;
            }

            el.href = '/cetak_range_pdf/' + tglawal + '/' + tglakhir;
            return true;
        }

        const bulan = document.getElementById('bulan').value;
        const tahun = document.getElementById('tahun').value;

        if (!bulan || !tahun) {
            alert('Harap pilih Bulan dan Tahun terlebih dahulu.');
            return false;
        }

        el.href = '/cetak_bulan_pdf/' + tahun + '/' + bulan;
        return true;
    }
</script>
@endsection

@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/laporan_penjualan.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/cetak_tgl_pdf.css') }}">

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-printer"></i>
            </span> Laporan Penjualan Per Jam
        </h3>
    </div>

    <div class="row mb-2">
        <div class="col-12 grid-margin stretch-card">
            <div class="lp-card">
                <div class="lp-card-header">
                    <div class="lp-title-left">
                        <div class="lp-title-icon icon-danger">
                            <i class="mdi mdi-clock-outline"></i>
                        </div>
                        <h4>Pilih Tanggal &amp; Rentang Jam</h4>
                    </div>
                </div>

                <div class="ctgl-form-grid ctgl-form-grid-3">
                    <div class="ctgl-field-group">
                        <label class="ctgl-label" for="tanggal">
                            <i class="mdi mdi-calendar"></i> Tanggal
                        </label>
                        <input name="tanggal" id="tanggal" class="ctgl-input" type="date" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="ctgl-field-group">
                        <label class="ctgl-label" for="jamawal">
                            <i class="mdi mdi-clock-start"></i> Jam Awal
                        </label>
                        <select name="jamawal" id="jamawal" class="ctgl-input">
                            @for ($i = 0; $i <= 23; $i++)
                                <option value="{{ $i }}" {{ $i === 0 ? 'selected' : '' }}>
                                    {{ sprintf('%02d:00', $i) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="ctgl-field-group">
                        <label class="ctgl-label" for="jamakhir">
                            <i class="mdi mdi-clock-end"></i> Jam Akhir
                        </label>
                        <select name="jamakhir" id="jamakhir" class="ctgl-input">
                            @for ($i = 0; $i <= 23; $i++)
                                <option value="{{ $i }}" {{ $i === 23 ? 'selected' : '' }}>
                                    {{ sprintf('%02d:59', $i) }}
                                </option>
                            @endfor
                        </select>
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
    function handleCetak(el) {
        const tanggal  = document.getElementById('tanggal').value;
        const jamawal  = document.getElementById('jamawal').value;
        const jamakhir = document.getElementById('jamakhir').value;

        if (!tanggal) {
            alert('Harap isi Tanggal terlebih dahulu.');
            return false;
        }
        if (parseInt(jamawal, 10) > parseInt(jamakhir, 10)) {
            alert('Jam Awal tidak boleh lebih besar dari Jam Akhir.');
            return false;
        }

        el.href = '/cetak_tgl_pdf/' + tanggal + '/' + jamawal + '/' + jamakhir;
        return true;
    }
</script>
@endsection

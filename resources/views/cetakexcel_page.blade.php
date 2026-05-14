@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/laporan_penjualan.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/cetak_tgl_pdf.css') }}">

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-success text-white me-2">
                <i class="mdi mdi-file-excel"></i>
            </span> Cetak Excel Penjualan Pertanggal
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
                        <h4>Pilih Rentang Tanggal</h4>
                    </div>
                </div>

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

                <div class="ctgl-action" style="gap: 16px;">
                    <a href="{{ route('exportexcel') }}" class="lp-btn lp-btn-excel ctgl-btn-cetak" style="background-color: #20c997; color: white; border: none;">
                        <i class="mdi mdi-file-excel"></i> Cetak Semua Data
                    </a>
                    <a href="" id="btnCetak"
                       onclick="return handleCetak(this)"
                       class="lp-btn lp-btn-excel ctgl-btn-cetak" style="background-color: #28a745; color: white; border: none;">
                        <i class="mdi mdi-file-excel"></i> Cetak Pertanggal
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

        el.href = '/exportexcel-tanggal?tgl_awal=' + tglawal + '&tgl_akhir=' + tglakhir;
        return true;
    }
</script>
@endsection

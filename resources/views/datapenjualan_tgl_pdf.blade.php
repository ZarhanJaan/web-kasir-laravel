@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-table-large menu-icon"></i>
            </span> Laporan Penjualan Pertanggal
          </h3>
        </div>
        <br><br>
        {{-- add produk --}}
        <div class="card">
            <div class="container" style="padding-bottom: 50px">
                  <div class="row" style="padding-top: 50px">
                        <div class="col-12 col-md-6">
                              <label for="">Tanggal Awal</label>
                              <input name="tglawal" id="tglawal" class="form-control" type="date">
                        </div>
                        <br>
                         <div class="col-12 col-md-6">
                              <label for="">Tanggal Akhir</label>
                              <input name="tglakhir" id="tglakhir" class="form-control" type="date">
                        </div>
                  </div>
                  <br>
                  <div class="row">
                       <a href="" onclick="this.href='/cetak_tgl_pdf/'+document.getElementById('tglawal').value + 
                       '/' + document.getElementById('tglakhir').value" 
                       class="btn bg-gradient-info text-white">Cetak Laporan</a>
                  </div>
            </div>
        </div>
    
@endsection



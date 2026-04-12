@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-history menu-icon"></i>
            </span> Riwayat Stok
          </h3>
        </div>

        @if (session('pesan_sukses'))
        <div class="alert alert-success" role="alert">
          <i class="fa fa-check"></i>
          {{ session('pesan_sukses') }}
        </div>     
        @endif

        <div class="card shadow p-3 mb-4 bg-body rounded">
            <div class="card-body">
                <form action="/stok/riwayat" method="GET" class="row gx-3 gy-2 align-items-center">
                    <div class="col-sm-4">
                        <label class="visually-hidden">Tanggal Awal</label>
                        <input type="date" name="tgl_awal" class="form-control" value="{{ $awal ?? '' }}" required placeholder="Tanggal Awal">
                    </div>
                    <div class="col-sm-4">
                        <label class="visually-hidden">Tanggal Akhir</label>
                        <input type="date" name="tgl_akhir" class="form-control" value="{{ $akhir ?? '' }}" required placeholder="Tanggal Akhir">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Filter Tanggal</button>
                    </div>
                    <div class="col-auto">
                        <a href="/stok/riwayat" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow p-3 mb-5 bg-body rounded">
            <div class="container">
                  <div class="table-responsive">
                  <table class="table table-bordered table-striped" id="mytable">
                        <thead class="bg-light">
                              <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Produk</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                              </tr>
                        </thead>
                        <tbody>
                              @foreach ($riwayat as $index => $data)
                              <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $data->nama_produk ?? $data->nama_stok }}</td>
                                    <td>
                                        @if($data->jenis == 'masuk')
                                            <span class="badge bg-success">Stok Masuk</span>
                                        @else
                                            <span class="badge bg-danger">Stok Keluar</span>
                                        @endif
                                    </td>
                                    <td>{{ $data->jumlah }}</td>
                                    <td>{{ $data->keterangan ?? '-' }}</td>
                              </tr>
                              @endforeach
                        </tbody>
                  </table>
                  </div>
            </div>
        </div>
      </div>
@endsection

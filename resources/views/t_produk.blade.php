@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/menu.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-silverware menu-icon"></i>
                </span> Menu
            </h3>
            @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                <nav aria-label="breadcrumb">
                    <a href="/stok" class="mu-btn-header">
                        <i class="mdi mdi-package-variant"></i> Daftar Stok
                    </a>
                </nav>
            @endif
        </div>

        @if (session('pesan_sukses'))
            <div class="mu-alert-success">
                <i class="mdi mdi-check-circle"></i> {{ session('pesan_sukses') }}
            </div>
        @endif

        @if (session('pesan_hapus'))
            <div class="mu-alert-danger">
                <i class="mdi mdi-delete"></i> {{ session('pesan_hapus') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="mu-table-card" data-aos="fade-up" data-aos-duration="800">
                    <div class="card-body">
                        <div class="card-title-row">
                            <div class="title-icon">
                                <i class="mdi mdi-silverware"></i>
                            </div>
                            <h4>Daftar Menu</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="mu-table" id="mytable">
                                <thead>
                                    <tr>
                                        <th>ID Menu</th>
                                        <th>Nama Menu</th>
                                        <th>Harga Jual</th>
                                        <th>Kategori Menu</th>
                                        <th>Tgl. Input</th>
                                        @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                                            <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($produk as $data)
                                        <tr>
                                            <td>{{ $data->id_produk }}</td>
                                            <td class="menu-name">{{ $data->nama_produk }}</td>
                                            <td class="menu-price">Rp.{{ number_format($data->harga_jual, 0, ',', '.') }}</td>
                                            <td>
                                                <span class="mu-badge {{ $data->kategori == 'Makanan' ? 'mu-badge-info' : 'mu-badge-success' }}">
                                                    {{ $data->kategori }}
                                                </span>
                                            </td>
                                            <td style="font-size: 12px; color: #94a3b8; white-space: nowrap;">
                                                @if($data->created_at)
                                                    <i class="mdi mdi-calendar-check" style="color: #6366f1;"></i>
                                                    {{ \Carbon\Carbon::parse($data->created_at)->locale('id')->isoFormat('D MMM YYYY') }}
                                                    <br>
                                                    <small style="color: #cbd5e1;">{{ \Carbon\Carbon::parse($data->created_at)->format('H:i') }}</small>
                                                @else
                                                    <span style="color: #cbd5e1;">—</span>
                                                @endif
                                            </td>
                                            @if(Auth::user()->hasAnyRole(['owner', 'admin']))
                                                <td>
                                                    <a href="/menu/edit/{{ $data->id_produk }}"
                                                        class="mu-btn mu-btn-warning" title="Edit">
                                                        <i class="mdi mdi-border-color"></i>
                                                    </a>
                                                    <a href="/menu/delete/{{ $data->id_produk }}"
                                                        class="mu-btn mu-btn-danger"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')" title="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>
                                                </td>
                                            @endif
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
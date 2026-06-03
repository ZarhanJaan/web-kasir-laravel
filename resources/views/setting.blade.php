@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/setting.css') }}">

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title d-flex align-items-center">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-cog"></i>
            </span> Aplikasi Setting
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <span>Pengaturan <i class="mdi mdi-alert-circle-outline icon-sm align-middle"></i></span>
                </li>
            </ul>
        </nav>
    </div>

    @if(session('success'))
        <div class="st-alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="st-alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li><i class="mdi mdi-alert-circle"></i> {{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">

        {{-- Card 1: Pengaturan Nama Web --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="st-card">
                <div class="st-title-row">
                    <div class="st-title-icon">
                        <i class="mdi mdi-store"></i>
                    </div>
                    <h4>Pengaturan Nama Web</h4>
                </div>
                <p class="st-card-description">Nama ini akan ditampilkan pada navbar dan judul web.</p>
                <hr class="st-divider">

                <form action="{{ route('setting.update-store-name') }}" method="POST">
                    @csrf
                    <div class="st-form-group">
                        <label for="store_name" class="st-form-label">
                            <i class="mdi mdi-pencil-outline me-1"></i> Nama Web Saat Ini
                        </label>
                        <input type="text" name="store_name" id="store_name"
                            class="st-input"
                            value="{{ $store_name ?? 'Toko Sembako' }}" required>
                    </div>

                    <div class="st-form-group mt-3">
                        <label for="store_address" class="st-form-label">
                            <i class="mdi mdi-map-marker-outline me-1"></i> Alamat Web/Toko
                        </label>
                        <textarea name="store_address" id="store_address"
                            class="st-input" rows="3" required>{{ $store_address ?? 'Jl. Contoh Alamat No.123' }}</textarea>
                    </div>

                    <hr class="st-divider">

                    <button type="submit" class="st-btn-save">
                        <i class="mdi mdi-content-save"></i> Simpan Informasi
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

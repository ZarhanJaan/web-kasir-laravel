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

                    <hr class="st-divider">

                    <button type="submit" class="st-btn-save">
                        <i class="mdi mdi-content-save"></i> Simpan Nama
                    </button>
                </form>
            </div>
        </div>

        {{-- Card 2: Pengaturan QRIS --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="st-card">
                <div class="st-title-row">
                    <div class="st-title-icon icon-qris">
                        <i class="mdi mdi-qrcode"></i>
                    </div>
                    <h4>Pengaturan Pembayaran (QRIS)</h4>
                </div>
                <p class="st-card-description">Akan ditampilkan saat proses pembayaran di menu kasir.</p>
                <hr class="st-divider">

                <form action="{{ route('setting.update-qris') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Preview QRIS --}}
                    <div class="st-form-group">
                        <label class="st-form-label">
                            <i class="mdi mdi-image-outline me-1"></i> Gambar QRIS Saat Ini
                        </label>
                        <div class="st-qris-preview">
                            @if(isset($qris_image) && file_exists(public_path($qris_image)))
                                @if(isset($qris_name))
                                    <p class="st-qris-name">{{ $qris_name }}</p>
                                @endif
                                <img src="{{ asset($qris_image) }}" alt="QRIS" class="st-qris-img">
                            @else
                                <div class="st-qris-empty">
                                    <i class="mdi mdi-qrcode-scan"></i>
                                    <p>Belum ada gambar QRIS.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Upload QRIS --}}
                    <div class="st-form-group">
                        <label for="qris_image" class="st-form-label">
                            <i class="mdi mdi-upload me-1"></i> Upload / Ubah QRIS Baru
                        </label>
                        <input type="file" name="qris_image" id="qris_image"
                            class="st-input"
                            accept=".jpg,.jpeg,.png,.webp" required>
                        <small class="st-input-hint">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Maks. ukuran 2MB. Hanya format JPG, JPEG, PNG, WEBP.
                        </small>
                    </div>

                    <hr class="st-divider">

                    <button type="submit" class="st-btn-save">
                        <i class="mdi mdi-content-save"></i> Simpan QRIS
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

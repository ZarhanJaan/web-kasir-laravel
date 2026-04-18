@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-settings menu-icon"></i>
            </span> Aplikasi Setting
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <span>Pengaturan <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i></span>
                </li>
            </ul>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li><i class="mdi mdi-alert-circle me-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row" data-aos="fade-up" data-aos-duration="1000">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card shadow-sm border-0 rounded-4 glass-card" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-body">
                    <h4 class="card-title text-primary font-weight-bold mb-4">Pengaturan Nama Web</h4>
                    <p class="card-description text-muted">Nama ini akan ditampilkan pada navbar dan judul web</p>

                    <form action="{{ route('setting.update-store-name') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="store_name" class="font-weight-bold text-dark">Nama Web Saat Ini</label>
                            <input type="text" name="store_name" id="store_name" class="form-control" value="{{ $store_name ?? 'Toko Sembako' }}" required>
                        </div>
                        
                        <hr class="border-light mt-4 mb-4">

                        <button type="submit" class="btn btn-gradient-primary me-2 btn-lg">
                            <i class="mdi mdi-content-save me-1"></i> Simpan Nama
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 grid-margin stretch-card">
            <div class="card shadow-sm border-0 rounded-4 glass-card" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-body">
                    <h4 class="card-title text-primary font-weight-bold mb-4">Pengaturan Pembayaran (QRIS)</h4>
                    <p class="card-description text-muted">Akan ditampilkan saat proses pembayaran di menu kasir</p>

                    <form action="{{ route('setting.update-qris') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Gambar QRIS Saat Ini</label>
                            <div class="border rounded p-3 text-center bg-light" style="min-height: 200px; display:flex; align-items:center; justify-content:center;">
                                @if(isset($qris_image) && file_exists(public_path($qris_image)))
                                    <div>
                                        @if(isset($qris_name))
                                            <p class="text-primary font-weight-bold mb-2">{{ $qris_name }}</p>
                                        @endif
                                        <img src="{{ asset($qris_image) }}" alt="QRIS" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                                    </div>
                                @else
                                    <div class="text-muted">
                                        <i class="mdi mdi-qrcode-scan d-block mb-2" style="font-size: 3rem;"></i>
                                        Belum ada gambar QRIS.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="qris_image" class="font-weight-bold text-dark">Upload / Ubah QRIS Baru</label>
                            <input type="file" name="qris_image" id="qris_image" class="form-control" accept=".jpg,.jpeg,.png,.webp" required>
                            <small class="text-muted d-block mt-2">Maks. ukuran 2MB. Hanya format JPG, JPEG, PNG, WEBP.</small>
                        </div>
                        
                        <hr class="border-light mt-4 mb-4">

                        <button type="submit" class="btn btn-gradient-primary me-2 btn-lg">
                            <i class="mdi mdi-content-save me-1"></i> Simpan QRIS
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/manajemen_user.css') }}">

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account-multiple"></i>
                </span> Manajemen User
            </h3>
        </div>

        @if(session('success'))
            <div class="mu-alert-success alert alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="mu-alert-danger alert alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="mu-table-card" data-aos="fade-up" data-aos-duration="800">
                    <div class="card-body">
                        <div class="card-title-row">
                            <div class="title-icon">
                                <i class="mdi mdi-account-multiple"></i>
                            </div>
                            <h4>Daftar User</h4>
                        </div>
                        <p class="card-description">Kelola peran (role) aplikasi untuk setiap pengguna.</p>
                        <div class="table-responsive">
                            <table class="mu-table" id="mytable">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role Saat Ini</th>
                                        <th>Ubah Role</th>
                                        <th>Hapus</th>
                                        <th>Simpan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td class="user-name">{{ $user->name }}</td>
                                            <td class="user-email">{{ $user->email }}</td>
                                            <td>
                                                <span class="mu-badge {{ $user->role ? 'mu-badge-success' : 'mu-badge-warning' }}">
                                                    {{ $user->role->role ?? 'Menunggu Role' }}
                                                </span>
                                            </td>
                                            <form action="{{ route('manajemen-user.update') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <td>
                                                    @if(auth()->user()->role && auth()->user()->role->role == 'admin' && $user->role && $user->role->role == 'owner')
                                                        <span class="mu-denied"><i class="mdi mdi-lock"></i> Akses ditolak</span>
                                                    @else
                                                        <select name="role_id" class="mu-select" required>
                                                            <option value="">Pilih Role</option>
                                                            @foreach($roles as $role)
                                                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                                    {{ ucfirst($role->role) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(auth()->user()->role && auth()->user()->role->role == 'admin' && $user->role && $user->role->role == 'owner')
                                                        <span class="mu-denied">—</span>
                                                    @else
                                                        <a href="{{ route('manajemen-user.delete', $user->id) }}"
                                                            class="mu-btn mu-btn-danger"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                            <i class="mdi mdi-delete"></i> Hapus
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(auth()->user()->role && auth()->user()->role->role == 'admin' && $user->role && $user->role->role == 'owner')
                                                        <span class="mu-denied">—</span>
                                                    @else
                                                        <button type="submit" class="mu-btn mu-btn-primary">
                                                            <i class="mdi mdi-content-save"></i> Simpan
                                                        </button>
                                                    @endif
                                                </td>
                                            </form>
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
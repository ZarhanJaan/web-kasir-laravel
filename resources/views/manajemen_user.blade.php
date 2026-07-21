@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/manajemen_user.css') }}">
<style>
    .mu-status-form { margin: 0; }
    .mu-status-switch { position: relative; display: inline-block; width: 58px; height: 30px; vertical-align: middle; }
    .mu-status-switch input { opacity: 0; width: 0; height: 0; }
    .mu-status-slider { position: absolute; inset: 0; cursor: pointer; background: #dc3545; border-radius: 30px; transition: .2s; }
    .mu-status-slider:before { content: ''; position: absolute; width: 22px; height: 22px; left: 4px; bottom: 4px; background: white; border-radius: 50%; transition: .2s; }
    .mu-status-switch input:checked + .mu-status-slider { background: #28a745; }
    .mu-status-switch input:checked + .mu-status-slider:before { transform: translateX(28px); }
</style>

<div class="content-wrapper">
    <div class="page-header"><h3 class="page-title"><span class="page-title-icon bg-gradient-primary text-white me-2"><i class="mdi mdi-account-multiple"></i></span> Manajemen User</h3></div>

    @if(session('success'))
        <div class="mu-alert-success alert alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif
    @if(session('error'))
        <div class="mu-alert-danger alert alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif

    <div class="row"><div class="col-12 grid-margin stretch-card"><div class="mu-table-card" data-aos="fade-up" data-aos-duration="800"><div class="card-body">
        <div class="card-title-row"><div class="title-icon"><i class="mdi mdi-account-multiple"></i></div><h4>Daftar User</h4></div>
        <p class="card-description">Kelola peran dan status akses untuk setiap pengguna.</p>
        <div class="table-responsive"><table class="mu-table" id="mytable">
            <thead><tr><th>Nama</th><th>Email</th><th>Role Saat Ini</th><th>Ubah Role</th><th>Status</th><th>Simpan</th></tr></thead>
            <tbody>
                @foreach($users as $user)
                    @php($protectedOwner = auth()->user()->role && auth()->user()->role->role == 'admin' && $user->role && $user->role->role == 'owner')
                    <tr>
                        <td class="user-name">{{ $user->name }}</td>
                        <td class="user-email">{{ $user->email }}</td>
                        <td><span class="mu-badge {{ $user->role ? 'mu-badge-success' : 'mu-badge-warning' }}">{{ $user->role->role ?? 'Menunggu Role' }}</span></td>
                        <td>
                            @if($protectedOwner)
                                <span class="mu-denied"><i class="mdi mdi-lock"></i> Akses ditolak</span>
                            @else
                                <select name="role_id" form="role-form-{{ $user->id }}" class="mu-select" required>
                                        <option value="">Pilih Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ ucfirst($role->role) }}</option>
                                        @endforeach
                                </select>
                            @endif
                        </td>
                        <td>
                            @if(auth()->id() == $user->id || $protectedOwner)
                                <span class="mu-denied"><i class="mdi mdi-lock"></i> Tidak dapat diubah</span>
                            @else
                                <form action="{{ route('manajemen-user.status') }}" method="POST" class="mu-status-form">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <label class="mu-status-switch" title="{{ $user->status ? 'Aktif' : 'Tidak aktif' }}">
                                        <input type="checkbox" {{ $user->status ? 'checked' : '' }} onchange="this.form.submit()">
                                        <span class="mu-status-slider"></span>
                                    </label>
                                    <span>{{ $user->status ? 'Aktif' : 'Tidak aktif' }}</span>
                                </form>
                            @endif
                        </td>
                        <td>
                            @if($protectedOwner)
                                <span class="mu-denied">-</span>
                            @else
                                <form id="role-form-{{ $user->id }}" action="{{ route('manajemen-user.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="mu-btn mu-btn-primary"><i class="mdi mdi-content-save"></i> Simpan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table></div>
    </div></div></div></div>
</div>
@endsection

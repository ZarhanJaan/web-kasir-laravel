@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account-multiple"></i>
                </span> Manajemen User
            </h3>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Daftar User</h4>
                        <p class="card-description"> Kelola peran (role) aplikasi untuk setiap pengguna. </p>
                        <div class="table-responsive">
                            <table class="table table-hover" id="mytable">
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
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <label
                                                    class="badge {{ $user->role ? 'badge-gradient-success' : 'badge-gradient-warning' }}">
                                                    {{ $user->role->role ?? 'Menunggu Role' }}
                                                </label>
                                            </td>
                                            <form action="{{ route('manajemen-user.update') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <td>
                                                    @if(auth()->user()->role && auth()->user()->role->role == 'admin' && $user->role && $user->role->role == 'owner')
                                                        <span class="badge badge-secondary" style="color: gray">Akses ditolak</span>
                                                    @else
                                                        <select name="role_id" class="form-control form-control-sm" required>
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
                                                        <span class="badge badge-secondary" style="color: gray">-</span>
                                                    @else
                                                        <a href="{{ route('manajemen-user.delete', $user->id) }}"
                                                            class="btn btn-gradient-danger btn-sm"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                            Hapus
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(auth()->user()->role && auth()->user()->role->role == 'admin' && $user->role && $user->role->role == 'owner')
                                                        <span class="badge badge-secondary" style="color: gray">-</span>
                                                    @else
                                                        <button type="submit" class="btn btn-gradient-primary btn-sm">
                                                            Simpan
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
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/pages.css') }}">

<div class="content-wrapper">

  {{-- Page Header --}}
  <div class="page-header">
    <h3 class="page-title">
      <span class="page-title-icon bg-gradient-primary text-white me-2">
        <i class="mdi mdi-account-multiple"></i>
      </span> Manajemen User
    </h3>
  </div>

  {{-- Flash Messages --}}
  @if(session('success'))
    <div class="page-alert-success">
      <strong><i class="mdi mdi-check-circle me-1"></i>Berhasil!</strong> {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="page-alert-danger">
      <strong><i class="mdi mdi-alert-circle me-1"></i>Error!</strong> {{ session('error') }}
    </div>
  @endif

  {{-- Table Card --}}
  <div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="page-card" style="width:100%;">
        <div class="bg-circle"></div>
        <div class="card-body">

          <div class="page-card-header">
            <div class="page-card-title">
              <div class="title-icon">
                <i class="mdi mdi-account-cog"></i>
              </div>
              <div>
                <h4>Daftar Pengguna</h4>
                <p>Kelola peran (role) aplikasi untuk setiap pengguna</p>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="page-table" id="mytable">
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
                    <td class="text-primary-val">{{ $user->name }}</td>
                    <td style="color: var(--text-muted);">{{ $user->email }}</td>
                    <td>
                      @if($user->role)
                        <span class="badge-glass badge-glass-success">
                          <i class="mdi mdi-shield-check"></i> {{ ucfirst($user->role->role) }}
                        </span>
                      @else
                        <span class="badge-glass badge-glass-warning">
                          <i class="mdi mdi-clock-outline"></i> Menunggu Role
                        </span>
                      @endif
                    </td>
                    <form action="{{ route('manajemen-user.update') }}" method="POST">
                      @csrf
                      <input type="hidden" name="user_id" value="{{ $user->id }}">
                      <td>
                        <select name="role_id" class="role-select" required>
                          <option value="">Pilih Role</option>
                          @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                              {{ ucfirst($role->role) }}
                            </option>
                          @endforeach
                        </select>
                      </td>
                      <td>
                        <a href="{{ route('manajemen-user.delete', $user->id) }}"
                           class="btn-act btn-act-delete"
                           onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                          <i class="mdi mdi-delete"></i> Hapus
                        </a>
                      </td>
                      <td>
                        <button type="submit" class="btn-act btn-act-save">
                          <i class="mdi mdi-content-save"></i> Simpan
                        </button>
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
@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-account-multiple menu-icon"></i>
            </span> Edit User
          </h3>
        </div>
        <br><br>
        {{-- edit user --}}
        <form action="/user/update/{{ $user->id }}" method="POST">
            @csrf
            <div class="row g-3">
            <div class="col-12 col-md-6">
                        <label for="">Nama Pengguna</label>
                        <input name="name" class="form-control" value="{{ $user->name }}">
                        <div class="text-danger">
                              @error('name')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                        <div class="text-danger">
                              @error('email')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Password Baru (Biarkan kosong jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control" value="">
                        <div class="text-danger">
                              @error('password')
                                  {{ $message }}
                              @enderror
                        </div>
            </div>
            </div>
            <br>
            <div class="row">
                  <div class="col-12 col-md-6">
                        <button class="btr btn bg-gradient-info text-white">Update</button>
                        <a href="/user" class="btn btn-outline-secondary ms-2">Batal</a>
                  </div>
            </div>
        </form>
        
       
      </div>

@endsection

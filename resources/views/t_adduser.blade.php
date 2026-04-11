@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-account-multiple menu-icon"></i>
            </span> Add User
          </h3>
        </div>
        <br><br>
        {{-- add user --}}
        <form action="/user/insert" method="POST">
            @csrf
            <div class="row g-3">
            <div class="col-12 col-md-6">
                        <label for="">Nama Pengguna</label>
                        <input name="name" class="form-control" value="{{ old('name') }}">
                        <div class="text-danger">
                              @error('name')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        <div class="text-danger">
                              @error('email')
                                  {{ $message }}
                              @enderror
                        </div>
                        <br>
                        <label for="">Password</label>
                        <input type="password" name="password" class="form-control" value="{{ old('password') }}">
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
                        <button class="btr btn bg-gradient-info text-white">Simpan</button>
                        <a href="/user" class="btn btn-outline-secondary ms-2">Batal</a>
                  </div>
            </div>
        </form>
        
       
      </div>

@endsection

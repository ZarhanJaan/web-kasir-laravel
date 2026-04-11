@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-account-multiple menu-icon"></i>
            </span> Manajemen User
          </h3>
          <nav aria-label="breadcrumb">
            <a href="/user/add" class="btn bg-gradient-primary text-white">
                  Add +
            </a>
          </nav>
        </div>
         @if (session('pesan_sukses'))
        <div class="alert alert-success" role="alert">
          <i class="fa fa-edit"></i>
            {{session('pesan_sukses')}}
        </div>     
        @endif
        @if (session('pesan_hapus'))
        <div class="alert alert-danger" role="alert">
          <i class="fa fa-trash-o"></i>
            {{session('pesan_hapus')}}
        </div>     
        @endif
        {{-- table user --}}
        <div class="card shadow p-3 mb-5 bg-body rounded">
            <div class="container">
                  <div class="table-responsive">
                  <table class="table table-bordered" id="mytable">
                        <thead>
                              <tr>
                                    <th>No</th>
                                    <th>Nama Pengguna</th>
                                    <th>Email</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Action</th>
                              </tr>
                        </thead>
                        <tbody>
                              <?php $no = 1; ?>
                              @foreach ($users as $data)
                              <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{$data->name}}</td>
                                    <td>{{$data->email}}</td>
                                    <td>{{$data->created_at->format('d M Y')}}</td>
                                    <td>
                                          <a href="/user/edit/{{ $data->id }}" class="btn btn-gradient-warning">
                                                <i class="fa fa-edit"></i>
                                          </a>
                                          <button type="button" class="btn btn-gradient-danger" data-bs-toggle="modal" data-bs-target="#delete{{ $data->id }}">
                                                <i class="fa fa-trash-o"></i>
                                          </button>
                                    </td>
                              </tr>
                              @endforeach
                        </tbody>
                  </table>

                  @foreach ($users as $data)
                  <!-- Modal -->
                  <div class="modal fade" id="delete{{ $data->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header bg-gradient-primary text-white">
                              <h5 class="modal-title" id="delete">{{ $data->name }}</h5>
                              <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                              <p>Apakah Anda yakin ingin menghapus user ini?</p>
                        </div>
                        <div class="modal-footer">
                              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                              <a href="/user/delete/{{ $data->id }}" class="btn btn-gradient-danger">Delete</a>
                        </div>
                        </div>
                        </div>
                  </div>
                  @endforeach

            </div>
            </div>
        </div>
        
       
      </div>
    
    @endsection

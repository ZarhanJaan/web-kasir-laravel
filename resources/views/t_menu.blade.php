@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-book-open-page-variant menu-icon"></i>
            </span> Manajemen Menu
          </h3>
          <nav aria-label="breadcrumb">
            <a href="/menu/add" class="btn bg-gradient-primary text-white">
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
        @if (session('pesan_error'))
        <div class="alert alert-danger" role="alert">
          <i class="fa fa-warning"></i>
            {{session('pesan_error')}}
        </div>     
        @endif

        {{-- table menu --}}
        <div class="card shadow p-3 mb-5 bg-body rounded">
            <div class="container">
                  <div class="table-responsive">
                  <table class="table table-bordered" id="mytable">
                        <thead>
                              <tr>
                                    <th>ID Menu</th>
                                    <th>Nama Menu</th>
                                    <th>Harga Menu</th>
                                    <th>Resep/Komposisi</th>
                                    <th>Action</th>
                              </tr>
                        </thead>
                        <tbody>
                              @foreach ($menus as $data)
                              <tr>
                                    <td>{{$data->id_menu}}</td>
                                    <td>{{$data->nama_menu}}</td>
                                    <td>Rp.{{$data->harga_menu}}</td>
                                    <td>
                                        <ul>
                                            @foreach($data->details as $detail)
                                                <li>{{ $detail->produk ? $detail->produk->nama_produk : $detail->id_produk }} ({{ $detail->jumlah_dipakai }})</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                          <a href="/menu/edit/{{ $data->id_menu }}" class="btn btn-gradient-warning mb-1">
                                                <i class="fa fa-edit"></i>
                                          </a>
                                          <button type="button" class="btn btn-gradient-danger  mb-1" data-bs-toggle="modal" data-bs-target="#delete{{ $data->id_menu }}">
                                                <i class="fa fa-trash-o"></i>
                                          </button>
                                    </td>
                              </tr>
                              @endforeach
                        </tbody>
                  </table>

                  @foreach ($menus as $data)
                  <!-- Modal -->
                  <div class="modal fade" id="delete{{ $data->id_menu }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header bg-gradient-primary text-white">
                              <h5 class="modal-title" id="delete">{{ $data->nama_menu }}</h5>
                              <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                              <p>Apakah Anda yakin ingin menghapus data tersebut?</p>
                        </div>
                        <div class="modal-footer">
                              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                              <a href="/menu/delete/{{ $data->id_menu }}" class="btn btn-gradient-danger">Delete</a>
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

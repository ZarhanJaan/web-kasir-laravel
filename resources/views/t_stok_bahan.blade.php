@extends('layouts.app')

@section('content')

      <div class="content-wrapper">
            <div class="page-header">
                  <h3 class="page-title">
                        <span class="page-title-icon bg-gradient-primary text-white me-2">
                              <i class="mdi mdi-package-variant menu-icon"></i>
                        </span> Manajemen Bahan Baku (Inventory)
                  </h3>
                  <nav aria-label="breadcrumb">
                        <button type="button" class="btn bg-gradient-primary text-white" data-bs-toggle="modal"
                              data-bs-target="#addBahanModal">
                              + Bahan Baru
                        </button>
                  </nav>
            </div>

            @if (session('pesan_sukses'))
                  <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> {{ session('pesan_sukses') }}
                  </div>
            @endif

            <div class="row">
                  <div class="col-12 grid-margin">
                        <div class="card shadow p-3 mb-5 bg-body rounded">
                              <div class="card-body">
                                    <h4 class="card-title text-primary"><i class="mdi mdi-format-list-bulleted"></i> Daftar
                                          Stok Bahan Baku</h4>
                                    <div class="table-responsive">
                                          <table class="table table-bordered" id="mytable">
                                                <thead>
                                                      <tr>
                                                            <th>ID</th>
                                                            <th>Nama Bahan</th>
                                                            <th>Stok Saat Ini</th>
                                                            <th>Satuan</th>
                                                            <th>Action</th>
                                                      </tr>
                                                </thead>
                                                <tbody>
                                                      @foreach ($bahan as $data)
                                                            <tr>
                                                                  <td>{{ $data->id_stok }}</td>
                                                                  <td><b>{{ $data->nama_stok }}</b></td>
                                                                  <td>{{ $data->stok }}</td>
                                                                  <td>{{ $data->satuan }}</td>
                                                                  <td>
                                                                        <a href="/stok/bahan/delete/{{ $data->id_stok }}"
                                                                              class="btn btn-sm btn-danger"
                                                                              onclick="return confirm('Apakah Anda yakin ingin menghapus bahan ini? Ini mungkin merusak resep menu yang sudah ada.')"><i
                                                                                    class="mdi mdi-delete"></i></a>
                                                                  </td>
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

      <!-- Modal Add Bahan -->
      <div class="modal fade" id="addBahanModal" tabindex="-1" aria-labelledby="addBahanModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                  <div class="modal-content">
                        <form action="/stok/bahan/insert" method="POST">
                              @csrf
                              <div class="modal-header">
                                    <h5 class="modal-title" id="addBahanModalLabel">Tambah Bahan Baku Baru</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                          aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                    <div class="mb-3">
                                          <label class="form-label">ID Bahan (Manual)</label>
                                          <input type="number" name="id_stok" class="form-control" placeholder="Contoh: 101"
                                                required>
                                    </div>
                                    <div class="mb-3">
                                          <label class="form-label">Nama Bahan</label>
                                          <input type="text" name="nama_stok" class="form-control"
                                                placeholder="Contoh: Minyak Goreng, Beras, Telur" required>
                                    </div>
                                    <div class="mb-3">
                                          <label class="form-label">Satuan</label>
                                          <select name="satuan" class="form-control" required>
                                                <option value="Pcs">Pcs / Biji</option>
                                                <option value="Bungkus">Bungkus</option>
                                                <option value="Unit">Unit</option>
                                                <option value="Kg">Kg (Kilogram)</option>
                                                <option value="Gram">Gram</option>
                                                <option value="Liter">Liter</option>
                                                <option value="Box">Box / Kardus</option>
                                          </select>
                                    </div>
                                    <div class="mb-3">
                                          <label class="form-label">Stok Awal (Opsional)</label>
                                          <input type="number" name="stok" class="form-control" value="0">
                                    </div>
                              </div>
                              <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Bahan</button>
                              </div>
                        </form>
                  </div>
            </div>
      </div>

@endsection
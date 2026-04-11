<!DOCTYPE html>
<html>
<head>
<style>
#customers {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd;}

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: blueviolet;
  color: white;
}
</style>
</head>
<body>

<h1>Data Penjualan</h1>

<table id="customers">
  <tr>
      <th>ID Penjualan</th>
      <th>Tanggal</th>
      <th>Nama Pelanggan</th>
      <th>Jumlah Barang</th>
      <th>Total Harga</th>
      <th>Produk</th>
  </tr>
 @foreach ($cetakpertanggal as $row)<tr>
<td>{{$row->id_penjualan}}</td>
<td>{{$row->tanggal}}</td>
<td>{{$row->nama_pelanggan}}</td>
<td>{{$row->jumlah_barang}}</td>
<td>Rp.{{$row->total}}</td>
<td>
      @php
      $produkIds = explode(',', $row->id_produk);
      $namaProduk = \App\Models\ProdukModel::whereIn('id_produk', $produkIds)->pluck('nama_produk')->toArray();
      echo implode(', ', $namaProduk);
      @endphp
</td>
@endforeach
</table>

</body>
</html>



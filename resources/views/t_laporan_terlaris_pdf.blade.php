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

    .header {
        text-align: center;
        margin-bottom: 30px;
    }
</style>
</head>
<body>

<div class="header">
    <h1>Laporan Menu Terlaris</h1>
    <p>Tanggal Cetak: {{ date('d-m-Y') }}</p>
</div>

<table id="customers">
  <thead>
    <tr>
        <th style="width: 10%">No</th>
        <th>Nama Menu</th>
        <th style="width: 30%">Total Terjual (Porsi/Item)</th>
    </tr>
  </thead>
  <tbody>
    @php $no = 1; @endphp
    @foreach ($data as $row)
    <tr>
        <td>{{ $no++ }}</td>
        <td>{{ $row->nama_produk }}</td>
        <td>{{ $row->total_terjual }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

</body>
</html>

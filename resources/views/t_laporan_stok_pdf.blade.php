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
        background-color: #4bc0c0;
        color: white;
    }

    .header {
        text-align: center;
        margin-bottom: 30px;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 4px;
        color: white;
        font-size: 10px;
    }
    .bg-success { background-color: #28a745; }
    .bg-danger { background-color: #dc3545; }
</style>
</head>
<body>

<div class="header">
    <h1>Laporan Mutasi Stok (Masuk & Keluar)</h1>
    <p>Periode: {{ date('d-m-Y', strtotime($tgl_mulai)) }} s/d {{ date('d-m-Y') }}</p>
</div>

<table id="customers">
  <thead>
    <tr>
        <th style="width: 5%">No</th>
        <th>Tanggal</th>
        <th>Nama Barang/Menu</th>
        <th>Jenis</th>
        <th>Jumlah</th>
        <th>Nama Pelanggan / Keterangan</th>
    </tr>
  </thead>
  <tbody>
    @php $no = 1; @endphp
    @foreach ($data as $row)
    <tr>
        <td>{{ $no++ }}</td>
        <td>{{ date('d-m-Y', strtotime($row->tanggal)) }}</td>
        <td>{{ $row->nama_stok ?? $row->nama_produk ?? '-' }}</td>
        <td style="text-align: center;">
            <span class="badge {{ $row->jenis == 'masuk' ? 'bg-success' : 'bg-danger' }}">
                {{ strtoupper($row->jenis) }}
            </span>
        </td>
        <td style="text-align: right;">{{ $row->jumlah }}</td>
        <td>{{ $row->nama_pelanggan ?: $row->keterangan }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

</body>
</html>

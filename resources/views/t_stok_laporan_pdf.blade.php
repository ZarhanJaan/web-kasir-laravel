<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok {{ $jenis === 'masuk' ? 'Masuk' : 'Keluar' }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            color: #555;
            font-size: 13px;
        }
        .badge-jenis {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 8px;
        }
        .badge-masuk {
            background-color: #e6f9ec;
            color: #2b8a3e;
            border: 1px solid #51cf66;
        }
        .badge-keluar {
            background-color: #ffe3e3;
            color: #c92a2a;
            border: 1px solid #ff6b6b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 12px;
        }
        .text-masuk {
            color: green;
        }
        .text-keluar {
            color: red;
        }
    </style>
</head>
<body>

    @php
        $isMasuk = $jenis === 'masuk';
        $judulJenis = $isMasuk ? 'Masuk' : 'Keluar';
    @endphp

    <div class="header">
        <h2>Laporan Rekapitulasi Stok {{ $judulJenis }}</h2>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($tgl_awal)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($tgl_akhir)->translatedFormat('d F Y') }}</strong></p>
        <span class="badge-jenis {{ $isMasuk ? 'badge-masuk' : 'badge-keluar' }}">Stok {{ $judulJenis }}</span>
    </div>

    <p style="margin-bottom: 10px;">Rekapitulasi total stok {{ strtolower($judulJenis) }} per barang pada periode di atas:</p>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">ID Stok</th>
                <th style="width: 55%;">Nama Barang</th>
                <th style="width: 30%;">Total {{ $judulJenis }} (Pcs)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap_barang as $rekap)
                <tr>
                    <td class="text-center">#{{ $rekap->id_stok }}</td>
                    <td>{{ $rekap->nama_stok }}</td>
                    <td class="text-center {{ $isMasuk ? 'text-masuk' : 'text-keluar' }}">
                        {{ $isMasuk ? '+' : '-' }}{{ number_format($rekap->total, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Tidak ada stok {{ strtolower($judulJenis) }} pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i:s') }}</p>
    </div>

</body>
</html>

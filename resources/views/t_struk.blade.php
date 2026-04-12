<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran</title>
    <style>
        body { font-family: monospace; width: 300px; margin: 0 auto; color: #000; padding: 20px 0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mb-2 { margin-bottom: 10px; }
        .mt-2 { margin-top: 10px; }
        hr { border-top: 1px dashed #000; }
        table { width: 100%; }
        td { display: table-cell; }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center mb-2">
        <h2>Restoran</h2>
        <p>Jl. Contoh Alamat No.123<br>Telp: 08123456789</p>
    </div>
    <hr>
    <p>TRX ID: TRX-{{ $trx->id_penjualan }}<br>
    Tanggal: {{ date('d-m-Y H:i', strtotime($trx->tanggal)) }}<br>
    Pelanggan: {{ $trx->nama_pelanggan }}</p>
    <hr>
    <table>
        @foreach($produk as $p)
        <tr>
            <td colspan="3">{{ $p->nama_produk }}</td>
        </tr>
        <tr>
            <!-- Kita tidak menyimpan rincian per-item qty secara rapi di struktur DB lama, 
            namun karena struktur tabel eksisting mengharuskan ID digabung, kita cukup 
            tampilkan produk terkait. Jika ingin persis qty per produk, biasanya butuh pivot table t_detail_penjualan. 
            Disini dirender versi simpel. -->
            <td>Rp{{ number_format($p->harga_jual,0,',','.') }}</td>
            <td class="text-right">x1</td>
            <td class="text-right">Rp{{ number_format($p->harga_jual,0,',','.') }}</td>
        </tr>
        @endforeach
    </table>
    <hr>
    <table>
        <tr>
            <td><strong>TOTAL BELANJA</strong></td>
            <td class="text-right"><strong>Rp{{ number_format($trx->total,0,',','.') }}</strong></td>
        </tr>
        <tr>
            <td>Metode Pembayaran</td>
            <td class="text-right">{{ $trx->metode_pembayaran }}</td>
        </tr>
    </table>
    <hr>
    <div class="text-center mt-2">
        <p>TERIMA KASIH ATAS KUNJUNGAN ANDA</p>

    </div>
</body>
</html>

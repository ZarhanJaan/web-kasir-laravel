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

        /* Print button — hidden when printing */
        .btn-print {
            display: block;
            width: 100%;
            margin-top: 16px;
            padding: 10px;
            background: #111;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-family: monospace;
            cursor: pointer;
            letter-spacing: 0.5px;
        }
        .btn-print:hover { background: #333; }

        @media print {
            .btn-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="text-center mb-2">
        <h2>{{ $store_name }}</h2>
        <p>{!! nl2br(e($store_address)) !!}</p>
    </div>
    <hr>
    <p>TRX ID: TRX-{{ $trx->id_penjualan }}<br>
    Tanggal: {{ date('d-m-Y H:i', strtotime($trx->tanggal)) }}<br>
    Pelanggan: {{ $trx->nama_pelanggan }}</p>
    <hr>
    <table>
        @php
            $jumlahs = explode(',', $trx->jumlah_barang);
        @endphp
        @foreach($produk as $index => $p)
        <tr>
            <td colspan="3">{{ $p->nama_produk }}</td>
        </tr>
        <tr>
            @php
                $qty = $jumlahs[$index] ?? 1;
            @endphp
            <td>Rp{{ number_format($p->harga_jual,0,',','.') }}</td>
            <td class="text-right">x{{ $qty }}</td>
            <td class="text-right">Rp{{ number_format($p->harga_jual * $qty,0,',','.') }}</td>
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

    <button class="btn-print" onclick="window.print()">🖨 CETAK / PRINT</button>
</body>
</html>

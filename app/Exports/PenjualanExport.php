<?php

namespace App\Exports;

use App\Models\PenjualanModel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PenjualanExport implements FromArray, ShouldAutoSize
{
    /**
     * Data baris Excel — tanpa header baris judul.
     * Jika satu transaksi punya lebih dari 1 produk,
     * setiap produk ditulis di baris baru.
     * Kolom: A=id_penjualan, B=tanggal, C=nama_pelanggan,
     *        D=jumlah_barang, E=id_produk, F=nama_menu,
     *        G=total, H=metode_pembayaran, I=created_at, J=updated_at
     */
    public function array(): array
    {
        $penjualan = PenjualanModel::all();
        $rows = [];

        foreach ($penjualan as $p) {
            $idProdukList = array_map('trim', explode(',', $p->id_produk));
            $jumlahList   = array_map('trim', explode(',', $p->jumlah_barang));

            foreach ($idProdukList as $i => $idProduk) {
                // Get quantity for this specific item if available
                $jumlah = $jumlahList[$i] ?? '';

                // Ambil nama produk dari tabel t_produk
                $produk     = DB::table('t_produk')->where('id_produk', $idProduk)->first();
                $namaProduk = $produk ? $produk->nama_produk : '-';

                if ($i === 0) {
                    // Baris pertama: isi semua kolom termasuk created_at & updated_at
                    $rows[] = [
                        $p->id_penjualan,
                        $p->tanggal,
                        $p->nama_pelanggan,
                        $jumlah,
                        $idProduk,
                        $namaProduk,
                        $p->total,
                        $p->metode_pembayaran,
                        $p->created_at,
                        $p->updated_at,
                    ];
                } else {
                    // Baris produk tambahan: isi kolom D, E, F agar sejajar
                    $rows[] = [
                        '',
                        '',
                        '',
                        $jumlah, // Kolom D (Jumlah)
                        $idProduk, // Kolom E (ID Produk)
                        $namaProduk, // Kolom F (Nama Menu)
                        '',
                        '',
                        '',
                        '',
                    ];
                }
            }
        }
        
        // Add total row at the end
        $totalKeseluruhan = $penjualan->sum('total');
        $rows[] = [
            'Total Keseluruhan',
            '',
            '',
            '',
            '',
            '',
            $totalKeseluruhan,
            '',
            '',
            '',
        ];

        return $rows;
    }
}

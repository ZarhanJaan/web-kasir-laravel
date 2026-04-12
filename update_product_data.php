<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::transaction(function () {
        // 1. Delete products NOT in Makanan or Minuman categories
        $deleted = DB::table('t_produk')
            ->whereNotIn('kategori', ['Makanan', 'Minuman'])
            ->delete();
        echo "Deleted $deleted products that were not in 'Makanan' or 'Minuman' categories.\n";

        // 2. Add some Beverage (Minuman) products
        $beverages = [
            [
                'id_produk' => '3001',
                'nama_produk' => 'Kopi Kapal Api 165g',
                'stok' => 0,
                'harga_beli' => 0,
                'harga_jual' => 12500,
                'kategori' => 'Minuman',
                'satuan' => '-'
            ],
            [
                'id_produk' => '3002',
                'nama_produk' => 'Air Mineral Aqua 600ml',
                'stok' => 0,
                'harga_beli' => 0,
                'harga_jual' => 4000,
                'kategori' => 'Minuman',
                'satuan' => '-'
            ],
            [
                'id_produk' => '3003',
                'nama_produk' => 'Teh Pucuk Harum 350ml',
                'stok' => 0,
                'harga_beli' => 0,
                'harga_jual' => 4500,
                'kategori' => 'Minuman',
                'satuan' => '-'
            ],
            [
                'id_produk' => '3004',
                'nama_produk' => 'Susu Beruang (Bear Brand)',
                'stok' => 0,
                'harga_beli' => 0,
                'harga_jual' => 10500,
                'kategori' => 'Minuman',
                'satuan' => '-'
            ],
            [
                'id_produk' => '3005',
                'nama_produk' => 'Kopi Good Day Freeze',
                'stok' => 0,
                'harga_beli' => 0,
                'harga_jual' => 3000,
                'kategori' => 'Minuman',
                'satuan' => '-'
            ]
        ];

        foreach ($beverages as $bev) {
            // Check if ID already exists to avoid unique constraint error
            if (!DB::table('t_produk')->where('id_produk', $bev['id_produk'])->exists()) {
                DB::table('t_produk')->insert($bev);
                echo "Added: " . $bev['nama_produk'] . "\n";
            } else {
                echo "Skipped (ID exists): " . $bev['nama_produk'] . "\n";
            }
        }
    });
    echo "Product data cleanup and beverage additions completed successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

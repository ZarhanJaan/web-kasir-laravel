<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

try {
    // 1. Create t_stok_item (The Ingredients/Inventory)
    if (!Schema::hasTable('t_stok_item')) {
        Schema::create('t_stok_item', function (Blueprint $table) {
            $table->id('id_stok');
            $table->string('nama_stok');
            $table->integer('stok')->default(0);
            $table->string('satuan')->default('Pcs');
            $table->timestamps();
        });
        echo "Table t_stok_item created.\n";
    }

    // 2. Create t_menu_resep (The Recipe mapping)
    if (!Schema::hasTable('t_menu_resep')) {
        Schema::create('t_menu_resep', function (Blueprint $table) {
            $table->id('id_resep');
            $table->string('id_menu'); // id_produk from t_produk
            $table->unsignedBigInteger('id_stok');
            $table->integer('jumlah')->default(1);
            $table->timestamps();
            
            // Note: id_menu refers to t_produk.id_produk which is a string (e.g. 2551)
        });
        echo "Table t_menu_resep created.\n";
    }

    // 3. Migrate existing product stock to stock items (Init 1:1)
    $products = DB::table('t_produk')->get();
    foreach ($products as $p) {
        // Check if stock item already exists for this product name
        $existingStok = DB::table('t_stok_item')->where('nama_stok', $p->nama_produk)->first();
        if (!$existingStok) {
            $id_stok = DB::table('t_stok_item')->insertGetId([
                'nama_stok' => $p->nama_produk,
                'stok' => $p->stok,
                'satuan' => $p->satuan ?? '-',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "Migrated Stock: " . $p->nama_produk . " (" . $p->stok . ")\n";
        } else {
            $id_stok = $existingStok->id_stok;
        }

        // Check if recipe already exists
        $existingResep = DB::table('t_menu_resep')->where('id_menu', $p->id_produk)->where('id_stok', $id_stok)->first();
        if (!$existingResep) {
            DB::table('t_menu_resep')->insert([
                'id_menu' => $p->id_produk,
                'id_stok' => $id_stok,
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "Created 1:1 Recipe for: " . $p->nama_produk . "\n";
        }
    }

    // 4. Update t_riwayat_stok table to support id_stok instead of id_produk (optional? No, user wants riwayat to track ingredients)
    // Actually, I'll keep t_riwayat_stok mapping to id_produk for past sales, 
    // but for STOCK MOVEMENTS (Masuk/Keluar manual), it should now use id_stok.
    // I'll add id_stok column to t_riwayat_stok.
    if (!Schema::hasColumn('t_riwayat_stok', 'id_stok')) {
        Schema::table('t_riwayat_stok', function (Blueprint $table) {
            $table->unsignedBigInteger('id_stok')->nullable()->after('id_produk');
        });
        echo "Added id_stok column to t_riwayat_stok.\n";
    }

    echo "Data migration completed successfully.\n";

} catch (\Exception $e) {
    echo "Error during migration: " . $e->getMessage() . "\n";
    exit(1);
}

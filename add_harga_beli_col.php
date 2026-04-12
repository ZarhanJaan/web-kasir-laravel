<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasColumn('t_riwayat_stok', 'harga_beli')) {
    Schema::table('t_riwayat_stok', function (Blueprint $table) {
        $table->decimal('harga_beli', 15, 2)->nullable()->after('jumlah');
    });
    echo "Column 'harga_beli' added successfully to 't_riwayat_stok'.\n";
} else {
    echo "Column 'harga_beli' already exists in 't_riwayat_stok'.\n";
}

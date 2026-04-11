<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasColumn('t_riwayat_stok', 'total_harga')) {
    Schema::table('t_riwayat_stok', function (Blueprint $table) {
        $table->integer('total_harga')->nullable();
    });
    echo "Column total_harga added successfully.";
} else {
    echo "Column total_harga already exists.";
}

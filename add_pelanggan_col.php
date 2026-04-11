<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasColumn('t_riwayat_stok', 'nama_pelanggan')) {
    Schema::table('t_riwayat_stok', function (Blueprint $table) {
        $table->string('nama_pelanggan')->nullable();
    });
    echo "Column nama_pelanggan added successfully.";
} else {
    echo "Column nama_pelanggan already exists.";
}

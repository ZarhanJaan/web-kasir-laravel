<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasColumn('t_penjualan', 'metode_pembayaran')) {
    Schema::table('t_penjualan', function (Blueprint $table) {
        $table->string('metode_pembayaran')->nullable()->default('Cash');
    });
    echo "Column metode_pembayaran added successfully.";
} else {
    echo "Column metode_pembayaran already exists.";
}

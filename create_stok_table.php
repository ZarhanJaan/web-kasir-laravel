<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasTable('t_riwayat_stok')) {
    Schema::create('t_riwayat_stok', function (Blueprint $table) {
        $table->increments('id_riwayat');
        $table->string('id_produk', 10);
        $table->enum('jenis', ['masuk', 'keluar']);
        $table->integer('jumlah');
        $table->string('keterangan')->nullable();
        $table->date('tanggal');
        $table->timestamps();
    });
    echo "Table t_riwayat_stok created successfully.";
} else {
    echo "Table t_riwayat_stok already exists.";
}

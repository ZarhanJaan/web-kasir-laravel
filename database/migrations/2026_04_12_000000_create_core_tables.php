<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoreTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. t_produk
        if (!Schema::hasTable('t_produk')) {
            Schema::create('t_produk', function (Blueprint $table) {
                $table->string('id_produk')->primary();
                $table->string('nama_produk');
                $table->integer('harga_jual');
                $table->integer('stok');
                $table->string('satuan');
                $table->string('gambar')->nullable();
            });
        }

        // 2. t_penjualan
        if (!Schema::hasTable('t_penjualan')) {
            Schema::create('t_penjualan', function (Blueprint $table) {
                $table->string('id_penjualan')->primary();
                $table->date('tanggal');
                $table->string('nama_pelanggan');
                $table->integer('jumlah_barang');
                $table->text('id_produk'); // Comma separated IDs
                $table->double('total');
                $table->string('metode_pembayaran')->nullable();
                $table->timestamps();
            });
        }

        // 3. t_stok_item
        if (!Schema::hasTable('t_stok_item')) {
            Schema::create('t_stok_item', function (Blueprint $table) {
                $table->id('id_stok');
                $table->string('nama_stok');
                $table->integer('stok')->default(0);
                $table->string('satuan')->default('Pcs');
                $table->timestamps();
            });
        }

        // 4. t_riwayat_stok
        if (!Schema::hasTable('t_riwayat_stok')) {
            Schema::create('t_riwayat_stok', function (Blueprint $table) {
                $table->bigInteger('id_riwayat')->primary();
                $table->string('id_produk')->nullable();
                $table->unsignedBigInteger('id_stok')->nullable();
                $table->string('jenis'); // masuk / keluar
                $table->integer('jumlah');
                $table->double('total_harga')->default(0);
                $table->date('tanggal');
                $table->text('keterangan')->nullable();
                $table->string('nama_pelanggan')->nullable();
                $table->timestamps();
            });
        }

        // 5. t_menu_resep
        if (!Schema::hasTable('t_menu_resep')) {
            Schema::create('t_menu_resep', function (Blueprint $table) {
                $table->id('id_resep');
                $table->string('id_menu');
                $table->unsignedBigInteger('id_stok');
                $table->integer('jumlah')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_menu_resep');
        Schema::dropIfExists('t_riwayat_stok');
        Schema::dropIfExists('t_stok_item');
        Schema::dropIfExists('t_penjualan');
        Schema::dropIfExists('t_produk');
    }
}

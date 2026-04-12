<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetodePembayaranToTPenjualan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('t_penjualan')) {
            Schema::table('t_penjualan', function (Blueprint $table) {
                if (!Schema::hasColumn('t_penjualan', 'metode_pembayaran')) {
                    $table->string('metode_pembayaran')->nullable()->after('total');
                }
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
        if (Schema::hasTable('t_penjualan')) {
            Schema::table('t_penjualan', function (Blueprint $table) {
                $table->dropColumn('metode_pembayaran');
            });
        }
    }
}

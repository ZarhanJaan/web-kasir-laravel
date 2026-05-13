<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSatuanToTRiwayatStok extends Migration
{
    public function up()
    {
        Schema::table('t_riwayat_stok', function (Blueprint $table) {
            $table->string('satuan')->nullable()->after('jumlah');
        });
    }

    public function down()
    {
        Schema::table('t_riwayat_stok', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });
    }
}

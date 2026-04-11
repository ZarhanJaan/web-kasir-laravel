<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTMenuDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_menu_detail', function (Blueprint $table) {
            $table->id();
            $table->string('id_menu', 6);
            $table->string('id_produk', 6);
            $table->integer('jumlah_dipakai');
            
            $table->foreign('id_menu')->references('id_menu')->on('t_menu')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_menu_detail');
    }
}

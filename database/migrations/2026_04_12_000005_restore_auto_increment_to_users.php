<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RestoreAutoIncrementToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to avoid Doctrine DBAL dependency for column modification
        DB::statement('ALTER TABLE users MODIFY id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT');
        
        DB::statement('ALTER TABLE t_penjualan MODIFY id_penjualan INT(10) UNSIGNED NOT NULL AUTO_INCREMENT');
        
        DB::statement('ALTER TABLE t_stok_item MODIFY id_stok BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT');
        
        DB::statement('ALTER TABLE t_menu_resep MODIFY id_resep BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT');
        
        DB::statement('ALTER TABLE t_riwayat_stok MODIFY id_riwayat INT(10) UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No reverse needed as we are fixing a broken state
    }
}

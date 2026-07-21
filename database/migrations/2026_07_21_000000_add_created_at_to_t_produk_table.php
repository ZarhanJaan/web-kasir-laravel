<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedAtToTProdukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('t_produk')) {
            Schema::table('t_produk', function (Blueprint $table) {
                if (!Schema::hasColumn('t_produk', 'created_at')) {
                    $table->timestamp('created_at')->nullable()->useCurrent();
                }
                if (!Schema::hasColumn('t_produk', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
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
        if (Schema::hasTable('t_produk')) {
            Schema::table('t_produk', function (Blueprint $table) {
                if (Schema::hasColumn('t_produk', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('t_produk', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
}

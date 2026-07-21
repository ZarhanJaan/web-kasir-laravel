<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndCreatorAuditColumns extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'status')) {
                $table->boolean('status')->default(true)->after('role_id');
            }
        });

        foreach (['t_menu_resep', 't_produk', 't_penjualan', 't_riwayat_stok'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'created_by_id')) {
                    $table->unsignedBigInteger('created_by_id')->nullable()->index();
                }
                if (!Schema::hasColumn($tableName, 'created_by_name')) {
                    $table->string('created_by_name')->nullable();
                }
            });
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        foreach (['t_menu_resep', 't_produk', 't_penjualan', 't_riwayat_stok'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['created_by_id', 'created_by_name']);
            });
        }
    }
}

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Removing AUTO_INCREMENT from tables...\n";

try {
    // Disable foreign keys check to avoid issues
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // 1. t_penjualan (Existing: int(6) NOT NULL AUTO_INCREMENT)
    DB::statement('ALTER TABLE t_penjualan MODIFY id_penjualan INT(6) NOT NULL');
    echo "- Table t_penjualan updated.\n";

    // 2. t_stok_item (Existing: bigint(20) unsigned NOT NULL AUTO_INCREMENT)
    DB::statement('ALTER TABLE t_stok_item MODIFY id_stok BIGINT(20) UNSIGNED NOT NULL');
    echo "- Table t_stok_item updated.\n";

    // 3. t_menu_resep (Existing: bigint(20) unsigned NOT NULL AUTO_INCREMENT)
    DB::statement('ALTER TABLE t_menu_resep MODIFY id_resep BIGINT(20) UNSIGNED NOT NULL');
    echo "- Table t_menu_resep updated.\n";

    // 4. t_riwayat_stok (Existing: int(10) unsigned NOT NULL AUTO_INCREMENT)
    DB::statement('ALTER TABLE t_riwayat_stok MODIFY id_riwayat INT(10) UNSIGNED NOT NULL');
    echo "- Table t_riwayat_stok updated.\n";

    // 5. users (Existing: bigint(20) unsigned NOT NULL AUTO_INCREMENT)
    DB::statement('ALTER TABLE users MODIFY id BIGINT(20) UNSIGNED NOT NULL');
    echo "- Table users updated.\n";

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "DONE: AUTO_INCREMENT removed successfully from all core tables.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    exit(1);
}

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$ids = [13, 14];
echo "Deleting records with IDs: " . implode(', ', $ids) . "\n";

$deleted = DB::table('t_riwayat_stok')->whereIn('id_riwayat', $ids)->delete();

echo "Deleted " . $deleted . " records from t_riwayat_stok.\n";

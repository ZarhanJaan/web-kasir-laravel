<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$riwayat = DB::table('t_riwayat_stok')->orderBy('created_at', 'desc')->limit(2)->get();

foreach($riwayat as $r) {
    echo "ID: " . $r->id_riwayat . " | Item: " . ($r->id_stok ?? 'NULL') . " | Qty: " . $r->jumlah . " | Desc: " . $r->keterangan . "\n";
}

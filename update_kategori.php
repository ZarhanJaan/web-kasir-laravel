<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$produks = DB::table('t_produk')->get();
foreach($produks as $p) {
    $kategori = 'Sembako';
    if(str_contains(strtolower($p->nama_produk), 'indomie')){
        $kategori = 'Makanan';
    } elseif(str_contains(strtolower($p->nama_produk), 'kecap') || str_contains(strtolower($p->nama_produk), 'gula merah')){
        $kategori = 'Bumbu';
    } elseif(str_contains(strtolower($p->nama_produk), 'minuman') || str_contains(strtolower($p->nama_produk), 'kopi')){
        $kategori = 'Minuman';
    }
    
    DB::table('t_produk')->where('id_produk', $p->id_produk)->update(['kategori' => $kategori]);
}
echo "Done";

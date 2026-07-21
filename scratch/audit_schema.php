<?php
// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
foreach ($tables as $t) {
    $t    = (array)$t;
    $name = array_values($t)[0];
    echo "TABLE: $name\n";
    $cols = DB::select("SHOW FULL COLUMNS FROM `$name`");
    foreach ($cols as $c) {
        $key  = $c->Key     ?? '';
        $null = $c->Null    ?? '';
        $def  = $c->Default ?? 'NULL';
        $extra= $c->Extra   ?? '';
        echo "  [{$c->Field}]  type={$c->Type}  null=$null  key=$key  default=$def  extra=$extra\n";
    }
    echo "\n";
}

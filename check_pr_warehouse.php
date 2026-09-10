<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

$pr = DB::table('purchase_returns')->where('id', 5)->first();
if ($pr) {
    echo "id=5 warehouse_id=" . ($pr->warehouse_id ?? 'NULL') . "\n";
    echo "id=5 branch_id=" . ($pr->branch_id ?? 'NULL') . "\n";
    echo "id=5 purchase_id=" . ($pr->purchase_id ?? 'NULL') . "\n";
}

// Check purchase for this return
if ($pr && $pr->purchase_id) {
    $p = DB::table('purchases')->where('id', $pr->purchase_id)->first();
    if ($p) {
        echo "Purchase id={$p->id} warehouse_id=" . ($p->warehouse_id ?? 'NULL') . " purchase_to=" . ($p->purchase_to ?? 'NULL') . "\n";
    }
}

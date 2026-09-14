<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$negVariants = DB::table('product_variants')->where('stock_qty', '<', 0)->get();
echo "Negative stock_qty in product_variants table: " . count($negVariants) . "\n";
foreach ($negVariants as $nv) {
    echo "ID: {$nv->id}, Product ID: {$nv->product_id}, Name: {$nv->size_label}, stock_qty: {$nv->stock_qty}\n";
}

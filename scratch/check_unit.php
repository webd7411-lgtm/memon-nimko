<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$product = DB::table('products')->where('id', 83)->first();
echo "item_name: {$product->item_name}\n";
echo "unit_id: " . ($product->unit_id ?? 'NULL') . "\n";
echo "unit_type: " . ($product->unit_type ?? 'NULL') . "\n";

// Check what columns products table has related to unit
$cols = DB::select("SHOW COLUMNS FROM products LIKE '%unit%'");
echo "\nUnit-related columns:\n";
foreach ($cols as $c) {
    echo "  {$c->Field} — default: {$c->Default}\n";
}

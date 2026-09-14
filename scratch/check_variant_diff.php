<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Product 83 = 2 in 1 Biscuits
echo "=== ALL STOCK ROWS for product_id=83 ===\n";
$rows = DB::table('stocks')->where('product_id', 83)->get();
foreach ($rows as $r) {
    echo "  id:{$r->id}  qty:{$r->qty}  variant_id:" . ($r->variant_id ?? 'NULL') . 
         "  branch_id:{$r->branch_id}  warehouse_id:" . ($r->warehouse_id ?? 'NULL') . "\n";
}

echo "\n=== SUM where warehouse_id IS NULL (branch only) ===\n";
$sum = DB::table('stocks')->where('product_id', 83)->whereNull('warehouse_id')->sum('qty');
echo "  Total grams: $sum → " . ($sum/1000) . " KG\n";

echo "\n=== SUM by variant_id ===\n";
$byVariant = DB::table('stocks')
    ->where('product_id', 83)
    ->whereNull('warehouse_id')
    ->selectRaw('variant_id, SUM(qty) as total')
    ->groupBy('variant_id')
    ->get();
foreach ($byVariant as $bv) {
    echo "  variant_id:" . ($bv->variant_id ?? 'NULL') . "  total:" . $bv->total . "g = " . ($bv->total/1000) . " KG\n";
}

echo "\n=== product_variants for product_id=83 ===\n";
$variants = DB::table('product_variants')->where('product_id', 83)->get();
foreach ($variants as $v) {
    echo "  id:{$v->id}  name:{$v->variant_name}  stock_qty:{$v->stock_qty}  size_value:{$v->size_value}  size_unit:{$v->size_unit}\n";
}

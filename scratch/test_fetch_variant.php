<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$productId = 83;

$variants = DB::table('product_variants')
    ->join('products', 'products.id', '=', 'product_variants.product_id')
    ->select('product_variants.*', 'products.unit_type')
    ->where('products.id', $productId)
    ->get();

$stocksQuery = DB::table('stocks')->where('product_id', $productId)->whereNull('warehouse_id');
$rawStocks = $stocksQuery->get();

echo "Raw Stocks from DB:\n";
print_r($rawStocks);

$stocksMap = [];
$nullStocksMap = [];
foreach ($rawStocks as $st) {
    $pid = $st->product_id;
    if ($st->variant_id) {
        $stocksMap[$pid][$st->variant_id] = ($stocksMap[$pid][$st->variant_id] ?? 0) + (float)$st->qty;
    } else {
        $nullStocksMap[$pid] = ($nullStocksMap[$pid] ?? 0) + (float)$st->qty;
    }
}

echo "stocksMap:\n";
print_r($stocksMap);

echo "nullStocksMap:\n";
print_r($nullStocksMap);

foreach ($variants as $v) {
    echo "Variant ID: {$v->id}, stock_qty field in product_variants table: {$v->stock_qty}\n";
    $vid = $v->id;
    $hasInMap = isset($stocksMap[$productId][$vid]);
    echo "Is in stocksMap? " . ($hasInMap ? "YES (qty: " . $stocksMap[$productId][$vid] . ")" : "NO") . "\n";
    
    $rawStock = isset($stocksMap[$productId][$vid]) ? (float)$stocksMap[$productId][$vid] : (float)$v->stock_qty;
    echo "Initial rawStock: $rawStock\n";
    
    if (isset($nullStocksMap[$productId])) {
        echo "Adding nullStocksMap: " . $nullStocksMap[$productId] . "\n";
        $rawStock += $nullStocksMap[$productId];
    }
    
    echo "Final rawStock: $rawStock grams\n";
    echo "Converted KG stock: " . ($rawStock / 1000) . " KG\n";
}

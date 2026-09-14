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
if (!is_all_branches()) {
    $stocksQuery->where('branch_id', active_branch_id());
}
$rawStocks = $stocksQuery->get();

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

foreach ($variants as $v) {
    $vid = $v->id;
    if (isset($stocksMap[$productId][$vid])) {
        $rawStock = (float)$stocksMap[$productId][$vid];
    } elseif (isset($nullStocksMap[$productId])) {
        $rawStock = 0;
    } else {
        $rawStock = (float)$v->stock_qty;
    }

    if (($v->is_default || count($variants) === 1) && isset($nullStocksMap[$productId])) {
        $rawStock += $nullStocksMap[$productId];
        unset($nullStocksMap[$productId]);
    }

    $isKg = $v->unit_type === 'kg';
    $stock = $isKg ? ($rawStock / 1000) : $rawStock;

    echo "Variant: {$v->size_label} | Final Raw Stock: {$rawStock} grams | Display Stock: {$stock} " . strtoupper($v->unit_type) . "\n";
}

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

// Check reset time
$resetTime = null;
if (\Illuminate\Support\Facades\Storage::exists('stock_reset_timestamp.txt')) {
    $resetTime = trim(\Illuminate\Support\Facades\Storage::get('stock_reset_timestamp.txt'));
    echo "Reset Time: $resetTime\n";
} else {
    echo "No Reset Time\n";
}

$startDT = '2026-09-01 00:00:00';
$endDT = '2026-09-30 23:59:59';
$pid = 227;

echo "\n=== ALL SALES for product 227 (any time) ===\n";
$allSales = DB::table('sales')->where('product', 'like', '%227%')->get();
foreach ($allSales as $s) {
    echo '  product=[' . $s->product . '] qty=' . $s->qty . ' variant=[' . ($s->variant_id ?? 'null') . '] created=' . $s->created_at . ' after_reset=' . ($s->created_at >= $resetTime ? 'YES' : 'NO') . "\n";
}

echo "\n=== SALES AFTER RESET TIME ===\n";
$salesAfter = DB::table('sales')->where('product', 'like', '%227%')->where('created_at', '>=', $resetTime)->get();
foreach ($salesAfter as $s) {
    echo '  product=[' . $s->product . '] qty=' . $s->qty . ' variant=[' . ($s->variant_id ?? 'null') . '] created=' . $s->created_at . "\n";
}

echo "\n=== ALL RETURNS for product 227 ===\n";
$allReturns = DB::table('sales_returns')
    ->leftJoin('products', 'products.item_name', '=', 'sales_returns.product')
    ->where('sales_returns.product', 'like', '%2 PC BUN%')
    ->select('sales_returns.*', 'products.id as pid')
    ->get();
foreach ($allReturns as $r) {
    echo '  id=' . $r->id . ' qty=' . $r->qty . ' sale_id=' . $r->sale_id . ' created=' . $r->created_at . ' after_reset=' . ($r->created_at >= $resetTime ? 'YES' : 'NO') . ' pid=' . ($r->pid ?? 'null') . "\n";
}

echo "\n=== RETURNS AFTER RESET TIME ===\n";
$returnsAfter = DB::table('sales_returns')
    ->leftJoin('products', 'products.item_name', '=', 'sales_returns.product')
    ->where('sales_returns.product', 'like', '%2 PC BUN%')
    ->where('sales_returns.created_at', '>=', $resetTime)
    ->select('sales_returns.*', 'products.id as pid')
    ->get();
foreach ($returnsAfter as $r) {
    echo '  id=' . $r->id . ' qty=' . $r->qty . ' sale_id=' . $r->sale_id . ' created=' . $r->created_at . ' pid=' . ($r->pid ?? 'null') . "\n";
}

echo "\n=== DB STOCK for product 227 ===\n";
$dbStock = DB::table('stocks')->where('product_id', $pid)->whereNull('warehouse_id')->sum('qty');
echo "DB Stock: $dbStock\n";

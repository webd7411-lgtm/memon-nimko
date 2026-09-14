<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$productId = 83;

// Reset time
$reset = null;

// Current stock in shop (stocks table)
$stocks = DB::table('stocks')->where('product_id', 83)->where('branch_id', 1)->get();
echo "Current Shop Stock (stocks table):\n";
print_r($stocks->toArray());

// Current stock in warehouse (warehouse_stocks table)
$whStocks = DB::table('warehouse_stocks')->where('product_id', 83)->get();
echo "Current Warehouse Stock (warehouse_stocks table):\n";
print_r($whStocks->toArray());

// Purchases
$purchases = DB::table('purchase_items')
    ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
    ->where('purchase_items.product_id', 83)
    ->select('purchases.id', 'purchases.purchase_date', 'purchases.warehouse_id', 'purchase_items.qty')
    ->get();
echo "\nPurchases:\n";
print_r($purchases->toArray());

// Production entries
$productions = DB::table('production_entry_items')
    ->join('production_entries', 'production_entries.id', '=', 'production_entry_items.production_entry_id')
    ->where('production_entry_items.product_id', 83)
    ->select('production_entries.id', 'production_entries.production_date', 'production_entries.warehouse_id', 'production_entry_items.qty_stock')
    ->get();
echo "\nProductions:\n";
print_r($productions->toArray());

// Transfers
$transfers = DB::table('stock_transfers')->get();
echo "\nAll Transfers:\n";
foreach ($transfers as $tr) {
    $pids = json_decode($tr->product_id, true);
    if (is_string($pids)) $pids = json_decode($pids, true);
    if (is_array($pids) && in_array('83', $pids)) {
        echo "ID: {$tr->id}, date: {$tr->created_at}, from_wh: {$tr->from_warehouse_id}, transfer_to: {$tr->transfer_to}, to_wh: {$tr->to_warehouse_id}, branch_id: {$tr->branch_id}, to_branch: {$tr->to_branch_id}, qty: {$tr->quantity}\n";
    }
}

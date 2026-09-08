<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$results = DB::table('purchase_return_items')
    ->join('purchase_returns', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
    ->whereIn('purchase_return_items.product_id', [DB::raw("(SELECT id FROM products WHERE item_name = '2 PC BUN')")])
    ->select('purchase_return_items.*', 'purchase_returns.return_date', 'purchase_returns.branch_id', 'purchase_returns.purchase_id', 'purchase_returns.warehouse_id')
    ->get();

echo "Purchase return records:\n";
foreach ($results as $r) {
    echo "ID: " . $r->purchase_return_id . " | Product: " . $r->product_id . " | Qty: " . $r->qty . " | Date: " . $r->return_date . " | Branch: " . $r->branch_id . " | Warehouse: " . $r->warehouse_id . " | Purchase: " . $r->purchase_id . "\n";
}

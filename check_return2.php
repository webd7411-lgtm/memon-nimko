<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$productIds = [227]; // 2 PC BUN
$startDate = date('Y-m-01');
$endDate = date('Y-m-t');
$branchId = active_branch_id();

echo "Branch: $branchId, Range: $startDate - $endDate\n";

$query = DB::table('purchase_return_items')
    ->join('purchase_returns', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
    ->whereIn('purchase_return_items.product_id', $productIds)
    ->whereNull('purchase_returns.warehouse_id')
    ->where('purchase_returns.branch_id', $branchId)
    ->whereBetween('purchase_returns.return_date', [$startDate, $endDate]);

$results = $query->select('purchase_return_items.product_id', DB::raw('SUM(purchase_return_items.qty) as total_qty'))
    ->groupBy('purchase_return_items.product_id')
    ->get();

echo "Results:\n";
foreach ($results as $r) {
    echo "Product: " . $r->product_id . " | Qty: " . $r->total_qty . "\n";
}

<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Simulate ReportingController item_stock_report for product 83 with refined transfer logic
$startDT = '2026-09-01 00:00:00';
$endDT   = '2026-09-14 23:59:59';
$activeBranchId = 1;
$productId = 83;

$product = DB::table('products')->where('id', 83)->first();

// 1. Current stock (balance)
$currStockGrams = DB::table('stocks')
    ->where('product_id', 83)
    ->whereNull('warehouse_id')
    ->where('branch_id', $activeBranchId)
    ->sum('qty');

// 2. Transfers OUT (within range)
$transfersOut = DB::table('stock_transfers')
    ->where('status', 'completed')
    ->whereBetween('created_at', [$startDT, $endDT])
    ->where(function($q) {
        $q->whereNull('from_warehouse_id')
          ->orWhere('from_warehouse_id', '')
          ->orWhere('from_warehouse_id', 'Shop')
          ->orWhere('from_warehouse_id', 0);
    })
    ->where('branch_id', $activeBranchId)
    ->get();

$transferOutQty = 0;
foreach ($transfersOut as $tr) {
    $pids = json_decode($tr->product_id, true);
    if (is_string($pids)) $pids = json_decode($pids, true);
    $qtys = json_decode($tr->quantity, true);
    if (is_string($qtys)) $qtys = json_decode($qtys, true);
    if (is_array($pids)) {
        foreach ($pids as $i => $pid) {
            if ($pid == 83) {
                $transferOutQty += floatval($qtys[$i] ?? 0);
            }
        }
    }
}

// 3. Transfers IN (within range)
$transfersIn = DB::table('stock_transfers')
    ->where('status', 'completed')
    ->whereBetween('created_at', [$startDT, $endDT])
    ->where(function($q) use ($activeBranchId) {
        $q->where(function($sq) use ($activeBranchId) {
            $sq->where('transfer_to', 'branch')
               ->where('to_branch_id', $activeBranchId);
        })->orWhere(function($sq) use ($activeBranchId) {
            $sq->where('transfer_to', 'shop')
               ->where('branch_id', $activeBranchId);
        })->orWhere(function($sq) use ($activeBranchId) {
            $sq->where('to_branch_id', $activeBranchId)
               ->whereNull('to_warehouse_id');
        });
    })
    ->get();

$transferInQty = 0;
foreach ($transfersIn as $tr) {
    $pids = json_decode($tr->product_id, true);
    if (is_string($pids)) $pids = json_decode($pids, true);
    $qtys = json_decode($tr->quantity, true);
    if (is_string($qtys)) $qtys = json_decode($qtys, true);
    if (is_array($pids)) {
        foreach ($pids as $i => $pid) {
            if ($pid == 83) {
                $transferInQty += floatval($qtys[$i] ?? 0);
            }
        }
    }
}

echo "Current Stock (balance): " . ($currStockGrams / 1000) . " KG\n";
echo "Transfer Out: " . $transferOutQty . " KG\n";
echo "Transfer In: " . $transferInQty . " KG\n";

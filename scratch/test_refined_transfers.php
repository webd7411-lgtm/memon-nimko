<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$startDT = '2026-09-01 00:00:00';
$endDT   = '2026-09-14 23:59:59';
$activeBranchId = 1;
$productId = 83;

// ---- TRANSFER OUT QUERY ----
// Stock leaving shop: from_warehouse_id is null / 'Shop' AND branch_id = active_branch_id
$transferOutQuery = DB::table('stock_transfers')
    ->where('status', 'completed')
    ->whereBetween('created_at', [$startDT, $endDT])
    ->where(function($q) {
        $q->whereNull('from_warehouse_id')
          ->orWhere('from_warehouse_id', '')
          ->orWhere('from_warehouse_id', 'Shop')
          ->orWhere('from_warehouse_id', 0);
    });

if (!is_all_branches()) {
    $transferOutQuery->where('branch_id', $activeBranchId);
}

$transfersOut = $transferOutQuery->get();
echo "TRANSFERS OUT (count " . $transfersOut->count() . "):\n";
foreach ($transfersOut as $tr) {
    echo "ID: {$tr->id}, from_wh: {$tr->from_warehouse_id}, transfer_to: {$tr->transfer_to}, branch_id: {$tr->branch_id}, to_branch: {$tr->to_branch_id}, qty: {$tr->quantity}\n";
}

// ---- TRANSFER IN QUERY ----
// Stock entering shop: to_branch_id = active_branch_id AND (transfer_to = branch OR transfer_to = shop OR to_warehouse_id is null)
// AND status = completed
$transferInQuery = DB::table('stock_transfers')
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
    });

// Exclude transfers where source was ALSO shop of the SAME branch (which shouldn't happen, but safety check)
$transfersIn = $transferInQuery->get();
echo "\nTRANSFERS IN (count " . $transfersIn->count() . "):\n";
foreach ($transfersIn as $tr) {
    echo "ID: {$tr->id}, from_wh: {$tr->from_warehouse_id}, transfer_to: {$tr->transfer_to}, branch_id: {$tr->branch_id}, to_branch: {$tr->to_branch_id}, qty: {$tr->quantity}\n";
}

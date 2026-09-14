<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$productId = 83;
$startDT = '2026-09-01 00:00:00';
$endDT   = '2026-09-14 23:59:59';
$activeBranchId = 1;

$transferInQuery = DB::table('stock_transfers')
    ->where('transfer_to', 'branch')
    ->where('to_branch_id', $activeBranchId)
    ->whereBetween('created_at', [$startDT, $endDT]);

$transfersIn = $transferInQuery->get();
echo "Found " . $transfersIn->count() . " transfer in records:\n";
foreach ($transfersIn as $tr) {
    echo "ID: {$tr->id}, product_id: {$tr->product_id}, variant_id: {$tr->variant_id}, qty: {$tr->quantity}, created_at: {$tr->created_at}\n";
}

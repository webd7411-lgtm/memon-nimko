<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$productIds = [227];

$startDT = '2026-09-01 07:00';
$endDT = '2026-09-30 23:59';

$transferQuery = DB::table('stock_transfers')
    ->whereBetween('created_at', [$startDT, $endDT]);
$transferQuery->where('branch_id', 1);
$transfers = $transferQuery->get();

echo "Found: " . $transfers->count() . " transfers\n";
$mapTransfer = [];
foreach ($transfers as $tr) {
    $rawPids = is_array($tr->product_id) ? $tr->product_id : (json_decode($tr->product_id, true) ?: []);
    $rawQtys = is_array($tr->quantity) ? $tr->quantity : (json_decode($tr->quantity, true) ?: []);
    if (!is_array($rawPids)) $rawPids = ($rawPids !== null && $rawPids !== '') ? [$rawPids] : [];
    if (!is_array($rawQtys)) $rawQtys = ($rawQtys !== null && $rawQtys !== '') ? [$rawQtys] : [];
    echo "Transfer id=" . $tr->id . ", pids=" . count($rawPids) . ", qtys=" . count($rawQtys) . "\n";
    foreach ($rawPids as $i => $pid) {
        if (is_array($pid)) $pid = $pid[0] ?? '';
        $pid = trim($pid);
        if ($pid === '') continue;
        if (!in_array((int)$pid, $productIds)) {
            echo "  Filtered pid=$pid\n";
            continue;
        }
        $qtyRaw = $rawQtys[$i] ?? 0;
        if (is_array($qtyRaw)) $qtyRaw = $qtyRaw[0] ?? 0;
        $qty = floatval($qtyRaw);
        $key = $pid . '_0';
        $mapTransfer[$key] = ($mapTransfer[$key] ?? 0) + $qty;
        echo "  Added: key=$key qty=$qty total=" . ($mapTransfer[$key] ?? 0) . "\n";
    }
}
echo "Final: \n";
print_r($mapTransfer);

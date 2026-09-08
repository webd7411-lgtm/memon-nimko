<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$tr = DB::table('stock_transfers')->where('id', 9)->first();
echo "Raw product_id: " . $tr->product_id . "\n";
echo "Raw quantity: " . $tr->quantity . "\n";

$pids = is_array($tr->product_id) ? $tr->product_id : (json_decode($tr->product_id, true) ?: []);
$qtys = is_array($tr->quantity) ? $tr->quantity : (json_decode($tr->quantity, true) ?: []);
echo "Pids: ";
var_dump($pids);
echo "Qtys: ";
var_dump($qtys);

$productIds = [227];
$mapTransfer = [];
foreach ($pids as $i => $pid) {
    $pid = trim($pid);
    if ($pid === '') continue;
    if (!in_array((int)$pid, $productIds)) {
        echo "Filtered out pid: $pid (not in [227])\n";
        continue;
    }
    $qty = floatval($qtys[$i] ?? 0);
    $key = $pid . '_0';
    $mapTransfer[$key] = ($mapTransfer[$key] ?? 0) + $qty;
    echo "Added: pid=$pid qty=$qty key=$key total=" . ($mapTransfer[$key] ?? 0) . "\n";
}
echo "Final mapTransfer: ";
print_r($mapTransfer);

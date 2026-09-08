<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$controller = new App\Http\Controllers\ReportingController();
$request = new Illuminate\Http\Request([
    'product_id' => 'all',
    'start_date' => '2026-09-01',
    'end_date' => '2026-09-30',
]);

$response = $controller->fetchItemStock($request);
$data = $response->getData(true);

// Print only row 2 (2 PC BUN) details
foreach ($data['data'] as $r) {
    if (strpos($r['item_name'], '2 PC BUN') !== false) {
        echo "Item: " . $r['item_name'] . "\n";
        echo "Item Code: " . $r['item_code'] . "\n";
        echo "Initial: " . $r['initial_stock'] . "\n";
        echo "Produced: " . $r['produced'] . "\n";
        echo "Purchased: " . $r['purchased'] . "\n";
        echo "Purchase Return: " . $r['purchase_return'] . "\n";
        echo "Transfer Out: " . $r['transfer'] . "\n";
        echo "Transfer In: " . $r['transfer_in'] . "\n";
        echo "Adj+: " . $r['adj_increase'] . "\n";
        echo "Adj-: " . $r['adj_decrease'] . "\n";
        echo "Sold: " . $r['sold'] . "\n";
        echo "Sale Return: " . $r['sale_return'] . "\n";
        echo "STOCK QTY (Balance): " . $r['balance'] . "\n";
    }
}

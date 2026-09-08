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

$results = $controller->fetchItemStock($request);
$data = $results->getData(true);
echo "Total rows: " . count($data['data']) . "\n";
foreach ($data['data'] as $r) {
    if ($r['item_name'] == '2 PC BUN (2 PC BUN)' || $r['item_code'] == 'ITEM-0227') {
        echo "Item: " . $r['item_name'] . " (" . $r['item_code'] . ")\n";
        echo "Initial: " . $r['initial_stock'] . "\n";
        echo "Produced: " . $r['produced'] . "\n";
        echo "Purchased: " . $r['purchased'] . "\n";
        echo "Purchase Return: " . $r['purchase_return'] . "\n";
        echo "Sold: " . $r['sold'] . "\n";
        echo "Balance (STOCK QTY): " . $r['balance'] . "\n";
    }
}

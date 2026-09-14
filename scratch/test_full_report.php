<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\ReportingController;

$controller = new ReportingController();
$request = new Request([
    'start_date' => '2026-09-01',
    'end_date'   => '2026-09-14',
    'q'          => '2 in 1 Biscuits',
]);

session(['branch_id' => 1]);

$response = $controller->fetchItemStock($request);
if ($response instanceof \Illuminate\Http\JsonResponse) {
    $data = $response->getData(true);
    echo "JSON Data Rows: " . count($data['data'] ?? []) . "\n";
    foreach ($data['data'] as $row) {
        if (str_contains($row['item_name'], '2 in 1')) {
            echo "Item: {$row['item_name']}\n";
            echo "Initial: {$row['initial_stock']}\n";
            echo "Produced: {$row['produced']}\n";
            echo "Purchased: {$row['purchased']}\n";
            echo "Purch Return: {$row['purchase_return']}\n";
            echo "Transfer Out: {$row['transfer']}\n";
            echo "Transfer In: {$row['transfer_in']}\n";
            echo "Adj +: {$row['adj_increase']}\n";
            echo "Adj -: {$row['adj_decrease']}\n";
            echo "Sold: {$row['sold']}\n";
            echo "Sale Return: {$row['sale_return']}\n";
            echo "Balance (Closing): {$row['balance']}\n";
        }
    }
}

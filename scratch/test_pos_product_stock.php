<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\SaleController;

session(['branch_id' => 1]);

$controller = new SaleController();
$request = new Request([
    'q' => '2 in 1 Biscuits',
]);

$response = $controller->getPosProducts($request);
$data = $response->getData(true);

echo "Returned POS products:\n";
foreach ($data['data'] as $p) {
    echo "ID: {$p['id']}, Name: {$p['item_name']}, Stock (grams): {$p['stock']}, Formatted: " . ($p['stock'] / 1000) . " KG\n";
}

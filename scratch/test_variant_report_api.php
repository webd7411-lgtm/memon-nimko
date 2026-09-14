<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\ReportingController;

$controller = new ReportingController();
$request = Request::create('/variant-stock', 'GET', ['product_id' => 83]);

$response = $controller->fetchVariantStock($request);
echo $response->getContent();

<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;

$user = User::where('email', 'admin@admin.com')->first();
Auth::login($user);

try {
    $controller = new HomeController();
    $response = $controller->index();
    $html = $response->render();
    echo "SUCCESS! HomeController index rendered successfully! HTML size: " . strlen($html) . " bytes.\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

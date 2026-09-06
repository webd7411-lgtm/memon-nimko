<?php
require __DIR__ . '/vendor/autoload.php';
$kernel = require_once __DIR__ . '/bootstrap/app.php';
$kernel->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;
$cols = DB::select('SHOW COLUMNS FROM products');
$found = false;
foreach ($cols as $c) {
    if ($c->Field === 'branch_id') {
        echo "branch_id EXISTS in products\n";
        $found = true;
    }
}
if (!$found) echo "branch_id DOES NOT EXIST in products\n";

// Also check Product model
echo "\nChecking Product model for branch references:\n";
$model = file_get_contents(__DIR__ . '/app/Models/Product.php');
if (strpos($model, 'branch_id') !== false) {
    echo "Product model references branch_id\n";
} else {
    echo "Product model does NOT reference branch_id\n";
}

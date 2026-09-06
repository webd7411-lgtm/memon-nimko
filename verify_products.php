<?php
require __DIR__ . '/vendor/autoload.php';
$kernel = require_once __DIR__ . '/bootstrap/app.php';
$kernel->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;
try {
    $cols = DB::select("SHOW COLUMNS FROM products WHERE Field = 'branch_id'");
    echo count($cols) > 0 ? "branch_id EXISTS in products\n" : "branch_id MISSING in products\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

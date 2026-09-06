<?php
require __DIR__ . '/vendor/autoload.php';
$kernel = require_once __DIR__ . '/bootstrap/app.php';
$kernel->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
try {
    $cols = DB::select("SHOW COLUMNS FROM raw_material_stocks");
    echo "Columns in raw_material_stocks:\n";
    foreach ($cols as $c) {
        echo "  " . $c->Field . " (" . $c->Type . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

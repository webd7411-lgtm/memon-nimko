<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Active branch ID: " . active_branch_id() . "\n";
echo "Is all branches: " . (is_all_branches() ? 'yes' : 'no') . "\n";

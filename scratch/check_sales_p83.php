<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$productId = 83;

echo "=== SALES FOR PRODUCT 83 ===\n";
$sales = DB::table('sales')
    ->where(function($q) use ($productId) {
        $q->where('product', $productId)
          ->orWhere('product', 'like', '%"'.$productId.'"%')
          ->orWhere('product', 'like', '%['.$productId.',%')
          ->orWhere('product', 'like', '%,'.$productId.',%')
          ->orWhere('product', 'like', '%,'.$productId.']%');
    })
    ->get();

foreach ($sales as $s) {
    echo "Sale ID: {$s->id}, invoice: {$s->invoice_no}, product: {$s->product}, variant_id: {$s->variant_id}, qty: {$s->qty}, created_at: {$s->created_at}\n";
}

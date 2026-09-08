<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$tr = DB::table('stock_transfers')->where('id', 9)->first();
echo "DB product_id string value: \n";
$s = $tr->product_id;
echo "Length: " . strlen($s) . "\n";
echo "Value chars: ";
for ($i = 0; $i < strlen($s); $i++) {
    echo "[" . $s[$i] . "]";
}
echo "\n";

$arr = json_decode($s, true);
echo "Decoded type: " . gettype($arr) . "\n";
if (is_array($arr)) {
    echo "Array elements: \n";
    foreach ($arr as $i => $val) {
        echo "  [$i] value=[$val] type=" . gettype($val) . " len=" . strlen($val) . "\n";
        echo "  Trimmed: [" . trim($val) . "] len=" . strlen(trim($val)) . "\n";
    }
} else {
    echo "Not array, value: [$arr]\n";
    echo "JSON error: " . json_last_error_msg() . "\n";
}

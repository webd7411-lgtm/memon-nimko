<?php
$s = '["227"]';
echo "Length: " . strlen($s) . "\n";
$arr = json_decode($s, true);
echo "Type: " . gettype($arr) . "\n";
if (is_array($arr)) {
    echo "Is array, count=" . count($arr) . "\n";
    echo "First: [" . $arr[0] . "] type=" . gettype($arr[0]) . "\n";
} else {
    echo "Not array. Value: [" . $arr . "]\n";
    echo "JSON error: " . json_last_error_msg() . " (code: " . json_last_error() . ")\n";
}

$s2 = '[227]'; // No inner quotes
$arr2 = json_decode($s2, true);
echo "\nTest [227]: type=" . gettype($arr2) . ", count=" . (is_array($arr2) ? count($arr2) : 'N/A') . "\n";
if (is_array($arr2)) print_r($arr2);

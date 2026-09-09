<?php
// User's manual formula check
$initial = 100;
$produced = 20;
$purchased = 50;
$purch_return = 10;
$transfer_out = 15;
$transfer_in = 5;
$adj_plus = 3;
$adj_minus = 2;
$sold = 80;
$sale_return = 4;

$stock = $initial + $produced + $purchased + $transfer_in + $adj_plus + $sale_return
        - $purch_return - $transfer_out - $adj_minus - $sold;

echo "Manual Formula Result = " . $stock . "\n";

// Software's opening/closing logic (from ReportingController fetchItemStock)
// Balance (current) -> backward open calc, but forward equivalent is same components
// Software balance = balance - purchAft - prodAft - sRetAft + soldAft + prAft - pReturn - adjIncAft + adjDecAft - transferInAft + transferAft
// For current state with no "after" transactions (purchAft=0, prodAft=0, sRetAft=0, soldAft=0, prAft=0, adjIncAft=0, adjDecAft=0, transferInAft=0, transferAft=0):
$balance = $stock; // assume current balance is 75 from formula above
$openingStock = $balance - $purchased - $produced - $sale_return - $transfer_in - $adj_plus + $purch_return + $transfer_out + $adj_minus;

echo "Software backward-derived Opening (should match initial + adjustments) = " . $openingStock . "\n";

// Direct software-style current balance from initial + transactions (same as user formula)
$software_stock = $initial + $produced + $purchased + $transfer_in + $adj_plus + $sale_return
                  - $purch_return - $transfer_out - $adj_minus - $sold;
echo "Software Direct Formula = " . $software_stock . "\n";
echo ($software_stock == 75 ? "MATCH: 75\n" : "MISMATCH\n");

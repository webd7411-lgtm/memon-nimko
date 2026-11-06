<?php

$filePath = __DIR__ . '/app/Http/Controllers/SaleController.php';
$content = file_get_contents($filePath);

// 1. Add model imports if not present
if (strpos($content, 'use App\Models\ExpenseVoucher;') === false) {
    $importTarget = "use App\Models\ProductBooking;";
    $importReplacement = "use App\Models\ProductBooking;\nuse App\Models\ExpenseVoucher;\nuse App\Models\AccountHead;\nuse App\Models\Account;";
    $content = str_replace($importTarget, $importReplacement, $content);
}

// 2. In store(), call syncTradeOfferExpenseVoucher before DB::commit()
$storeCommitTarget = '            DB::commit();

            // Redirect logic
            if ($model instanceof \App\Models\Sale) {';
$storeCommitReplacement = '            if ($action === \'sale\' && $model instanceof \App\Models\Sale) {
                $this->syncTradeOfferExpenseVoucher($model, $combined_trade_offers, $combined_prices);
            }

            DB::commit();

            // Redirect logic
            if ($model instanceof \App\Models\Sale) {';

if (strpos($content, $storeCommitTarget) !== false) {
    $content = str_replace($storeCommitTarget, $storeCommitReplacement, $content);
    echo "Hooked into store() successfully.\n";
} else {
    // Try with CRLF
    $storeCommitTargetCRLF = str_replace("\n", "\r\n", $storeCommitTarget);
    $storeCommitReplacementCRLF = str_replace("\n", "\r\n", $storeCommitReplacement);
    if (strpos($content, $storeCommitTargetCRLF) !== false) {
        $content = str_replace($storeCommitTargetCRLF, $storeCommitReplacementCRLF, $content);
        echo "Hooked into store() (CRLF) successfully.\n";
    } else {
        echo "WARNING: Could not find storeCommitTarget in SaleController.php!\n";
    }
}

// 3. In updateRunningSale(), call syncTradeOfferExpenseVoucher before DB::commit()
$runningCommitTarget = '            $sale->save();

            DB::commit();

            // Redirects
            $returnTo = route(\'sale.add\');';
$runningCommitReplacement = '            $sale->save();

            if ($action === \'sale\') {
                $this->syncTradeOfferExpenseVoucher($sale, $combined_trade_offers, $combined_prices);
            }

            DB::commit();

            // Redirects
            $returnTo = route(\'sale.add\');';

if (strpos($content, $runningCommitTarget) !== false) {
    $content = str_replace($runningCommitTarget, $runningCommitReplacement, $content);
    echo "Hooked into updateRunningSale() successfully.\n";
} else {
    $runningCommitTargetCRLF = str_replace("\n", "\r\n", $runningCommitTarget);
    $runningCommitReplacementCRLF = str_replace("\n", "\r\n", $runningCommitReplacement);
    if (strpos($content, $runningCommitTargetCRLF) !== false) {
        $content = str_replace($runningCommitTargetCRLF, $runningCommitReplacementCRLF, $content);
        echo "Hooked into updateRunningSale() (CRLF) successfully.\n";
    } else {
        echo "WARNING: Could not find runningCommitTarget in SaleController.php!\n";
    }
}

// 4. In updatesale(), set $sale->trade_offer and call syncTradeOfferExpenseVoucher before DB::commit()
$updatesaleSaveTarget = '            $sale->total_items         = $total_items;
            $sale->save();';
$updatesaleSaveReplacement = '            $sale->total_items         = $total_items;
            $sale->trade_offer         = implode(\',\', $combined_trade_offers);
            $sale->save();';

if (strpos($content, $updatesaleSaveTarget) !== false) {
    $content = str_replace($updatesaleSaveTarget, $updatesaleSaveReplacement, $content);
    echo "Updated sale->trade_offer in updatesale() successfully.\n";
} else {
    $updatesaleSaveTargetCRLF = str_replace("\n", "\r\n", $updatesaleSaveTarget);
    $updatesaleSaveReplacementCRLF = str_replace("\n", "\r\n", $updatesaleSaveReplacement);
    if (strpos($content, $updatesaleSaveTargetCRLF) !== false) {
        $content = str_replace($updatesaleSaveTargetCRLF, $updatesaleSaveReplacementCRLF, $content);
        echo "Updated sale->trade_offer in updatesale() (CRLF) successfully.\n";
    } else {
        echo "WARNING: Could not find updatesaleSaveTarget in SaleController.php!\n";
    }
}

$updatesaleCommitTarget = '            CustomerLedger::create([
                        \'customer_id\'      => $customer_id,
                        \'admin_or_user_id\' => auth()->id(),
                        \'previous_balance\' => 0,
                        \'closing_balance\'  => $new_total,
                        \'opening_balance\'  => $new_total,
                    ]);
                }
            }

            DB::commit();';
$updatesaleCommitReplacement = '            CustomerLedger::create([
                        \'customer_id\'      => $customer_id,
                        \'admin_or_user_id\' => auth()->id(),
                        \'previous_balance\' => 0,
                        \'closing_balance\'  => $new_total,
                        \'opening_balance\'  => $new_total,
                    ]);
                }
            }

            $this->syncTradeOfferExpenseVoucher($sale, $combined_trade_offers, $combined_prices);

            DB::commit();';

if (strpos($content, $updatesaleCommitTarget) !== false) {
    $content = str_replace($updatesaleCommitTarget, $updatesaleCommitReplacement, $content);
    echo "Hooked into updatesale() DB::commit successfully.\n";
} else {
    $updatesaleCommitTargetCRLF = str_replace("\n", "\r\n", $updatesaleCommitTarget);
    $updatesaleCommitReplacementCRLF = str_replace("\n", "\r\n", $updatesaleCommitReplacement);
    if (strpos($content, $updatesaleCommitTargetCRLF) !== false) {
        $content = str_replace($updatesaleCommitTargetCRLF, $updatesaleCommitReplacementCRLF, $content);
        echo "Hooked into updatesale() DB::commit (CRLF) successfully.\n";
    } else {
        echo "WARNING: Could not find updatesaleCommitTarget in SaleController.php!\n";
    }
}

// 5. In saleDetails(), load tradeOfferVoucher and add financial data
$saleDetailsWithTarget = "public function saleDetails(\$id)\n    {\n        \$sale = Sale::with(['user', 'branch', 'customer_relation', 'cardAccount'])->findOrFail(\$id);";
$saleDetailsWithReplacement = "public function saleDetails(\$id)\n    {\n        \$sale = Sale::with(['user', 'branch', 'customer_relation', 'cardAccount', 'tradeOfferVoucher'])->findOrFail(\$id);";

if (strpos($content, $saleDetailsWithTarget) !== false) {
    $content = str_replace($saleDetailsWithTarget, $saleDetailsWithReplacement, $content);
    echo "Updated saleDetails with() successfully.\n";
} else {
    $saleDetailsWithTargetCRLF = str_replace("\n", "\r\n", $saleDetailsWithTarget);
    $saleDetailsWithReplacementCRLF = str_replace("\n", "\r\n", $saleDetailsWithReplacement);
    if (strpos($content, $saleDetailsWithTargetCRLF) !== false) {
        $content = str_replace($saleDetailsWithTargetCRLF, $saleDetailsWithReplacementCRLF, $content);
        echo "Updated saleDetails with() (CRLF) successfully.\n";
    } else {
        echo "WARNING: Could not find saleDetailsWithTarget in SaleController.php!\n";
    }
}

$saleDetailsFinancialTarget = "                    'change'           => (float)(\$sale->change ?: 0),\n                ],";
$saleDetailsFinancialReplacement = "                    'change'           => (float)(\$sale->change ?: 0),\n                    'trade_offer_amount' => (float)(\$sale->trade_offer_amount ?: 0),\n                    'trade_offer_voucher_evid' => \$sale->tradeOfferVoucher ? \$sale->tradeOfferVoucher->evid : null,\n                    'trade_offer_voucher_id'   => \$sale->trade_offer_voucher_id,\n                ],";

if (strpos($content, $saleDetailsFinancialTarget) !== false) {
    $content = str_replace($saleDetailsFinancialTarget, $saleDetailsFinancialReplacement, $content);
    echo "Updated saleDetails financial array successfully.\n";
} else {
    $saleDetailsFinancialTargetCRLF = str_replace("\n", "\r\n", $saleDetailsFinancialTarget);
    $saleDetailsFinancialReplacementCRLF = str_replace("\n", "\r\n", $saleDetailsFinancialReplacement);
    if (strpos($content, $saleDetailsFinancialTargetCRLF) !== false) {
        $content = str_replace($saleDetailsFinancialTargetCRLF, $saleDetailsFinancialReplacementCRLF, $content);
        echo "Updated saleDetails financial array (CRLF) successfully.\n";
    } else {
        echo "WARNING: Could not find saleDetailsFinancialTarget in SaleController.php!\n";
    }
}

// 6. Add syncTradeOfferExpenseVoucher method before the closing brace of the class
if (strpos($content, 'function syncTradeOfferExpenseVoucher') === false) {
    $lastBracePos = strrpos($content, '}');
    if ($lastBracePos !== false) {
        $methodCode = <<<'CODE'

    /**
     * Automatically create, update, or reverse an Expense Voucher for Trade Offer (free goods)
     * under AccountHead "Trade Promotion & Market Discounts" and Account "Trade Promotion & Retail Schemes Expense".
     *
     * @param \App\Models\Sale $sale
     * @param array $tradeOffers
     * @param array $prices
     * @return void
     */
    public function syncTradeOfferExpenseVoucher(\App\Models\Sale $sale, array $tradeOffers = [], array $prices = [])
    {
        try {
            if (empty($tradeOffers)) {
                $tradeOffers = !empty($sale->trade_offer) ? explode(',', $sale->trade_offer) : [];
            }
            if (empty($prices)) {
                $prices = !empty($sale->per_price) ? explode(',', $sale->per_price) : [];
            }

            $totalTradeOfferAmount = 0.0;
            foreach ($tradeOffers as $idx => $toQty) {
                $to = floatval($toQty);
                $pr = isset($prices[$idx]) ? floatval($prices[$idx]) : 0.0;
                if ($to > 0 && $pr > 0) {
                    $totalTradeOfferAmount += ($to * $pr);
                }
            }
            $totalTradeOfferAmount = round($totalTradeOfferAmount, 2);

            // 1. Locate or create Head: Trade Promotion & Market Discounts
            $head = \App\Models\AccountHead::firstOrCreate(
                ['name' => 'Trade Promotion & Market Discounts'],
                ['status' => 1]
            );

            // 2. Locate or create Account: Trade Promotion & Retail Schemes Expense
            $expenseAccount = \App\Models\Account::where('account_code', 'ACC-CLAIM-TRADE-01')->first();
            if (!$expenseAccount) {
                $expenseAccount = \App\Models\Account::create([
                    'head_id'         => $head->id,
                    'account_code'    => 'ACC-CLAIM-TRADE-01',
                    'title'           => 'Trade Promotion & Retail Schemes Expense',
                    'opening_balance' => 0.00,
                    'status'          => 1,
                ]);
            } else {
                if ($expenseAccount->head_id != $head->id) {
                    $expenseAccount->head_id = $head->id;
                    $expenseAccount->save();
                }
            }

            // Customer Name
            $customerName = 'Walk-in Customer';
            $rawCust = (string)($sale->customer ?? '');
            if (str_starts_with($rawCust, 'dist_')) {
                $distId = (int)str_replace('dist_', '', $rawCust);
                $dist = \App\Models\Distributor::find($distId);
                if ($dist) $customerName = $dist->name . ' (Distributor)';
            } elseif (str_starts_with($rawCust, 'booker_')) {
                $bookerId = (int)str_replace('booker_', '', $rawCust);
                $booker = \App\Models\Distributor::find($bookerId);
                if ($booker) $customerName = $booker->name . ' (Order Booker)';
            } elseif (str_starts_with($rawCust, 'dc_')) {
                $dcId = (int)str_replace('dc_', '', $rawCust);
                $dc = \App\Models\DistributorCustomer::find($dcId);
                if ($dc) $customerName = ($dc->shop_name ?: $dc->owner_name) . ' (Retail Shop)';
            } elseif (is_numeric($rawCust)) {
                $c = \App\Models\Customer::find($rawCust);
                if ($c) $customerName = $c->customer_name;
            } elseif (!empty($rawCust) && $rawCust !== 'Walk-in Customer') {
                $customerName = $rawCust;
            }

            $voucherId = $sale->trade_offer_voucher_id;
            $voucher = $voucherId ? \App\Models\ExpenseVoucher::find($voucherId) : null;

            if ($totalTradeOfferAmount > 0) {
                $remarkText = "Auto Expense: Trade Offer Free Goods on Invoice #{$sale->invoice_no} ({$customerName})";
                $saleDate = $sale->created_at ? $sale->created_at->format('Y-m-d') : now()->format('Y-m-d');

                if ($voucher) {
                    // Adjust difference in account balance
                    $oldAmount = (float)$voucher->total_amount;
                    $diff = $totalTradeOfferAmount - $oldAmount;
                    if ($diff != 0) {
                        $expenseAccount->opening_balance = (float)$expenseAccount->opening_balance + $diff;
                        $expenseAccount->save();
                    }

                    $voucher->update([
                        'date'         => $saleDate,
                        'type'         => (string)$head->id,
                        'party_id'     => (string)$expenseAccount->id,
                        'remarks'      => [$remarkText],
                        'amount'       => [(string)$totalTradeOfferAmount],
                        'total_amount' => $totalTradeOfferAmount,
                    ]);
                } else {
                    $evid = method_exists(\App\Models\ExpenseVoucher::class, 'generateInvoiceNo')
                        ? \App\Models\ExpenseVoucher::generateInvoiceNo()
                        : 'EV-' . date('Ymd') . '-' . rand(1000, 9999);

                    $voucher = \App\Models\ExpenseVoucher::create([
                        'evid'         => $evid,
                        'user_id'      => auth()->id() ?: 1,
                        'branch_id'    => $sale->branch_id ?: (function_exists('active_branch_id') ? active_branch_id() : null),
                        'entry_date'   => now(),
                        'date'         => $saleDate,
                        'type'         => (string)$head->id,
                        'party_id'     => (string)$expenseAccount->id,
                        'remarks'      => [$remarkText],
                        'amount'       => [(string)$totalTradeOfferAmount],
                        'total_amount' => $totalTradeOfferAmount,
                    ]);

                    $expenseAccount->opening_balance = (float)$expenseAccount->opening_balance + $totalTradeOfferAmount;
                    $expenseAccount->save();

                    $sale->trade_offer_voucher_id = $voucher->id;
                }

                $sale->trade_offer_amount = $totalTradeOfferAmount;
                $sale->saveQuietly();
            } else {
                // If previously there was a voucher but now trade offer is 0
                if ($voucher) {
                    $oldAmount = (float)$voucher->total_amount;
                    $expenseAccount->opening_balance = max(0, (float)$expenseAccount->opening_balance - $oldAmount);
                    $expenseAccount->save();
                    $voucher->delete();
                }
                $sale->trade_offer_voucher_id = null;
                $sale->trade_offer_amount = 0.00;
                $sale->saveQuietly();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Trade offer expense voucher sync failed: ' . $e->getMessage());
        }
    }
}
CODE;
        $content = substr($content, 0, $lastBracePos) . $methodCode . "\n";
        echo "Added syncTradeOfferExpenseVoucher method.\n";
    }
}

file_put_contents($filePath, $content);
echo "SaleController.php updated successfully!\n";

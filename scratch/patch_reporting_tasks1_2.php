<?php
$filePath = 'app/Http/Controllers/ReportingController.php';
$content = file_get_contents($filePath);

// ==========================================
// 1. CASHBOOK PATCH
// ==========================================
$cashStartMarker = "    public function cashbook(Request \$request)\r\n    {";
if (strpos($content, $cashStartMarker) === false) {
    $cashStartMarker = "    public function cashbook(Request \$request)\n    {";
}

$cashEndMarker = "        \$maxRows = max(count(\$receipts), count(\$payments));";

$cashStartPos = strpos($content, $cashStartMarker);
$cashEndPos = strpos($content, $cashEndMarker);

if ($cashStartPos === false || $cashEndPos === false) {
    die("Cashbook markers not found\n");
}

$newCashbookCode = <<<PHP
    public function cashbook(Request \$request)
    {
        \$selectedDate = \$request->get('date', Carbon::today()->toDateString());
        \$today = \$selectedDate;
        \$startDate = \$request->get('start_date');
        \$startTime = \$request->start_time ?? '00:00';
        \$endTime   = \$request->end_time   ?? '23:59';

        \$startDT = \$selectedDate . ' ' . \$startTime . ':00';
        \$endDT   = \$selectedDate . ' ' . \$endTime   . ':59';

        \$branches = \App\Models\Branch::orderBy('name')->get();
        \$branchId = \$request->get('branch_id');
        if (!\$branchId) {
            \$branchId = is_all_branches() ? 'all' : (string)active_branch_id();
        }
        \$selectedBranchId = \$branchId;

        // 🛡️ User Permission Check: Can this user view all cashbook entries or only their own?
        \$currentUser = auth()->user();
        \$canViewAllCashbook = \$currentUser && (
            \$currentUser->can('View All Cashbook') ||
            \$currentUser->hasRole('super-admin') ||
            \$currentUser->hasRole('Super Admin') ||
            \$currentUser->hasRole('Admin') ||
            \$currentUser->email === 'admin@admin.com'
        );

        \$users = \App\Models\User::orderBy('name')->get();
        if (\$canViewAllCashbook) {
            \$selectedUserId = \$request->get('user_id', 'all');
            if (\$selectedUserId !== 'all' && !empty(\$selectedUserId)) {
                \$selectedUserId = (string)\$selectedUserId;
            } else {
                \$selectedUserId = 'all';
            }
        } else {
            // Strictly enforce own user id
            \$selectedUserId = (string)(\$currentUser->id ?? 0);
        }

        /* ================= OPENING BALANCE (CUMULATIVE HISTORICAL) ================= */
        // Calculate true historical opening balance up to startDT (no 30-day cutoff unless explicitly requested)
        \$hasCustomStartDate = \$request->filled('start_date');
        \$customStartDT = \$hasCustomStartDate ? (\$request->get('start_date') . ' 00:00:00') : null;
        \$customStartDate = \$hasCustomStartDate ? \$request->get('start_date') : null;

        \$prevSalesQ = Sale::where('created_at', '<', \$startDT);
        if (\$customStartDT) \$prevSalesQ->where('created_at', '>=', \$customStartDT);

        \$prevCustQ = CustomerPayment::where('payment_date', '<', \$selectedDate)
            ->where(function(\$q) {
                \$q->whereNull('note')->orWhere('note', 'not like', '%Paid on Sale%');
            });
        if (\$customStartDate) \$prevCustQ->where('payment_date', '>=', \$customStartDate);

        \$prevReturnsQ = \App\Models\SalesReturn::where('created_at', '<', \$startDT);
        if (\$customStartDT) \$prevReturnsQ->where('created_at', '>=', \$customStartDT);

        \$prevVendQ = VendorPayment::where('payment_date', '<', \$selectedDate)
            ->where(function(\$q) {
                \$q->whereNull('note')->orWhere('note', 'not like', '%Paid on Purchase%');
            });
        if (\$customStartDate) \$prevVendQ->where('payment_date', '>=', \$customStartDate);

        \$prevExpQ = ExpenseVoucher::where('date', '<', \$selectedDate)
            ->where(function(\$q) {
                \$q->whereNull('is_stock_adjustment')->orWhere('is_stock_adjustment', false);
            });
        if (\$customStartDate) \$prevExpQ->where('date', '>=', \$customStartDate);

        if (\$selectedBranchId !== 'all') {
            \$prevSalesQ->where('branch_id', \$selectedBranchId);
            \$prevCustQ->where('branch_id', \$selectedBranchId);
            \$prevReturnsQ->where('branch_id', \$selectedBranchId);
            \$prevVendQ->where('branch_id', \$selectedBranchId);
            \$prevExpQ->where('branch_id', \$selectedBranchId);
        }

        if (\$selectedUserId !== 'all') {
            \$prevSalesQ->where('user_id', \$selectedUserId);
            \$prevCustQ->where('admin_or_user_id', \$selectedUserId);
            \$prevVendQ->where('admin_or_user_id', \$selectedUserId);
            \$prevExpQ->where('user_id', \$selectedUserId);
        }

        // For sales opening: actual cash/card received on sales
        \$previousSales = \$prevSalesQ->get()->sum(function(\$s) {
            \$paid = floatval(\$s->cash) + floatval(\$s->card);
            \$net = floatval(\$s->total_net);
            if (\$s->customer === 'Walk-in Customer' || \$paid >= \$net) {
                return \$net;
            }
            return min(\$net, \$paid);
        });
        \$previousCustomerRecoveries = (float)\$prevCustQ->sum('amount');
        \$previousReturnsCash = (float)\$prevReturnsQ->sum('cash');
        \$previousVendorPayments = (float)\$prevVendQ->sum('amount');
        \$previousExpenses = (float)\$prevExpQ->sum('total_amount');

        // Cash Accounts opening balance from Chart of Accounts
        \$accountCashOpening = 0;
        if (\$selectedUserId === 'all') {
            \$cashAccountsQ = DB::table('accounts')
                ->leftJoin('account_heads', 'account_heads.id', '=', 'accounts.head_id')
                ->where('accounts.status', 1)
                ->whereNull('accounts.distributor_id')
                ->where(function(\$q) {
                    \$q->where('account_heads.name', 'like', '%Cash%')
                      ->orWhere('accounts.title', 'like', '%Cash%')
                      ->orWhere('accounts.head_id', 2);
                });
            \$accountCashOpening = (float)\$cashAccountsQ->sum('accounts.opening_balance');
        }

        \$openingBalance = \$accountCashOpening + (\$previousSales + \$previousCustomerRecoveries) - (\$previousVendorPayments + \$previousExpenses + \$previousReturnsCash);

        /* ================= TODAY'S DATA (with time filter) ================= */
        \$salesQ = Sale::with('user')
            ->where('created_at', '>=', \$startDT)
            ->where('created_at', '<=', \$endDT);
        \$custQ = CustomerPayment::with(['customer', 'user'])
            ->whereDate('payment_date', \$selectedDate)
            ->where(function(\$q) {
                \$q->whereNull('note')->orWhere('note', 'not like', '%Paid on Sale%');
            });
        \$returnsQ = \App\Models\SalesReturn::with('user')
            ->whereBetween('created_at', [\$startDT, \$endDT]);
        \$vendQ = VendorPayment::with(['vendor', 'user'])
            ->whereDate('payment_date', \$selectedDate)
            ->where(function(\$q) {
                \$q->whereNull('note')->orWhere('note', 'not like', '%Paid on Purchase%');
            });
        \$expQ = ExpenseVoucher::with('user')
            ->whereDate('date', \$selectedDate)
            ->where(function(\$q) {
                \$q->whereNull('is_stock_adjustment')->orWhere('is_stock_adjustment', false);
            });

        if (\$selectedBranchId !== 'all') {
            \$salesQ->where('branch_id', \$selectedBranchId);
            \$custQ->where('branch_id', \$selectedBranchId);
            \$returnsQ->where('branch_id', \$selectedBranchId);
            \$vendQ->where('branch_id', \$selectedBranchId);
            \$expQ->where('branch_id', \$selectedBranchId);
        }

        if (\$selectedUserId !== 'all') {
            \$salesQ->where('user_id', \$selectedUserId);
            \$custQ->where('admin_or_user_id', \$selectedUserId);
            \$vendQ->where('admin_or_user_id', \$selectedUserId);
            \$expQ->where('user_id', \$selectedUserId);
        }

        \$allSales = \$salesQ->get();
        \$customerRecoveries = \$custQ->get();
        \$todayReturns = \$returnsQ->get();
        \$vendorPayments = \$vendQ->get();
        \$expenseVouchers = \$expQ->get();

        /* ================= RECEIPTS BREAKDOWN ================= */
        \$totalSaleCash = \$allSales->sum('cash');
        \$totalSaleCard = \$allSales->sum('card');
        \$totalChange   = \$allSales->sum('change');
        \$totalSaleNet  = \$allSales->sum('total_net');
        \$saleCount     = \$allSales->count();

        \$recoveryByMethod = [];
        foreach (\$customerRecoveries as \$cr) {
            \$m = \$cr->payment_method ?? 'Other';
            if (!isset(\$recoveryByMethod[\$m])) \$recoveryByMethod[\$m] = 0;
            \$recoveryByMethod[\$m] += \$cr->amount;
        }
        \$totalRecoveries = \$customerRecoveries->sum('amount');

        // Actual cash/card received from sales today
        \$todaySaleReceived = \$allSales->sum(function(\$s) {
            \$paid = floatval(\$s->cash) + floatval(\$s->card);
            \$net = floatval(\$s->total_net);
            if (\$s->customer === 'Walk-in Customer' || \$paid >= \$net) {
                return \$net;
            }
            return min(\$net, \$paid);
        });

        \$totalReceipts = \$todaySaleReceived + \$totalRecoveries;

        /* ================= PAYMENTS BREAKDOWN ================= */
        \$vendorPayByMethod = [];
        foreach (\$vendorPayments as \$vp) {
            \$m = \$vp->payment_method ?? 'Other';
            if (!isset(\$vendorPayByMethod[\$m])) \$vendorPayByMethod[\$m] = 0;
            \$vendorPayByMethod[\$m] += \$vp->amount;
        }
        \$totalVendorPayments = \$vendorPayments->sum('amount');
        \$totalExpenses = \$expenseVouchers->sum('total_amount');
        \$todayReturnCash = (float)\$todayReturns->sum('cash');
        \$totalPayments = \$totalVendorPayments + \$totalExpenses + \$todayReturnCash;
        \$closingBalance = \$openingBalance + \$totalReceipts - \$totalPayments;

        /* ================= DETAILED ENTRIES ================= */
        \$receipts = [];
        foreach (\$allSales as \$sale) {
            \$paid = floatval(\$sale->cash) + floatval(\$sale->card);
            \$net = floatval(\$sale->total_net);
            \$received = (\$sale->customer === 'Walk-in Customer' || \$paid >= \$net) ? \$net : min(\$net, \$paid);
            if (\$received > 0) {
                \$ref = '#' . \$sale->invoice_no;
                if (\$selectedUserId === 'all' && \$sale->user) {
                    \$ref .= ' (' . \$sale->user->name . ')';
                }
                \$receipts[] = ['title' => 'Sale', 'ref' => \$ref, 'amount' => \$received];
            }
        }
        foreach (\$customerRecoveries as \$cr) {
            \$ref = (\$cr->customer->customer_name ?? '-') . ' (' . (\$cr->payment_method ?? 'N/A') . ')';
            if (\$selectedUserId === 'all' && \$cr->user) {
                \$ref .= ' - by ' . \$cr->user->name;
            }
            \$receipts[] = ['title' => 'Recovery', 'ref' => \$ref, 'amount' => \$cr->amount];
        }

        \$payments = [];
        foreach (\$vendorPayments as \$vp) {
            \$ref = (\$vp->vendor->name ?? '-') . ' (' . (\$vp->payment_method ?? 'N/A') . ')';
            if (\$selectedUserId === 'all' && \$vp->user) {
                \$ref .= ' - by ' . \$vp->user->name;
            }
            \$payments[] = ['title' => 'Vendor Pay', 'ref' => \$ref, 'amount' => \$vp->amount];
        }
        foreach (\$expenseVouchers as \$exp) {
            \$remarks = is_array(\$exp->remarks) ? implode(', ', \$exp->remarks) : (\$exp->remarks ?? '');
            \$ref = \$remarks ?: 'Voucher #' . \$exp->evid;
            if (\$selectedUserId === 'all' && \$exp->user) {
                \$ref .= ' (' . \$exp->user->name . ')';
            }
            \$payments[] = ['title' => 'Expense', 'ref' => \$ref, 'amount' => \$exp->total_amount];
        }
        foreach (\$todayReturns as \$ret) {
            \$rCash = floatval(\$ret->cash);
            if (\$rCash > 0) {
                \$ref = 'Return #' . \$ret->id . (\$ret->invoice_no ? " (Inv #{\$ret->invoice_no})" : '');
                \$payments[] = ['title' => 'Sale Return Refund', 'ref' => \$ref, 'amount' => \$rCash];
            }
        }

PHP;

$content = substr($content, 0, $cashStartPos) . $newCashbookCode . substr($content, $cashEndPos);

// ==========================================
// 2. PROFIT & LOSS BOM RECIPE COSTING PATCH
// ==========================================
$targetPLProductEager = "\$products = \\App\\Models\\Product::with(['category_relation', 'variants'])->get()->keyBy('id');";
$replacePLProductEager = "\$products = \\App\\Models\\Product::with(['category_relation', 'variants', 'bom.rawMaterial', 'bom.ingredientProduct', 'unit'])->get()->keyBy('id');

        // BOM Recipe Manufactured Cost mapping for Profit & Loss
        \$bomProductCostMap = [];
        foreach (\$products as \$pObj) {
            \$isObjKg = (\$pObj->unit_type === 'kg') || str_contains(strtolower(\$pObj->unit->name ?? ''), 'kg');
            \$pBoms = \$pObj->bom ?? collect();

            if (\$pBoms->isNotEmpty()) {
                \$baseBom = \$pBoms->whereNull('variant_id');
                \$baseCost = 0;
                foreach (\$baseBom as \$bItem) {
                    if (\$bItem->rawMaterial) {
                        \$rmCost = (float)\$bItem->rawMaterial->lastPurchaseCost();
                        \$factor = (float)(\$bItem->rawMaterial->conversion_factor ?? 1);
                        if (\$factor <= 0) \$factor = 1;
                        \$baseCost += ((float)\$bItem->qty * (\$rmCost / \$factor));
                    } elseif (\$bItem->ingredientProduct) {
                        \$ingCost = (float)(\$bItem->ingredientProduct->wholesale_price ?: \$bItem->ingredientProduct->price ?: 0);
                        \$baseCost += ((float)\$bItem->qty * \$ingCost);
                    }
                }

                if (\$baseCost > 0) {
                    \$bomProductCostMap[\$pObj->id . '_0'] = \$baseCost;
                }

                foreach (\$pObj->variants as \$vObj) {
                    \$vBom = \$pBoms->where('variant_id', \$vObj->id);
                    if (\$vBom->isNotEmpty()) {
                        \$vCost = 0;
                        foreach (\$vBom as \$bItem) {
                            if (\$bItem->rawMaterial) {
                                \$rmCost = (float)\$bItem->rawMaterial->lastPurchaseCost();
                                \$factor = (float)(\$bItem->rawMaterial->conversion_factor ?? 1);
                                if (\$factor <= 0) \$factor = 1;
                                \$vCost += ((float)\$bItem->qty * (\$rmCost / \$factor));
                            } elseif (\$bItem->ingredientProduct) {
                                \$ingCost = (float)(\$bItem->ingredientProduct->wholesale_price ?: \$bItem->ingredientProduct->price ?: 0);
                                \$vCost += ((float)\$bItem->qty * \$ingCost);
                            }
                        }
                        if (\$vCost > 0) {
                            \$bomProductCostMap[\$pObj->id . '_' . \$vObj->id] = \$vCost;
                        }
                    } elseif (\$baseCost > 0) {
                        if (\$isObjKg) {
                            \$sizeVal = (float)\$vObj->size_value;
                            if (in_array(strtolower(trim(\$vObj->size_unit ?? '')), ['g', 'gm', 'gram', 'grams']) && \$sizeVal > 0) {
                                \$sizeVal /= 1000;
                            }
                            \$bomProductCostMap[\$pObj->id . '_' . \$vObj->id] = \$baseCost * (\$sizeVal > 0 ? \$sizeVal : 1);
                        } else {
                            \$bomProductCostMap[\$pObj->id . '_' . \$vObj->id] = \$baseCost;
                        }
                    }
                }
            }
        }";

$content = str_replace($targetPLProductEager, $replacePLProductEager, $content);

$targetCostCalc = "                // Cost Determination\r\n" .
"                \$unitCost = 0;\r\n" .
"                if (isset(\$purchasePriceMap[\$pid . '_' . \$vid]) && \$purchasePriceMap[\$pid . '_' . \$vid] > 0) {\r\n" .
"                    \$unitCost = \$purchasePriceMap[\$pid . '_' . \$vid];\r\n" .
"                } elseif (isset(\$purchasePriceMap[\$pid . '_0']) && \$purchasePriceMap[\$pid . '_0'] > 0) {\r\n" .
"                    \$unitCost = \$purchasePriceMap[\$pid . '_0'];\r\n" .
"                } elseif (\$vObj && \$vObj->cost_price > 0) {\r\n" .
"                    \$unitCost = (float)\$vObj->cost_price;\r\n" .
"                } elseif (\$p && (\$firstV = \$p->variants->first()) && \$firstV->cost_price > 0) {\r\n" .
"                    \$unitCost = (float)\$firstV->cost_price;\r\n" .
"                }";

if (strpos($content, $targetCostCalc) === false) {
    $targetCostCalc = str_replace("\r\n", "\n", $targetCostCalc);
}

$replaceCostCalc = "                // Cost Determination (BOM Recipe Manufactured Cost -> Purchase Price -> Variant Cost -> Wholesale)\r\n" .
"                \$unitCost = 0;\r\n" .
"                \$vKey = \$pid . '_' . \$vid;\r\n" .
"                \$pKey = \$pid . '_0';\r\n\r\n" .
"                if (isset(\$bomProductCostMap[\$vKey]) && \$bomProductCostMap[\$vKey] > 0) {\r\n" .
"                    \$unitCost = (float)\$bomProductCostMap[\$vKey];\r\n" .
"                } elseif (isset(\$bomProductCostMap[\$pKey]) && \$bomProductCostMap[\$pKey] > 0) {\r\n" .
"                    if (\$p && (\$p->unit_type === 'kg') && \$vObj) {\r\n" .
"                        \$sVal = (float)\$vObj->size_value;\r\n" .
"                        if (in_array(strtolower(trim(\$vObj->size_unit ?? '')), ['g', 'gm', 'gram', 'grams']) && \$sVal > 0) {\r\n" .
"                            \$sVal /= 1000;\r\n" .
"                        }\r\n" .
"                        \$unitCost = (float)\$bomProductCostMap[\$pKey] * (\$sVal > 0 ? \$sVal : 1);\r\n" .
"                    } else {\r\n" .
"                        \$unitCost = (float)\$bomProductCostMap[\$pKey];\r\n" .
"                    }\r\n" .
"                } elseif (isset(\$purchasePriceMap[\$vKey]) && \$purchasePriceMap[\$vKey] > 0) {\r\n" .
"                    \$unitCost = \$purchasePriceMap[\$vKey];\r\n" .
"                } elseif (isset(\$purchasePriceMap[\$pKey]) && \$purchasePriceMap[\$pKey] > 0) {\r\n" .
"                    \$unitCost = \$purchasePriceMap[\$pKey];\r\n" .
"                } elseif (\$vObj && \$vObj->cost_price > 0) {\r\n" .
"                    \$unitCost = (float)\$vObj->cost_price;\r\n" .
"                } elseif (\$p && (\$firstV = \$p->variants->first()) && \$firstV->cost_price > 0) {\r\n" .
"                    \$unitCost = (float)\$firstV->cost_price;\r\n" .
"                } elseif (\$p && (float)\$p->wholesale_price > 0) {\r\n" .
"                    \$unitCost = (float)\$p->wholesale_price;\r\n" .
"                }";

$content = str_replace($targetCostCalc, $replaceCostCalc, $content);

file_put_contents($filePath, $content);
echo "Successfully patched CashBook and P&L BOM costing in ReportingController.php\n";

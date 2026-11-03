<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Distributor;
use App\Models\DistributorCustomer;
use App\Models\DistributorStock;
use App\Models\ExpenseVoucher;
use App\Models\PaymentVoucher;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\ReceiptsVoucher;
use App\Models\Sale;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialCommandCenterController extends Controller
{
    /**
     * Display the main Executive Financial Command Center page.
     */
    public function index(Request $request)
    {
        $branches = Branch::orderBy('name')->get();
        return view('admin_panel.reporting.financial_command_center', compact('branches'));
    }

    /**
     * Compute and return financial snapshot comparison via AJAX.
     * Compares "Current Period" (e.g. Today) vs "Baseline Period" (e.g. Yesterday).
     */
    public function fetchSnapshot(Request $request)
    {
        $comparisonType = $request->input('comparison_type', 'today_vs_yesterday');
        $branchId       = $request->input('branch_id', 'all');

        $now = Carbon::now();

        switch ($comparisonType) {
            case 'this_month_vs_last_month':
                $currStart = $now->copy()->startOfMonth()->toDateString();
                $currEnd   = $now->copy()->toDateString();
                $baseStart = $now->copy()->subMonth()->startOfMonth()->toDateString();
                $baseEnd   = $now->copy()->subMonth()->endOfMonth()->toDateString();
                $currLabel = 'This Month (' . $now->format('M Y') . ')';
                $baseLabel = 'Last Month (' . $now->copy()->subMonth()->format('M Y') . ')';
                break;

            case 'this_week_vs_last_week':
                $currStart = $now->copy()->startOfWeek()->toDateString();
                $currEnd   = $now->copy()->toDateString();
                $baseStart = $now->copy()->subWeek()->startOfWeek()->toDateString();
                $baseEnd   = $now->copy()->subWeek()->endOfWeek()->toDateString();
                $currLabel = 'This Week';
                $baseLabel = 'Last Week';
                break;

            case 'this_year_vs_last_year':
                $currStart = $now->copy()->startOfYear()->toDateString();
                $currEnd   = $now->copy()->toDateString();
                $baseStart = $now->copy()->subYear()->startOfYear()->toDateString();
                $baseEnd   = $now->copy()->subYear()->endOfYear()->toDateString();
                $currLabel = 'This Year (' . $now->format('Y') . ')';
                $baseLabel = 'Last Year (' . $now->copy()->subYear()->format('Y') . ')';
                break;

            case 'custom':
                $currStart = $request->input('curr_start_date', $now->toDateString());
                $currEnd   = $request->input('curr_end_date', $now->toDateString());
                $baseStart = $request->input('base_start_date', $now->copy()->subDay()->toDateString());
                $baseEnd   = $request->input('base_end_date', $now->copy()->subDay()->toDateString());
                $currLabel = 'Selected Period (' . $currStart . ' to ' . $currEnd . ')';
                $baseLabel = 'Baseline Period (' . $baseStart . ' to ' . $baseEnd . ')';
                break;

            case 'today_vs_yesterday':
            default:
                $currStart = $now->toDateString();
                $currEnd   = $now->toDateString();
                $baseStart = $now->copy()->subDay()->toDateString();
                $baseEnd   = $now->copy()->subDay()->toDateString();
                $currLabel = 'Today (' . $now->format('d M Y') . ')';
                $baseLabel = 'Yesterday (' . $now->copy()->subDay()->format('d M Y') . ')';
                break;
        }

        // 1. Compute Period Metrics (P&L: Sales, Expenses, Net Profit for that period)
        $currPL = $this->calculatePeriodPL($currStart, $currEnd, $branchId);
        $basePL = $this->calculatePeriodPL($baseStart, $baseEnd, $branchId);

        // 2. Compute Capital / Equity Movements (Outsource Injections vs Owner Drawings)
        $currEquityMovements = $this->calculateEquityMovements($currStart, $currEnd, $branchId);
        $baseEquityMovements = $this->calculateEquityMovements($baseStart, $baseEnd, $branchId);
        $cumulativeEquity   = $this->calculateCumulativeEquity($currEnd, $branchId);

        // 3. Compute Cumulative Balance Sheet & Net Worth AS OF Current vs AS OF Baseline
        $currBS = $this->calculateCumulativeBalanceSheet($currEnd, $branchId, $cumulativeEquity);
        $baseBS = $this->calculateCumulativeBalanceSheet($baseEnd, $branchId, $this->calculateCumulativeEquity($baseEnd, $branchId));

        // 4. Compute Delta Comparison Indicators
        $comparison = [
            'labels' => [
                'current'  => $currLabel,
                'baseline' => $baseLabel,
            ],
            'net_worth' => $this->buildDeltaItem($currBS['net_worth'], $baseBS['net_worth'], 'currency'),
            'total_assets' => $this->buildDeltaItem($currBS['total_assets'], $baseBS['total_assets'], 'currency'),
            'total_liabilities' => $this->buildDeltaItem($currBS['total_liabilities'], $baseBS['total_liabilities'], 'currency', true),
            'sales' => $this->buildDeltaItem($currPL['total_sales'], $basePL['total_sales'], 'currency'),
            'expenses' => $this->buildDeltaItem($currPL['total_expenses'], $basePL['total_expenses'], 'currency', true),
            'net_profit' => $this->buildDeltaItem($currPL['net_profit'], $basePL['net_profit'], 'currency'),
            'capital_injected' => $this->buildDeltaItem($currEquityMovements['injections'], $baseEquityMovements['injections'], 'currency'),
            'drawings_withdrawn' => $this->buildDeltaItem($currEquityMovements['drawings'], $baseEquityMovements['drawings'], 'currency', true),
            'cash_and_bank' => $this->buildDeltaItem($currBS['cash_and_bank'], $baseBS['cash_and_bank'], 'currency'),
            'inventory_value' => $this->buildDeltaItem($currBS['inventory']['total'], $baseBS['inventory']['total'], 'currency'),
            'receivables' => $this->buildDeltaItem($currBS['receivables']['total'], $baseBS['receivables']['total'], 'currency'),
            'payables' => $this->buildDeltaItem($currBS['payables']['total'], $baseBS['payables']['total'], 'currency', true),
        ];

        // 5. Quick Liquidity & Health Indicators
        $currentRatio = $currBS['total_liabilities'] > 0
            ? round($currBS['current_assets'] / $currBS['total_liabilities'], 2)
            : ($currBS['current_assets'] > 0 ? 99.9 : 1.0);

        $debtToEquity = $currBS['net_worth'] > 0
            ? round(($currBS['total_liabilities'] / $currBS['net_worth']) * 100, 1)
            : 0;

        return response()->json([
            'status'             => true,
            'curr_label'         => $currLabel,
            'base_label'         => $baseLabel,
            'curr_pl'            => $currPL,
            'base_pl'            => $basePL,
            'curr_bs'            => $currBS,
            'base_bs'            => $baseBS,
            'curr_equity'        => $currEquityMovements,
            'base_equity'        => $baseEquityMovements,
            'cumulative_equity'  => $cumulativeEquity,
            'comparison'         => $comparison,
            'health'             => [
                'current_ratio'    => $currentRatio,
                'debt_to_equity'   => $debtToEquity,
                'is_profitable'    => $currPL['net_profit'] >= 0,
                'solvency_status'  => $currBS['net_worth'] >= 0 ? 'Solvent & Healthy' : 'Action Required',
            ],
        ]);
    }

    /**
     * Calculate Period-Specific Capital Injections (Outsource / Investment) & Drawings (Personal withdrawals).
     */
    private function calculateEquityMovements(string $startDate, string $endDate, $branchId): array
    {
        $isAll = ($branchId === 'all');

        // Capital Injections (Receipt Vouchers linked to Equity / Capital accounts or narrations)
        $rvQ = DB::table('receipts_vouchers')
            ->whereBetween('entry_date', [$startDate, $endDate]);
        if (!$isAll) $rvQ->where('branch_id', $branchId);

        // Find equity/capital heads
        $equityHeadIds = DB::table('account_heads')
            ->where('name', 'like', '%Equity%')
            ->orWhere('name', 'like', '%Capital%')
            ->orWhere('name', 'like', '%Investment%')
            ->pluck('id')
            ->toArray();

        $equityAccIds = DB::table('accounts')
            ->whereIn('head_id', $equityHeadIds)
            ->orWhere('title', 'like', '%Capital%')
            ->orWhere('title', 'like', '%Investment%')
            ->pluck('id')
            ->toArray();

        $vouchers = $rvQ->get();
        $injectionsTotal = 0;
        $injectionsList = [];

        foreach ($vouchers as $v) {
            $isEquity = false;
            if (in_array($v->type, $equityHeadIds) || in_array($v->party_id, $equityAccIds)) {
                $isEquity = true;
            } else {
                // Check remarks or narration
                $narrText = strtolower($v->remarks ?? '');
                if (str_contains($narrText, 'capital') || str_contains($narrText, 'invest') || str_contains($narrText, 'outsource') || str_contains($narrText, 'loan')) {
                    $isEquity = true;
                }
            }

            if ($isEquity) {
                $amt = (float) $v->total_amount;
                $injectionsTotal += $amt;
                $injectionsList[] = [
                    'id'          => $v->id,
                    'rvid'        => $v->rvid,
                    'date'        => $v->entry_date ?? $v->receipt_date,
                    'amount'      => $amt,
                    'remarks'     => $v->remarks ?: 'Capital Injection / Outsource Fund',
                ];
            }
        }

        // Drawings / Outsource Payments (Payment Vouchers linked to Drawings / Owner Withdrawal)
        $pvQ = DB::table('payment_vouchers')
            ->whereBetween('entry_date', [$startDate, $endDate]);
        if (!$isAll) $pvQ->where('branch_id', $branchId);

        $drawingsHeadIds = DB::table('account_heads')
            ->where('name', 'like', '%Drawing%')
            ->orWhere('name', 'like', '%Withdrawal%')
            ->orWhere('name', 'like', '%Personal%')
            ->pluck('id')
            ->toArray();

        $drawingsAccIds = DB::table('accounts')
            ->whereIn('head_id', $drawingsHeadIds)
            ->orWhere('title', 'like', '%Drawing%')
            ->orWhere('title', 'like', '%Personal%')
            ->pluck('id')
            ->toArray();

        $pvtVouchers = $pvQ->get();
        $drawingsTotal = 0;
        $drawingsList = [];

        foreach ($pvtVouchers as $pv) {
            $isDrawing = false;
            if (in_array($pv->type, $drawingsHeadIds) || in_array($pv->party_id, $drawingsAccIds)) {
                $isDrawing = true;
            } else {
                $narrText = strtolower($pv->remarks ?? '');
                if (str_contains($narrText, 'drawing') || str_contains($narrText, 'withdraw') || str_contains($narrText, 'personal') || str_contains($narrText, 'outsource')) {
                    $isDrawing = true;
                }
            }

            if ($isDrawing) {
                $amt = (float) $pv->total_amount;
                $drawingsTotal += $amt;
                $drawingsList[] = [
                    'id'          => $pv->id,
                    'pvid'        => $pv->pvid,
                    'date'        => $pv->entry_date ?? $pv->receipt_date,
                    'amount'      => $amt,
                    'remarks'     => $pv->remarks ?: 'Owner Personal Withdrawal / Return of Capital',
                ];
            }
        }

        $netEquityMovement = $injectionsTotal - $drawingsTotal;

        return [
            'injections'          => $injectionsTotal,
            'drawings'            => $drawingsTotal,
            'net_movement'        => $netEquityMovement,
            'injections_history'  => array_slice($injectionsList, 0, 10),
            'drawings_history'    => array_slice($drawingsList, 0, 10),
        ];
    }

    /**
     * Compute Cumulative Equity Injections & Drawings as of a specific date.
     */
    private function calculateCumulativeEquity(string $asOfDate, $branchId): array
    {
        $isAll = ($branchId === 'all');

        $equityHeadIds = DB::table('account_heads')
            ->where('name', 'like', '%Equity%')
            ->orWhere('name', 'like', '%Capital%')
            ->orWhere('name', 'like', '%Investment%')
            ->pluck('id')
            ->toArray();

        $equityAccIds = DB::table('accounts')
            ->whereIn('head_id', $equityHeadIds)
            ->orWhere('title', 'like', '%Capital%')
            ->orWhere('title', 'like', '%Investment%')
            ->pluck('id')
            ->toArray();

        $rvQ = DB::table('receipts_vouchers')->where('entry_date', '<=', $asOfDate);
        if (!$isAll) $rvQ->where('branch_id', $branchId);

        $totalInjected = 0;
        foreach ($rvQ->get() as $v) {
            $isEquity = false;
            if (in_array($v->type, $equityHeadIds) || in_array($v->party_id, $equityAccIds)) {
                $isEquity = true;
            } else {
                $narrText = strtolower($v->remarks ?? '');
                if (str_contains($narrText, 'capital') || str_contains($narrText, 'invest') || str_contains($narrText, 'outsource') || str_contains($narrText, 'loan')) {
                    $isEquity = true;
                }
            }
            if ($isEquity) {
                $totalInjected += (float) $v->total_amount;
            }
        }

        $drawingsHeadIds = DB::table('account_heads')
            ->where('name', 'like', '%Drawing%')
            ->orWhere('name', 'like', '%Withdrawal%')
            ->orWhere('name', 'like', '%Personal%')
            ->pluck('id')
            ->toArray();

        $drawingsAccIds = DB::table('accounts')
            ->whereIn('head_id', $drawingsHeadIds)
            ->orWhere('title', 'like', '%Drawing%')
            ->orWhere('title', 'like', '%Personal%')
            ->pluck('id')
            ->toArray();

        $pvQ = DB::table('payment_vouchers')->where('entry_date', '<=', $asOfDate);
        if (!$isAll) $pvQ->where('branch_id', $branchId);

        $totalWithdrawn = 0;
        foreach ($pvQ->get() as $pv) {
            $isDrawing = false;
            if (in_array($pv->type, $drawingsHeadIds) || in_array($pv->party_id, $drawingsAccIds)) {
                $isDrawing = true;
            } else {
                $narrText = strtolower($pv->remarks ?? '');
                if (str_contains($narrText, 'drawing') || str_contains($narrText, 'withdraw') || str_contains($narrText, 'personal') || str_contains($narrText, 'outsource')) {
                    $isDrawing = true;
                }
            }
            if ($isDrawing) {
                $totalWithdrawn += (float) $pv->total_amount;
            }
        }

        $netInvestedCapital = $totalInjected - $totalWithdrawn;

        return [
            'total_injected'       => $totalInjected,
            'total_withdrawn'      => $totalWithdrawn,
            'net_invested_capital' => $netInvestedCapital,
        ];
    }

    /**
     * Compute Period-Specific Profit & Loss (Sales, COGS, Expenses, Net Profit).
     */
    private function calculatePeriodPL(string $startDate, string $endDate, $branchId): array
    {
        $startDT = $startDate . ' 00:00:00';
        $endDT   = $endDate   . ' 23:59:59';
        $isAll   = ($branchId === 'all');

        // Direct Sales Revenue
        $directSalesQ = DB::table('sales')->whereBetween('created_at', [$startDT, $endDT]);
        if (!$isAll) $directSalesQ->where('branch_id', $branchId);
        $directSales = (float) $directSalesQ->sum(DB::raw('COALESCE(total_net, total_bill_amount)'));

        // Distributor Sales Revenue
        $distSalesQ = DB::table('distributor_sales')->whereBetween('sale_date', [$startDate, $endDate]);
        $distSales = (float) $distSalesQ->sum('grand_total');

        $totalSales = $directSales + $distSales;

        // Purchases in this period
        $purchQ = DB::table('purchases')->whereBetween('purchase_date', [$startDate, $endDate]);
        if (!$isAll) $purchQ->where('branch_id', $branchId);
        $purchasesCost = (float) $purchQ->sum('net_amount');

        $rmPurchQ = DB::table('raw_material_purchases')->whereBetween('date', [$startDate, $endDate]);
        if (!$isAll) $rmPurchQ->where('branch_id', $branchId);
        $rmPurchasesCost = (float) $rmPurchQ->sum('total_cost');

        // Cost of Goods Sold estimate (Standard markup 70% of revenue or actual purchases)
        $cogs = min($totalSales * 0.70, ($purchasesCost + $rmPurchasesCost));
        if ($cogs <= 0 && $totalSales > 0) {
            $cogs = $totalSales * 0.65;
        }
        $grossProfit = max(0, $totalSales - $cogs);

        // Operating Expenses in this period
        $expQ = DB::table('expense_vouchers')->whereBetween('created_at', [$startDT, $endDT]);
        if (!$isAll && \Illuminate\Support\Facades\Schema::hasColumn('expense_vouchers', 'branch_id')) {
            $expQ->where('branch_id', $branchId);
        }
        $totalExpenses = (float) $expQ->sum('total_amount');

        // Net Profit = Gross Profit - Operating Expenses
        $netProfit = $grossProfit - $totalExpenses;

        return [
            'direct_sales'    => $directSales,
            'dist_sales'      => $distSales,
            'total_sales'     => $totalSales,
            'purchases_cost'  => $purchasesCost + $rmPurchasesCost,
            'cogs'            => $cogs,
            'gross_profit'    => $grossProfit,
            'total_expenses'  => $totalExpenses,
            'net_profit'      => $netProfit,
            'net_margin_pct'  => $totalSales > 0 ? round(($netProfit / $totalSales) * 100, 1) : 0,
        ];
    }

    /**
     * Compute Cumulative Balance Sheet & Net Worth as of a specific date.
     */
    private function calculateCumulativeBalanceSheet(string $asOfDate, $branchId, array $cumulativeEquity = []): array
    {
        $endDT = $asOfDate . ' 23:59:59';
        $isAll = ($branchId === 'all');

        // 1. CASH IN HAND & ACCOUNTS
        $cashAccountsQ = DB::table('accounts')
            ->leftJoin('account_heads', 'account_heads.id', '=', 'accounts.head_id')
            ->where('accounts.status', 1)
            ->where(function($q) {
                $q->where('account_heads.name', 'like', '%Cash%')
                  ->orWhere('accounts.title', 'like', '%Cash%');
            });
        $accountCashOpening = (float) $cashAccountsQ->sum('accounts.opening_balance');

        $salesCashQ = DB::table('sales')->where('created_at', '<=', $endDT);
        if (!$isAll) $salesCashQ->where('branch_id', $branchId);
        $totalSalesCash = (float) $salesCashQ->sum(DB::raw('COALESCE(cash, 0) - COALESCE(`change`, 0)'));

        $totalCash = max(0, $accountCashOpening + $totalSalesCash);

        // 2. BANK BALANCES
        $bankAccountsQ = DB::table('accounts')
            ->leftJoin('account_heads', 'account_heads.id', '=', 'accounts.head_id')
            ->where('accounts.status', 1)
            ->where(function($q) {
                $q->where('account_heads.name', 'like', '%Bank%')
                  ->orWhere('accounts.title', 'like', '%Bank%')
                  ->orWhere('accounts.head_id', 1);
            });
        $accountBankOpening = (float) $bankAccountsQ->sum('accounts.opening_balance');

        $custBankInflow = (float) DB::table('customer_payments')
            ->where('payment_date', '<=', $asOfDate)
            ->whereNotNull('card_account_id')
            ->sum(DB::raw('CASE WHEN card > 0 THEN card ELSE amount END'));

        $vendorBankOutflow = (float) DB::table('vendor_payments')
            ->where('payment_date', '<=', $asOfDate)
            ->whereNotNull('card_account_id')
            ->sum(DB::raw('CASE WHEN card > 0 THEN card ELSE amount END'));

        $totalBank = max(0, $accountBankOpening + $custBankInflow - $vendorBankOutflow);
        $cashAndBank = $totalCash + $totalBank;

        // 3. INVENTORY VALUE
        // A. Finished Goods Stock (Stocks table * Product Price)
        $fgValQ = DB::table('stocks')
            ->join('products', 'products.id', '=', 'stocks.product_id')
            ->where('stocks.qty', '>', 0);
        if (!$isAll) $fgValQ->where('stocks.branch_id', $branchId);
        $finishedGoodsValue = (float) $fgValQ->sum(DB::raw('stocks.qty * COALESCE(products.price, 0)'));

        // B. Raw Materials Live Stock
        $rawMaterialValue = 0;
        try {
            $rawMaterials = RawMaterial::all();
            foreach ($rawMaterials as $rm) {
                $factor = (float) ($rm->conversion_factor ?? 1);
                $sQty   = $isAll ? $rm->totalStock() : $rm->currentStock(null, $branchId);
                if ($sQty <= 0) continue;
                $purQty   = $factor > 0 ? ($sQty / $factor) : $sQty;
                $unitCost = (float) $rm->lastPurchaseCost();
                $rawMaterialValue += ($purQty * $unitCost);
            }
        } catch (\Throwable $e) {
            $rawMaterialValue = 0;
        }

        // C. Distributor Field / Van Custody Stock
        $fieldStockValue = (float) DB::table('distributor_stocks')
            ->where('qty', '>', 0)
            ->selectRaw('SUM(qty * avg_price) as val')
            ->value('val');

        $totalInventoryValue = $finishedGoodsValue + $rawMaterialValue + $fieldStockValue;

        // 4. ACCOUNTS RECEIVABLE (Direct Customers + Distributors)
        $custRecQ = DB::table('customers');
        if (!$isAll && \Illuminate\Support\Facades\Schema::hasColumn('customers', 'branch_id')) {
            $custRecQ->where('branch_id', $branchId);
        }
        $customersList = $custRecQ->select('id', 'opening_balance')->get();
        $latestCustomerLedgers = DB::table('customer_ledgers')
            ->select('customer_id', 'closing_balance')
            ->where('created_at', '<=', $endDT)
            ->orderBy('id', 'desc')
            ->get()
            ->unique('customer_id')
            ->keyBy('customer_id');

        $custReceivable = 0;
        foreach ($customersList as $cust) {
            $bal = 0;
            if (isset($latestCustomerLedgers[$cust->id])) {
                $bal = (float)$latestCustomerLedgers[$cust->id]->closing_balance;
            } else {
                $init = (float)($cust->opening_balance ?? 0);
                $cSales = (float)DB::table('sales')->where('customer', $cust->id)->where('created_at', '<=', $endDT)->sum(DB::raw('COALESCE(total_net, total_bill_amount)'));
                $cPay = (float)DB::table('customer_payments')->where('customer_id', $cust->id)->where('payment_date', '<=', $asOfDate)->sum('amount');
                $bal = $init + $cSales - $cPay;
            }
            if ($bal > 0) {
                $custReceivable += $bal;
            }
        }

        $distReceivable = (float) DB::table('distributors')
            ->where('status', 'active')
            ->sum('current_balance');

        $totalReceivable = max(0, $custReceivable + $distReceivable);

        // 5. FIXED ASSETS
        $assetsQ = Asset::whereNull('deleted_at')->where('status', '!=', 'disposed');
        if (!$isAll && \Illuminate\Support\Facades\Schema::hasColumn('assets', 'branch_id')) {
            $assetsQ->where('branch_id', $branchId);
        }
        $totalFixedAssets = (float) $assetsQ->sum(DB::raw('COALESCE(current_value, purchase_cost)'));

        // TOTAL CURRENT ASSETS & OVERALL ASSETS
        $currentAssets = $cashAndBank + $totalInventoryValue + $totalReceivable;
        $totalAssets   = $currentAssets + $totalFixedAssets;

        // 6. LIABILITIES
        $vendorsList = DB::table('vendors')->select('id', 'opening_balance')->get();
        $latestVendorLedgers = DB::table('vendor_ledgers')
            ->select('vendor_id', 'closing_balance')
            ->where('created_at', '<=', $endDT)
            ->orderBy('id', 'desc')
            ->get()
            ->unique('vendor_id')
            ->keyBy('vendor_id');

        $totalPayables = 0;
        foreach ($vendorsList as $vnd) {
            $bal = 0;
            if (isset($latestVendorLedgers[$vnd->id])) {
                $bal = (float)$latestVendorLedgers[$vnd->id]->closing_balance;
            } else {
                $init = (float)($vnd->opening_balance ?? 0);
                $vPur = (float)DB::table('purchases')->where('vendor_id', $vnd->id)->where('purchase_date', '<=', $asOfDate)->sum('net_amount');
                $vRmPur = (float)DB::table('raw_material_purchases')->where('vendor_id', $vnd->id)->where('date', '<=', $asOfDate)->sum('total_cost');
                $vRet = (float)DB::table('purchase_returns')->where('vendor_id', $vnd->id)->where('return_date', '<=', $asOfDate)->sum('net_amount');
                $vRmRet = (float)DB::table('raw_material_purchase_returns')->where('vendor_id', $vnd->id)->where('return_date', '<=', $asOfDate)->sum('total_cost');
                $vPay = (float)DB::table('vendor_payments')->where('vendor_id', $vnd->id)->where('payment_date', '<=', $asOfDate)->sum('amount');
                $bal = $init + $vPur + $vRmPur - $vRet - $vRmRet - $vPay;
            }
            if ($bal > 0) {
                $totalPayables += $bal;
            }
        }

        $claimsPayable = (float) DB::table('distributor_claims')
            ->where('status', 'pending')
            ->sum('total_amount');

        $totalLiabilities = max(0, $totalPayables + $claimsPayable);

        // 7. NET WORTH = TOTAL ASSETS - TOTAL LIABILITIES
        $netWorth = $totalAssets - $totalLiabilities;

        $netInvestedCapital = $cumulativeEquity['net_invested_capital'] ?? 0;
        $retainedEarnings = $netWorth - $netInvestedCapital;

        return [
            'cash_and_bank'      => $cashAndBank,
            'cash_in_hand'       => $totalCash,
            'bank_balances'      => $totalBank,
            'inventory'          => [
                'finished_goods' => $finishedGoodsValue,
                'raw_materials'  => $rawMaterialValue,
                'field_custody'  => $fieldStockValue,
                'total'          => $totalInventoryValue,
            ],
            'receivables'        => [
                'direct_customers' => $custReceivable,
                'distributors'     => $distReceivable,
                'total'            => $totalReceivable,
            ],
            'fixed_assets'       => $totalFixedAssets,
            'current_assets'     => $currentAssets,
            'total_assets'       => $totalAssets,
            'payables'           => [
                'vendors' => $totalPayables,
                'claims'  => $claimsPayable,
                'total'   => $totalLiabilities,
            ],
            'total_liabilities'  => $totalLiabilities,
            'net_worth'          => $netWorth,
            'equity_breakdown'   => [
                'net_invested_capital' => $netInvestedCapital,
                'retained_earnings'    => $retainedEarnings,
            ],
        ];
    }

    /**
     * Helper to compute absolute delta, percentage difference, and direction.
     */
    private function buildDeltaItem(float $current, float $baseline, string $type = 'currency', bool $invertDirection = false): array
    {
        $diff = $current - $baseline;
        $pct  = ($baseline != 0) ? round(($diff / abs($baseline)) * 100, 1) : ($current != 0 ? 100.0 : 0.0);

        $isPositive = $diff > 0;
        $isGood = $invertDirection ? ($diff <= 0) : ($diff >= 0);

        return [
            'current'       => $current,
            'baseline'      => $baseline,
            'difference'    => $diff,
            'pct'           => abs($pct),
            'direction'     => $diff > 0 ? 'up' : ($diff < 0 ? 'down' : 'flat'),
            'status'        => $diff == 0 ? 'neutral' : ($isGood ? 'good' : 'bad'),
            'type'          => $type,
        ];
    }
}

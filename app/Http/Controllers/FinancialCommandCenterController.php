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
use App\Models\ProductVariant;
use App\Models\RawMaterial;
use App\Models\ReceiptsVoucher;
use App\Models\Sale;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
     * Compares "Current Period" vs "Baseline Period".
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

        // 1. Compute Period Metrics (P&L: Sales, Returns, Net Revenue, COGS, Expenses, Net Profit)
        $currPL = $this->calculatePeriodPL($currStart, $currEnd, $branchId);
        $basePL = $this->calculatePeriodPL($baseStart, $baseEnd, $branchId);

        // 2. Compute Capital / Equity Movements (Outsource Injections vs Owner Drawings)
        $currEquityMovements = $this->calculateEquityMovements($currStart, $currEnd, $branchId);
        $baseEquityMovements = $this->calculateEquityMovements($baseStart, $baseEnd, $branchId);
        $cumulativeEquity   = $this->calculateCumulativeEquity($currEnd, $branchId);
        $baseCumulativeEq   = $this->calculateCumulativeEquity($baseEnd, $branchId);

        // 3. Compute Cumulative Balance Sheet & Net Worth AS OF Current vs AS OF Baseline
        $currBS = $this->calculateCumulativeBalanceSheet($currEnd, $branchId, $cumulativeEquity);
        $baseBS = $this->calculateCumulativeBalanceSheet($baseEnd, $branchId, $baseCumulativeEq);

        // 4. Compute Delta Comparison Indicators
        $comparison = [
            'labels' => [
                'current'  => $currLabel,
                'baseline' => $baseLabel,
            ],
            'net_worth'          => $this->buildDeltaItem($currBS['net_worth'], $baseBS['net_worth'], 'currency'),
            'total_assets'       => $this->buildDeltaItem($currBS['total_assets'], $baseBS['total_assets'], 'currency'),
            'total_liabilities'  => $this->buildDeltaItem($currBS['total_liabilities'], $baseBS['total_liabilities'], 'currency', true),
            'sales'              => $this->buildDeltaItem($currPL['net_revenue'], $basePL['net_revenue'], 'currency'),
            'expenses'           => $this->buildDeltaItem($currPL['total_expenses'], $basePL['total_expenses'], 'currency', true),
            'net_profit'         => $this->buildDeltaItem($currPL['net_profit'], $basePL['net_profit'], 'currency'),
            'capital_injected'   => $this->buildDeltaItem($currEquityMovements['injections'], $baseEquityMovements['injections'], 'currency'),
            'drawings_withdrawn' => $this->buildDeltaItem($currEquityMovements['drawings'], $baseEquityMovements['drawings'], 'currency', true),
            'cash_and_bank'      => $this->buildDeltaItem($currBS['cash_and_bank'], $baseBS['cash_and_bank'], 'currency'),
            'inventory_value'    => $this->buildDeltaItem($currBS['inventory']['total'], $baseBS['inventory']['total'], 'currency'),
            'receivables'        => $this->buildDeltaItem($currBS['receivables']['total'], $baseBS['receivables']['total'], 'currency'),
            'payables'           => $this->buildDeltaItem($currBS['payables']['total'], $baseBS['payables']['total'], 'currency', true),
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
     * Fetch voucher total & latest entries for Receipt/Payment Vouchers within optional period or cumulative up to $endDate.
     */
    private function fetchVoucherMovements(string $table, ?string $startDate, string $endDate, $branchId, string $refCol, string $defaultRemark, bool $withHistory = true): array
    {
        $endDT = $endDate . ' 23:59:59';
        $q = DB::table($table);

        if ($startDate !== null) {
            $startDT = $startDate . ' 00:00:00';
            $q->where(function ($sub) use ($startDate, $endDate, $startDT, $endDT) {
                $sub->whereBetween('entry_date', [$startDate, $endDate])
                    ->orWhereBetween('receipt_date', [$startDate, $endDate])
                    ->orWhereBetween('created_at', [$startDT, $endDT]);
            });
        } else {
            $q->where(function ($sub) use ($endDate, $endDT) {
                $sub->where('entry_date', '<=', $endDate)
                    ->orWhere('receipt_date', '<=', $endDate)
                    ->orWhere('created_at', '<=', $endDT);
            });
        }

        if ($branchId !== 'all' && Schema::hasColumn($table, 'branch_id')) {
            $q->where('branch_id', $branchId);
        }

        $rows = $q->orderByDesc('id')->get();
        $total = 0.0;
        $history = [];

        foreach ($rows as $row) {
            $amt = (float) ($row->total_amount ?? 0);
            $total += $amt;

            if ($withHistory && count($history) < 10) {
                $history[] = [
                    'id'      => $row->id,
                    $refCol   => $row->{$refCol} ?? (strtoupper(substr($refCol, 0, 2)) . '-' . $row->id),
                    'date'    => $row->receipt_date ?? $row->entry_date ?? substr((string) ($row->created_at ?? ''), 0, 10),
                    'amount'  => $amt,
                    'remarks' => $row->remarks ?: $defaultRemark,
                ];
            }
        }

        return [
            'total'   => round($total, 2),
            'history' => $history,
        ];
    }

    /**
     * Calculate Period-Specific Receipts & Payments (every Receipt Voucher & Payment Voucher).
     */
    private function calculateEquityMovements(string $startDate, string $endDate, $branchId): array
    {
        $rv = $this->fetchVoucherMovements('receipts_vouchers', $startDate, $endDate, $branchId, 'rvid', 'Receipt Voucher Inflow', true);
        $pv = $this->fetchVoucherMovements('payment_vouchers', $startDate, $endDate, $branchId, 'pvid', 'Payment Voucher Outflow', true);

        return [
            'injections'         => $rv['total'],
            'drawings'           => $pv['total'],
            'net_movement'       => round($rv['total'] - $pv['total'], 2),
            'injections_history' => $rv['history'],
            'drawings_history'   => $pv['history'],
        ];
    }

    /**
     * Compute Cumulative Receipt & Payment Vouchers as of a specific date.
     */
    private function calculateCumulativeEquity(string $asOfDate, $branchId): array
    {
        $rv = $this->fetchVoucherMovements('receipts_vouchers', null, $asOfDate, $branchId, 'rvid', 'Receipt Voucher Inflow', false);
        $pv = $this->fetchVoucherMovements('payment_vouchers', null, $asOfDate, $branchId, 'pvid', 'Payment Voucher Outflow', false);

        return [
            'total_injected'       => $rv['total'],
            'total_withdrawn'      => $pv['total'],
            'net_invested_capital' => round($rv['total'] - $pv['total'], 2),
        ];
    }

    /**
     * Compute Period-Specific Profit & Loss (Sales, Deductions, COGS, Expenses, Net Profit).
     */
    private function calculatePeriodPL(string $startDate, string $endDate, $branchId): array
    {
        $startDT = $startDate . ' 00:00:00';
        $endDT   = $endDate   . ' 23:59:59';
        $isAll   = ($branchId === 'all');

        // 1. Direct Sales & Returns (Retail Counter & Direct Customers)
        $directSalesQ = DB::table('sales')->whereBetween('created_at', [$startDT, $endDT])
            ->where(function ($q) {
                $q->whereNull('customer')
                  ->orWhere(function($sub) {
                      $sub->where('customer', 'not like', 'dist_%')
                          ->where('customer', 'not like', 'dc_%');
                  });
            });
        if (!$isAll) $directSalesQ->where('branch_id', $branchId);
        $directGrossSales = (float) $directSalesQ->sum(DB::raw('COALESCE(total_net, total_bill_amount)'));

        $directReturnsQ = DB::table('sales_returns')->whereBetween('created_at', [$startDT, $endDT])
            ->where(function ($q) {
                $q->whereNull('customer')
                  ->orWhere(function($sub) {
                      $sub->where('customer', 'not like', 'dist_%')
                          ->where('customer', 'not like', 'dc_%');
                  });
            });
        if (!$isAll) $directReturnsQ->where('branch_id', $branchId);
        $directReturns = (float) $directReturnsQ->sum('total_net');
        $netDirectSales = max(0, $directGrossSales - $directReturns);

        // 2. Primary Distributor Dispatches & Returns (Company -> Distributor Network)
        $distDispatchesQ = DB::table('sales')->whereBetween('created_at', [$startDT, $endDT])
            ->where(function ($q) {
                $q->where('customer', 'like', 'dist_%')
                  ->orWhere('customer', 'like', 'dc_%');
            });
        if (!$isAll) $distDispatchesQ->where('branch_id', $branchId);
        $distGrossDispatches = (float) $distDispatchesQ->sum(DB::raw('COALESCE(total_net, total_bill_amount)'));

        $distDispatchReturnsQ = DB::table('sales_returns')->whereBetween('created_at', [$startDT, $endDT])
            ->where(function ($q) {
                $q->where('customer', 'like', 'dist_%')
                  ->orWhere('customer', 'like', 'dc_%');
            });
        if (!$isAll) $distDispatchReturnsQ->where('branch_id', $branchId);
        $distDispatchReturns = (float) $distDispatchReturnsQ->sum('total_net');
        $netDistDispatches = max(0, $distGrossDispatches - $distDispatchReturns);

        // 3. Secondary Market Sales (Distributors -> Market Retail Stores)
        $secSalesQ = DB::table('distributor_sales')->whereBetween('sale_date', [$startDate, $endDate]);
        $secondarySales = (float) $secSalesQ->sum('grand_total');

        $secReturnsQ = DB::table('distributor_sales_returns')->whereBetween('created_at', [$startDT, $endDT]);
        $secondaryReturns = (float) $secReturnsQ->sum('net_return_amount');
        $netSecondarySales = max(0, $secondarySales - $secondaryReturns);

        // Overall Company Gross & Net Turnover
        $totalGrossSales = $directGrossSales + $distGrossDispatches;
        $totalReturns    = $directReturns + $distDispatchReturns;
        $netRevenue      = max(0, $totalGrossSales - $totalReturns);

        // 4. Calculate COGS item-by-item from sold items in this period
        $allPeriodSalesQ = DB::table('sales')->whereBetween('created_at', [$startDT, $endDT]);
        if (!$isAll) $allPeriodSalesQ->where('branch_id', $branchId);
        $allPeriodSales = $allPeriodSalesQ->select('product', 'variant_id', 'qty')->get();

        $variants = DB::table('product_variants')->get()->keyBy('id');
        $products = DB::table('products')->get()->keyBy('id');

        $totalCogs = 0;
        foreach ($allPeriodSales as $s) {
            $pids = explode(',', $s->product ?? '');
            $vids = explode(',', $s->variant_id ?? '');
            $qtys = explode(',', $s->qty ?? '');

            foreach ($pids as $idx => $pid) {
                $pid = trim($pid);
                if (!$pid || !is_numeric($pid)) continue;
                $vid = trim($vids[$idx] ?? '0');
                $qty = (float)($qtys[$idx] ?? 0);

                $cost = 0;
                if ($vid && isset($variants[$vid]) && (float)$variants[$vid]->cost_price > 0) {
                    $cost = (float)$variants[$vid]->cost_price;
                } elseif (isset($products[$pid])) {
                    $firstV = DB::table('product_variants')->where('product_id', $pid)->first();
                    if ($firstV && (float)$firstV->cost_price > 0) {
                        $cost = (float)$firstV->cost_price;
                    } elseif (isset($variants[$vid]) && (float)$variants[$vid]->price > 0) {
                        $cost = (float)$variants[$vid]->price * 0.70;
                    }
                }
                $totalCogs += ($qty * $cost);
            }
        }

        // Adjust COGS for returned goods
        $returnItemsQ = DB::table('sales_returns')->whereBetween('created_at', [$startDT, $endDT]);
        if (!$isAll) $returnItemsQ->where('branch_id', $branchId);
        $returnItems = $returnItemsQ->select('product', 'variant_id', 'qty')->get();

        $returnCostReduction = 0;
        foreach ($returnItems as $r) {
            $pids = explode(',', $r->product ?? '');
            $vids = explode(',', $r->variant_id ?? '');
            $qtys = explode(',', $r->qty ?? '');
            foreach ($pids as $idx => $pid) {
                $pid = trim($pid);
                if (!$pid || !is_numeric($pid)) continue;
                $vid = trim($vids[$idx] ?? '0');
                $qty = (float)($qtys[$idx] ?? 0);
                $cost = (isset($variants[$vid]) && (float)$variants[$vid]->cost_price > 0) ? (float)$variants[$vid]->cost_price : 0;
                $returnCostReduction += ($qty * $cost);
            }
        }
        $cogs = max(0, $totalCogs - $returnCostReduction);

        // Fallback estimate if no item cost configured
        if ($cogs <= 0 && $netRevenue > 0) {
            $cogs = $netRevenue * 0.65;
        }

        $grossProfit = max(0, $netRevenue - $cogs);

        // 5. Operating Expenses in period (excluding stock write-off adjustments)
        $expQ = DB::table('expense_vouchers')
            ->where(function($q) use ($startDT, $endDT, $startDate, $endDate) {
                $q->whereBetween('created_at', [$startDT, $endDT])
                  ->orWhereBetween('date', [$startDate, $endDate]);
            })
            ->where(function($q) {
                $q->whereNull('is_stock_adjustment')->orWhere('is_stock_adjustment', 0);
            });

        if (!$isAll && Schema::hasColumn('expense_vouchers', 'branch_id')) {
            $expQ->where('branch_id', $branchId);
        }
        $totalExpenses = (float) $expQ->sum('total_amount');

        // Net Profit = Gross Profit - Operating Expenses
        $netProfit = $grossProfit - $totalExpenses;

        return [
            'direct_sales'            => $directGrossSales,
            'direct_returns'          => $directReturns,
            'net_direct_sales'        => $netDirectSales,
            'dist_sales'              => $distGrossDispatches,
            'dist_returns'            => $distDispatchReturns,
            'net_dist_sales'          => $netDistDispatches,
            'secondary_market_sales'  => $netSecondarySales,
            'total_gross_sales'       => $totalGrossSales,
            'total_returns'           => $totalReturns,
            'total_sales'             => $netRevenue, // Standard alias for total net sales
            'net_revenue'             => $netRevenue,
            'cogs'                    => $cogs,
            'gross_profit'            => $grossProfit,
            'total_expenses'          => $totalExpenses,
            'net_profit'              => $netProfit,
            'net_margin_pct'          => $netRevenue > 0 ? round(($netProfit / $netRevenue) * 100, 1) : 0,
        ];
    }

    /**
     * Compute Cumulative Balance Sheet & Net Worth as of a specific date.
     */
    private function calculateCumulativeBalanceSheet(string $asOfDate, $branchId, array $cumulativeEquity = []): array
    {
        $endDT = $asOfDate . ' 23:59:59';
        $isAll = ($branchId === 'all');
        $activeBranchId = $isAll ? null : (int)$branchId;

        // 1. CASH IN HAND & ACCOUNTS
        $cashAccountsQ = DB::table('accounts')
            ->leftJoin('account_heads', 'account_heads.id', '=', 'accounts.head_id')
            ->where('accounts.status', 1)
            ->whereNull('accounts.distributor_id')
            ->where(function($q) {
                $q->where('account_heads.name', 'like', '%Cash%')
                  ->orWhere('accounts.title', 'like', '%Cash%')
                  ->orWhere('accounts.head_id', 2);
            });
        $cashAccounts = $cashAccountsQ->select('accounts.id', 'accounts.title', 'accounts.account_code', 'accounts.opening_balance')->get();
        $totalCash = max(0, (float) $cashAccounts->sum('opening_balance'));
        $counterCashBalance = 0;
        $accountCashOpening = $totalCash;

        $salesCashQ = DB::table('sales')->where('created_at', '<=', $endDT);
        if (!$isAll) $salesCashQ->where('branch_id', $branchId);
        $totalSalesCash = (float) $salesCashQ->sum(DB::raw('COALESCE(cash, 0) - COALESCE(`change`, 0)'));

        $salesReturnCashQ = DB::table('sales_returns')->where('created_at', '<=', $endDT);
        if (!$isAll) $salesReturnCashQ->where('branch_id', $branchId);
        $totalSalesReturnCash = (float) $salesReturnCashQ->sum('cash');

        $custCashQ = DB::table('customer_payments')
            ->where('payment_date', '<=', $asOfDate)
            ->where(function($q) {
                $q->whereNull('note')->orWhere('note', 'not like', '%Paid on Sale%');
            })
            ->where(function($q) {
                $q->where('payment_method', 'Cash')->orWhere('cash', '>', 0);
            });
        if (!$isAll && Schema::hasColumn('customer_payments', 'branch_id')) {
            $custCashQ->where('branch_id', $branchId);
        }
        $custCashInflow = (float) $custCashQ->sum(DB::raw('CASE WHEN cash > 0 THEN cash ELSE amount END'));

        $distCashInflow = (float) DB::table('distributor_payments')
            ->where('payment_date', '<=', $asOfDate)
            ->where('status', 'approved')
            ->where(function($q) {
                $q->whereNull('notes')->orWhere('notes', 'not like', '%Payment on Sale Invoice%');
            })
            ->where('payment_method', 'Cash')
            ->sum('amount');

        $vendCashQ = DB::table('vendor_payments')
            ->where('payment_date', '<=', $asOfDate)
            ->where(function($q) {
                $q->whereNull('note')->orWhere('note', 'not like', '%Paid on Purchase%');
            })
            ->where(function($q) {
                $q->where('payment_method', 'Cash')->orWhere('cash', '>', 0);
            });
        if (!$isAll && Schema::hasColumn('vendor_payments', 'branch_id')) {
            $vendCashQ->where('branch_id', $branchId);
        }
        $vendCashOutflow = (float) $vendCashQ->sum(DB::raw('CASE WHEN cash > 0 THEN cash ELSE amount END'));

        $cashAccIds = DB::table('accounts')
            ->leftJoin('account_heads', 'account_heads.id', '=', 'accounts.head_id')
            ->whereNull('accounts.distributor_id')
            ->where(function($q) {
                $q->where('account_heads.name', 'like', '%Cash%')
                  ->orWhere('accounts.title', 'like', '%Cash%')
                  ->orWhere('accounts.head_id', 2);
            })
            ->pluck('accounts.id')
            ->toArray();

        $expenseCashQ = DB::table('expense_vouchers')
            ->where('created_at', '<=', $endDT)
            ->where(function($q) use ($cashAccIds) {
                $q->whereIn('party_id', $cashAccIds)
                  ->orWhere('type', 'cash')
                  ->orWhere('type', 'walkin')
                  ->orWhere('type', 2);
            })
            ->where(function($q) {
                $q->whereNull('is_stock_adjustment')->orWhere('is_stock_adjustment', 0);
            });
        if (!$isAll && Schema::hasColumn('expense_vouchers', 'branch_id')) {
            $expenseCashQ->where('branch_id', $branchId);
        }
        $expenseCashOutflow = (float) $expenseCashQ->sum('total_amount');

        $totalCash = max(0, (float) $cashAccounts->sum('opening_balance'));
        $counterCashBalance = 0;

        $cashAccountsDetailed = [];
        foreach ($cashAccounts as $ca) {
            $cashAccountsDetailed[] = [
                'id'      => $ca->id,
                'title'   => $ca->title,
                'code'    => $ca->account_code ?? ('ACC-' . $ca->id),
                'balance' => (float)$ca->opening_balance,
            ];
        }

        // 2. BANK BALANCES
        $bankAccountsQ = DB::table('accounts')
            ->leftJoin('account_heads', 'account_heads.id', '=', 'accounts.head_id')
            ->where('accounts.status', 1)
            ->whereNull('accounts.distributor_id')
            ->where(function($q) {
                $q->where('account_heads.name', 'like', '%Bank%')
                  ->orWhere('accounts.title', 'like', '%Bank%')
                  ->orWhere('accounts.head_id', 1);
            });
        $bankAccountsData = $bankAccountsQ->select('accounts.id', 'accounts.title', 'accounts.account_code', 'accounts.bank_name', 'accounts.opening_balance')->get();

        $bankAccountsDetailed = [];
        $totalBank = 0;
        foreach ($bankAccountsData as $ba) {
            $op = (float)$ba->opening_balance;
            $cpQ = DB::table('customer_payments')->where('card_account_id', $ba->id)->where('payment_date', '<=', $asOfDate);
            if (!$isAll && Schema::hasColumn('customer_payments', 'branch_id')) $cpQ->where('branch_id', $branchId);
            $inflow = (float)$cpQ->sum(DB::raw('CASE WHEN card > 0 THEN card ELSE amount END'));

            // Distributor payments into this bank account
            $dpInflow = (float)DB::table('distributor_payments')
                ->where('account_id', $ba->id)
                ->where('payment_date', '<=', $asOfDate)
                ->where('status', 'approved')
                ->sum('amount');
            $inflow += $dpInflow;

            $vpQ = DB::table('vendor_payments')->where('card_account_id', $ba->id)->where('payment_date', '<=', $asOfDate);
            if (!$isAll && Schema::hasColumn('vendor_payments', 'branch_id')) $vpQ->where('branch_id', $branchId);
            $outflow = (float)$vpQ->sum(DB::raw('CASE WHEN card > 0 THEN card ELSE amount END'));

            $expBankQ = DB::table('expense_vouchers')->where('party_id', $ba->id)->where('created_at', '<=', $endDT);
            if (!$isAll && Schema::hasColumn('expense_vouchers', 'branch_id')) $expBankQ->where('branch_id', $branchId);
            $expOut = (float)$expBankQ->sum('total_amount');
            $outflow += $expOut;

            // Direct sales card payments to this bank account
            $salesCardQ = DB::table('sales')->where('card_account_id', $ba->id)->where('created_at', '<=', $endDT);
            if (!$isAll) $salesCardQ->where('branch_id', $branchId);
            $salesCardIn = (float)$salesCardQ->sum('card');
            $inflow += $salesCardIn;

            $curBal = $op + $inflow - $outflow;
            $bankAccountsDetailed[] = [
                'id'              => $ba->id,
                'title'           => $ba->title,
                'code'            => $ba->account_code ?? ('ACC-' . $ba->id),
                'bank_name'       => $ba->bank_name ?? 'Bank',
                'opening_balance' => $op,
                'inflow'          => $inflow,
                'outflow'         => $outflow,
                'current_balance' => round($curBal, 2),
            ];
            $totalBank += $curBal;
        }
        $totalBank = max(0, $totalBank);
        $cashAndBank = $totalCash + $totalBank;

        // 3. INVENTORY VALUE (EXACT RECONCILIATION WITH ITEM STOCK REPORT & BALANCE SHEET)
        $stockReq = new Request([
            'item_type'  => 'all',
            'start_date' => date('Y-m-01', strtotime($asOfDate)),
            'end_date'   => $asOfDate,
            'start_time' => '00:00:00',
            'end_time'   => '23:59:59',
            'branch_id'  => $branchId,
        ]);

        $reportingCtrl = app(\App\Http\Controllers\ReportingController::class);
        $stockResponse = $reportingCtrl->fetchItemStock($stockReq);
        $stockData = $stockResponse->getData(true);

        $finishedGoodsValue = (float)($stockData['fg_value'] ?? 0);
        $rawMaterialValue   = (float)($stockData['rm_value'] ?? 0);
        $totalInventoryValue = (float)($stockData['total_value'] ?? ($finishedGoodsValue + $rawMaterialValue));

        // Build finished_items array for the modal
        $finishedItems = [];
        foreach (($stockData['data'] ?? []) as $row) {
            if (($row['item_type'] ?? 'fg') === 'rm') continue;
            $qty = (float)($row['balance'] ?? 0);
            if ($qty <= 0) continue;
            $displayQty = !empty($row['is_kg']) ? ($qty / 1000) : $qty;
            $unitCost = (float)($row['val_price'] ?? 0);
            $lineVal = (float)($row['row_value'] ?? ($displayQty * $unitCost));

            $finishedItems[] = [
                'name'      => $row['item_name'],
                'code'      => $row['item_code'] ?? '-',
                'category'  => $row['category'] ?? 'General',
                'qty'       => round($displayQty, 2),
                'unit'      => $row['unit'] ?? (!empty($row['is_kg']) ? 'KG' : 'PC'),
                'unit_cost' => round($unitCost, 2),
                'total_val' => round($lineVal, 2),
            ];
        }
        usort($finishedItems, fn($a, $b) => $b['total_val'] <=> $a['total_val']);

        // Build raw_items array for the modal
        $rawItems = [];
        foreach (($stockData['data'] ?? []) as $row) {
            if (($row['item_type'] ?? '') !== 'rm') continue;
            $qty = (float)($row['balance'] ?? 0);
            if ($qty <= 0) continue;
            $unitCost = (float)($row['val_price'] ?? 0);
            $lineVal = (float)($row['row_value'] ?? ($qty * $unitCost));

            $rawItems[] = [
                'id'        => $row['product_id'] ?? 0,
                'name'      => $row['item_name'],
                'code'      => $row['item_code'] ?? '-',
                'qty'       => round($qty, 2),
                'unit'      => $row['unit'] ?? 'Unit',
                'unit_cost' => round($unitCost, 2),
                'total_val' => round($lineVal, 2),
            ];
        }
        usort($rawItems, fn($a, $b) => $b['total_val'] <=> $a['total_val']);

        // 4. ACCOUNTS RECEIVABLE (Direct Customers + Distributors + Market Retail Shops)
        $custRecQ = DB::table('customers');
        if (!$isAll && Schema::hasColumn('customers', 'branch_id')) {
            $custRecQ->where('branch_id', $branchId);
        }
        $customersList = $custRecQ->select('id', 'customer_name', 'mobile', 'opening_balance')->get();
        $latestCustomerLedgers = DB::table('customer_ledgers')
            ->select('customer_id', 'closing_balance')
            ->where('created_at', '<=', $endDT)
            ->orderBy('id', 'desc')
            ->get()
            ->unique('customer_id')
            ->keyBy('customer_id');

        $custReceivable = 0;
        $customersDetailed = [];
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
                $customersDetailed[] = [
                    'id'         => $cust->id,
                    'name'       => $cust->customer_name,
                    'phone'      => $cust->mobile ?? '-',
                    'balance'    => round($bal, 2),
                    'ledger_url' => route('customers.ledger') . '?customer_id=' . $cust->id,
                ];
            }
        }
        usort($customersDetailed, fn($a, $b) => $b['balance'] <=> $a['balance']);

        // Distributors where balance > 0 (They owe company money) and balance < 0 (Advances)
        $distributorsList = DB::table('distributors')
            ->where('status', 'active')
            ->select('id', 'name', 'phone', 'area', 'code', 'current_balance')
            ->get();

        $distReceivable = 0;
        $distPayableAdvance = 0;
        $distributorsDetailed = [];
        $distAdvancesDetailed = [];

        foreach ($distributorsList as $dist) {
            $dBal = (float)$dist->current_balance;
            if ($dBal > 0) {
                $distReceivable += $dBal;
                $distributorsDetailed[] = [
                    'id'         => $dist->id,
                    'code'       => $dist->code ?? ('DIST-' . $dist->id),
                    'name'       => $dist->name,
                    'phone'      => $dist->phone ?? '-',
                    'area'       => $dist->area ?? '-',
                    'balance'    => round($dBal, 2),
                    'ledger_url' => route('admin.distributors.ledger', $dist->id),
                ];
            } elseif ($dBal < 0) {
                $adv = abs($dBal);
                $distPayableAdvance += $adv;
                $distAdvancesDetailed[] = [
                    'id'         => $dist->id,
                    'code'       => $dist->code ?? ('DIST-' . $dist->id),
                    'name'       => $dist->name,
                    'phone'      => $dist->phone ?? '-',
                    'area'       => $dist->area ?? '-',
                    'balance'    => round($adv, 2),
                    'ledger_url' => route('admin.distributors.ledger', $dist->id),
                ];
            }
        }
        usort($distributorsDetailed, fn($a, $b) => $b['balance'] <=> $a['balance']);
        usort($distAdvancesDetailed, fn($a, $b) => $b['balance'] <=> $a['balance']);

        // Market / Retail Shop receivables (distributor_customers)
        // Exclude distributor's customers to prevent double counting (their balance is already in distributor ledger)
        $retailShops = DB::table('distributor_customers')
            ->leftJoin('distributors', 'distributors.id', '=', 'distributor_customers.distributor_id')
            ->where('distributor_customers.status', 'active')
            ->where('distributor_customers.current_balance', '>', 0)
            ->where(function ($q) {
                $q->whereNull('distributor_customers.distributor_id')
                  ->orWhere('distributor_customers.distributor_id', 0);
            })
            ->select(
                'distributor_customers.id',
                'distributor_customers.shop_name',
                'distributor_customers.owner_name',
                'distributor_customers.phone',
                'distributor_customers.area',
                'distributor_customers.current_balance',
                'distributors.name as distributor_name'
            )
            ->get();

        $marketReceivable = 0;
        $retailShopsDetailed = [];
        foreach ($retailShops as $shop) {
            $sBal = (float)$shop->current_balance;
            $marketReceivable += $sBal;
            $retailShopsDetailed[] = [
                'id'               => $shop->id,
                'shop_name'        => $shop->shop_name ?: $shop->owner_name,
                'owner_name'       => $shop->owner_name ?? '-',
                'phone'            => $shop->phone ?? '-',
                'area'             => $shop->area ?? '-',
                'distributor_name' => $shop->distributor_name ?? 'Direct',
                'balance'          => round($sBal, 2),
                'ledger_url'       => route('admin.distributors.customers.ledger', $shop->id),
            ];
        }
        usort($retailShopsDetailed, fn($a, $b) => $b['balance'] <=> $a['balance']);

        $totalReceivable = $custReceivable + $distReceivable + $marketReceivable;

        // 5. FIXED ASSETS
        $assetsQ = Asset::whereNull('deleted_at')->where('status', '!=', 'disposed');
        if (!$isAll && Schema::hasColumn('assets', 'branch_id')) {
            $assetsQ->where('branch_id', $branchId);
        }
        $assetsList = $assetsQ->with('category')->get();
        $totalFixedAssets = 0;
        $assetsDetailed = [];
        foreach ($assetsList as $ast) {
            $cost = (float)$ast->purchase_cost;
            $val  = (float)($ast->current_value > 0 ? $ast->current_value : $ast->estimated_current_value);
            if ($val <= 0) $val = $cost;
            $totalFixedAssets += $val;
            $assetsDetailed[] = [
                'id'            => $ast->id,
                'code'          => $ast->asset_code,
                'name'          => $ast->name,
                'category'      => $ast->category->name ?? 'Asset',
                'purchase_cost' => round($cost, 2),
                'current_value' => round($val, 2),
                'status'        => $ast->status,
            ];
        }

        // TOTAL CURRENT ASSETS & OVERALL ASSETS
        $currentAssets = $cashAndBank + $totalInventoryValue + $totalReceivable;
        $totalAssets   = $currentAssets + $totalFixedAssets;

        // 6. LIABILITIES
        $vendorsList = DB::table('vendors')->select('id', 'name', 'phone', 'opening_balance')->get();
        $latestVendorLedgers = DB::table('vendor_ledgers')
            ->select('vendor_id', 'closing_balance')
            ->where('created_at', '<=', $endDT)
            ->orderBy('id', 'desc')
            ->get()
            ->unique('vendor_id')
            ->keyBy('vendor_id');

        $totalPayables = 0;
        $vendorsDetailed = [];
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
                $vendorsDetailed[] = [
                    'id'         => $vnd->id,
                    'name'       => $vnd->name,
                    'phone'      => $vnd->phone ?? '-',
                    'balance'    => round($bal, 2),
                    'ledger_url' => route('report.vendor.ledger') . '?vendor_id=' . $vnd->id,
                ];
            }
        }
        usort($vendorsDetailed, fn($a, $b) => $b['balance'] <=> $a['balance']);

        $claimsList = DB::table('distributor_claims')
            ->leftJoin('distributors', 'distributors.id', '=', 'distributor_claims.distributor_id')
            ->where('distributor_claims.status', 'pending')
            ->select(
                'distributor_claims.id',
                'distributor_claims.claim_no',
                'distributor_claims.claim_date',
                'distributor_claims.claim_type',
                'distributor_claims.total_amount',
                'distributors.name as distributor_name'
            )
            ->get();

        $claimsPayable = 0;
        $claimsDetailed = [];
        foreach ($claimsList as $clm) {
            $amt = (float)$clm->total_amount;
            $claimsPayable += $amt;
            $typeLabel = match($clm->claim_type) {
                'trade_scheme' => 'Trade Scheme / Discount',
                'damage_stock' => 'Damaged Goods',
                'freight'      => 'Freight & Carriage',
                default        => ucfirst(str_replace('_', ' ', $clm->claim_type ?? 'Claim')),
            };
            $claimsDetailed[] = [
                'id'               => $clm->id,
                'claim_no'         => $clm->claim_no,
                'distributor_name' => $clm->distributor_name ?? 'Distributor',
                'claim_date'       => $clm->claim_date ? date('d M Y', strtotime($clm->claim_date)) : '-',
                'type_label'       => $typeLabel,
                'amount'           => round($amt, 2),
            ];
        }

        $totalLiabilities = max(0, $totalPayables + $distPayableAdvance + $claimsPayable);

        // 7. NET WORTH = TOTAL ASSETS - TOTAL LIABILITIES
        $netWorth = $totalAssets - $totalLiabilities;

        $netInvestedCapital = $cumulativeEquity['net_invested_capital'] ?? 0;
        $retainedEarnings = $netWorth - $netInvestedCapital;

        return [
            'cash_and_bank'      => $cashAndBank,
            'cash_in_hand'       => $totalCash,
            'bank_balances'      => $totalBank,
            'cash_details'       => [
                'counter_cash' => round($counterCashBalance, 2),
                'accounts'     => $cashAccountsDetailed,
            ],
            'bank_details'       => $bankAccountsDetailed,
            'inventory'          => [
                'finished_goods' => round($finishedGoodsValue, 2),
                'raw_materials'  => round($rawMaterialValue, 2),
                'total'          => round($totalInventoryValue, 2),
                'finished_items' => $finishedItems,
                'raw_items'      => $rawItems,
            ],
            'receivables'        => [
                'direct_customers'  => round($custReceivable, 2),
                'distributors'      => round($distReceivable, 2),
                'market_shops'      => round($marketReceivable, 2),
                'total'             => round($totalReceivable, 2),
                'customers_list'    => $customersDetailed,
                'distributors_list' => $distributorsDetailed,
                'market_shops_list' => $retailShopsDetailed,
            ],
            'fixed_assets'       => round($totalFixedAssets, 2),
            'fixed_assets_list'  => $assetsDetailed,
            'current_assets'     => round($currentAssets, 2),
            'total_assets'       => round($totalAssets, 2),
            'payables'           => [
                'vendors'              => round($totalPayables, 2),
                'distributor_advances' => round($distPayableAdvance, 2),
                'claims'               => round($claimsPayable, 2),
                'total'                => round($totalLiabilities, 2),
                'vendors_list'         => $vendorsDetailed,
                'advances_list'        => $distAdvancesDetailed,
                'claims_list'          => $claimsDetailed,
            ],
            'total_liabilities'  => round($totalLiabilities, 2),
            'net_worth'          => round($netWorth, 2),
            'equity_breakdown'   => [
                'net_invested_capital' => round($netInvestedCapital, 2),
                'retained_earnings'    => round($retainedEarnings, 2),
            ],
        ];
    }

    private function buildDeltaItem(float $current, float $baseline, string $type = 'currency', bool $invertDirection = false): array
    {
        $diff = $current - $baseline;
        $pct  = ($baseline != 0) ? round(($diff / abs($baseline)) * 100, 1) : ($current != 0 ? 100.0 : 0.0);

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

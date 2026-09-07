<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usertype = Auth::user()->usertype;
        $userId = Auth::id();

        if ($usertype == 'user') {
            return view('user_panel.dashboard', compact('userId'));
        } elseif ($usertype == 'admin') {
            $data = $this->getReportData(now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d'));
            return view('admin_panel.dashboard', $data);
        } else {
            return redirect()->back()->with('error', 'Unauthorized access');
        }
    }

    private function getReportData($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?: now()->startOfMonth()->format('Y-m-d');
        $endDate   = $endDate   ?: now()->endOfMonth()->format('Y-m-d');

        $categoryCount = DB::table('categories')->count();
        $subcategoryCount = DB::table('subcategories')->count();
        $productCount = DB::table('products')->count();
        $customerscount = DB::table('customers')->count();

        $totalPurchases = 0;
        $totalPurchaseReturns = 0;
        $totalSales = 0;
        $totalSalesReturns = 0;
        $totalExpenses = 0;
        $netSales = 0;
        $netPurchases = 0;
        $grossProfit = 0;

        $salesChartStats = [
            'daily' => ['categories' => [], 'series' => []],
            'weekly' => ['categories' => [], 'series' => []],
            'monthly' => ['categories' => [], 'series' => []]
        ];
        $purchaseChartStats = [
            'daily' => ['categories' => [], 'series' => []],
            'weekly' => ['categories' => [], 'series' => []],
            'monthly' => ['categories' => [], 'series' => []]
        ];

        $salesData = [];
        $purchaseData = [];
        $labels = [];

        $startObj = Carbon::parse($startDate)->startOfMonth();
        $endObj   = Carbon::parse($endDate)->endOfMonth();

        $start = $startObj->format('Y-m-d 00:00:00');
        $end   = $endObj->format('Y-m-d 23:59:59');

            $purchasesQuery = DB::table('purchases')
                ->whereBetween('purchase_date', [$startObj->format('Y-m-d'), $endObj->format('Y-m-d')]);
            $purchaseReturnsQuery = DB::table('purchase_returns')
                ->whereBetween('return_date', [$startObj->format('Y-m-d'), $endObj->format('Y-m-d')]);

            $salesQuery = DB::table('sales')
                ->leftJoin('customers', 'sales.customer', '=', 'customers.id')
                ->whereBetween('sales.created_at', [$start, $end])
                ->where(function ($q) {
                    $q->where('sales.customer', 'Walk-in Customer')
                        ->orWhere('customers.customer_category', 'Walking Customer')
                        ->orWhere('customers.customer_category', 'Retailer');
                });

            $salesReturnsQuery = DB::table('sales_returns')
                ->leftJoin('customers', 'sales_returns.customer', '=', 'customers.id')
                ->whereBetween('sales_returns.created_at', [$start, $end])
                ->where(function ($q) {
                    $q->where('sales_returns.customer', 'Walk-in Customer')
                        ->orWhere('customers.customer_category', 'Walking Customer')
                        ->orWhere('customers.customer_category', 'Retailer');
                });

            if (!is_all_branches()) {
                $purchasesQuery->where('purchases.branch_id', active_branch_id());
                $purchaseReturnsQuery->where('purchase_returns.branch_id', active_branch_id());
                $salesQuery->where('sales.branch_id', active_branch_id());
                $salesReturnsQuery->where('sales_returns.branch_id', active_branch_id());
            }

            $totalPurchases = $purchasesQuery->sum('net_amount');
            $totalPurchaseReturns = $purchaseReturnsQuery->sum('net_amount');
            $totalSales = $salesQuery->sum('sales.total_net');
            $totalSalesReturns = $salesReturnsQuery->sum('sales_returns.total_net');

            $chartStart = $startObj;
            $chartEnd   = $endObj;
            $diffInDays = $chartStart->diffInDays($chartEnd);

            $granularity = 'daily';
            if ($diffInDays > 90) {
                $granularity = 'monthly';
            } elseif ($diffInDays > 14) {
                $granularity = 'weekly';
            }

            $getSalesData = function($selectRaw) use ($start, $end) {
                $q = DB::table('sales')
                    ->leftJoin('customers', 'sales.customer', '=', 'customers.id')
                    ->whereBetween('sales.created_at', [$start, $end])
                    ->where(function ($sub) {
                        $sub->where('sales.customer', 'Walk-in Customer')
                            ->orWhere('customers.customer_category', 'Walking Customer')
                            ->orWhere('customers.customer_category', 'Retailer');
                    });
                if (!is_all_branches()) {
                    $q->where('sales.branch_id', active_branch_id());
                }
                return $q->select(DB::raw("$selectRaw as label_key"), DB::raw('SUM(sales.total_net) as total'))
                    ->groupBy('label_key')->orderBy('label_key')->pluck('total', 'label_key');
            };

            $getPurchaseData = function($selectRaw) use ($startObj, $endObj) {
                 $q = DB::table('purchases')
                    ->whereBetween('purchase_date', [$startObj->format('Y-m-d'), $endObj->format('Y-m-d')]);
                 if (!is_all_branches()) {
                     $q->where('purchases.branch_id', active_branch_id());
                 }
                 return $q->select(DB::raw("$selectRaw as label_key"), DB::raw('SUM(net_amount) as total'))
                    ->groupBy('label_key')->orderBy('label_key')->pluck('total', 'label_key');
            };

            if ($granularity === 'daily') {
                $period = \Carbon\CarbonPeriod::create($chartStart, $chartEnd);
                $salesMap = $getSalesData('DATE(sales.created_at)');
                $purchaseMap = $getPurchaseData('DATE(purchase_date)');
                foreach ($period as $dt) {
                    $key = $dt->format('Y-m-d');
                    $labels[] = $dt->format('d M (D)');
                    $salesData[] = $salesMap[$key] ?? 0;
                    $purchaseData[] = $purchaseMap[$key] ?? 0;
                }
            } elseif ($granularity === 'weekly') {
                $salesMap = $getSalesData('YEARWEEK(sales.created_at, 1)');
                $purchaseMap = $getPurchaseData('YEARWEEK(purchase_date, 1)');
                $current = $chartStart->copy()->startOfWeek();
                $endWeek = $chartEnd->copy()->endOfWeek();
                while ($current <= $endWeek) {
                    $dbKey = $current->format('oW');
                    $labels[] = "Week " . $current->weekOfYear . " - " . $current->format('M Y');
                    $salesData[] = $salesMap[$dbKey] ?? 0;
                    $purchaseData[] = $purchaseMap[$dbKey] ?? 0;
                    $current->addWeek();
                }
            } else {
                $salesMap = $getSalesData("DATE_FORMAT(sales.created_at, '%Y-%m')");
                $purchaseMap = $getPurchaseData("DATE_FORMAT(purchase_date, '%Y-%m')");
                $current = $chartStart->copy()->startOfMonth();
                $endMonth = $chartEnd->copy()->endOfMonth();
                while ($current <= $endMonth) {
                    $key = $current->format('Y-m');
                    $labels[] = $current->format('F Y');
                    $salesData[] = $salesMap[$key] ?? 0;
                    $purchaseData[] = $purchaseMap[$key] ?? 0;
                    $current->addMonth();
                }
            }

            $salesChartStats['daily'] = ['categories' => $labels, 'series' => [['name' => 'Sales', 'data' => $salesData]]];
            $purchaseChartStats['daily'] = ['categories' => $labels, 'series' => [['name' => 'Purchases', 'data' => $purchaseData]]];

            // Weekly aggregation for dropdown
            $weeklyLabels = [];
            $weeklySales = [];
            $weeklyPurchases = [];
            $wSalesMap = $getSalesData('YEARWEEK(sales.created_at, 1)');
            $wPurchaseMap = $getPurchaseData('YEARWEEK(purchase_date, 1)');
            $wCurrent = $chartStart->copy()->startOfWeek();
            $wEndWeek = $chartEnd->copy()->endOfWeek();
            while ($wCurrent <= $wEndWeek) {
                $wKey = $wCurrent->format('oW');
                $weeklyLabels[] = "Week " . $wCurrent->weekOfYear . " - " . $wCurrent->format('M Y');
                $weeklySales[] = $wSalesMap[$wKey] ?? 0;
                $weeklyPurchases[] = $wPurchaseMap[$wKey] ?? 0;
                $wCurrent->addWeek();
            }
            $salesChartStats['weekly'] = ['categories' => $weeklyLabels, 'series' => [['name' => 'Sales', 'data' => $weeklySales]]];
            $purchaseChartStats['weekly'] = ['categories' => $weeklyLabels, 'series' => [['name' => 'Purchases', 'data' => $weeklyPurchases]]];

            // Monthly aggregation for dropdown
            $monthlyLabels = [];
            $monthlySales = [];
            $monthlyPurchases = [];
            $mSalesMap = $getSalesData("DATE_FORMAT(sales.created_at, '%Y-%m')");
            $mPurchaseMap = $getPurchaseData("DATE_FORMAT(purchase_date, '%Y-%m')");
            $mCurrent = $chartStart->copy()->startOfMonth();
            $mEndMonth = $chartEnd->copy()->endOfMonth();
            while ($mCurrent <= $mEndMonth) {
                $mKey = $mCurrent->format('Y-m');
                $monthlyLabels[] = $mCurrent->format('F Y');
                $monthlySales[] = $mSalesMap[$mKey] ?? 0;
                $monthlyPurchases[] = $mPurchaseMap[$mKey] ?? 0;
                $mCurrent->addMonth();
            }
            $salesChartStats['monthly'] = ['categories' => $monthlyLabels, 'series' => [['name' => 'Sales', 'data' => $monthlySales]]];
            $purchaseChartStats['monthly'] = ['categories' => $monthlyLabels, 'series' => [['name' => 'Purchases', 'data' => $monthlyPurchases]]];

        $categoryProductData = DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->select('categories.id', 'categories.name as category_name', DB::raw('COUNT(products.id) as total_products'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_products')
            ->get();

        $categoryProductChart = [
            'categories'   => $categoryProductData->pluck('category_name'),
            'category_ids' => $categoryProductData->pluck('id'),
            'series' => [['name' => 'Total Products', 'data' => $categoryProductData->pluck('total_products')]]
        ];

        $lowStockQuery = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('stocks', function($join) {
                $join->on('products.id', '=', 'stocks.product_id');
                if (!is_all_branches()) {
                    $join->where('stocks.branch_id', '=', active_branch_id());
                }
            })
            ->select(
                'products.id',
                'products.item_code',
                'products.item_name',
                'categories.name as category_name',
                DB::raw('COALESCE(SUM(stocks.qty), 0) as qty'),
                DB::raw('COALESCE(products.alert_quantity, 0) as alert_quantity')
            )
            ->groupBy('products.id', 'products.item_code', 'products.item_name', 'categories.name', 'products.alert_quantity')
            ->havingRaw('COALESCE(SUM(stocks.qty), 0) <= COALESCE(products.alert_quantity, 0)');

        $lowStockItems = $lowStockQuery->orderBy('qty', 'asc')->limit(10)->get()->map(function($item) {
            $item->deficit = max(0, $item->alert_quantity - $item->qty);
            $item->status = $item->qty <= 0 ? 'Out of Stock' : 'Critical Low';
            return $item;
        });

        $lowStockChart = [
            'categories' => $lowStockItems->pluck('item_name'),
            'series' => [
                ['name' => 'Current Stock', 'data' => $lowStockItems->pluck('qty')],
                ['name' => 'Alert Level', 'data' => $lowStockItems->pluck('alert_quantity')],
            ]
        ];

        $categorySubData = DB::table('categories')
            ->leftJoin('subcategories', 'categories.id', '=', 'subcategories.category_id')
            ->leftJoin('products', 'subcategories.id', '=', 'products.sub_category_id')
            ->select('categories.name as category_name', DB::raw('COUNT(DISTINCT subcategories.id) as sub_count'), DB::raw('COUNT(DISTINCT products.id) as product_count'))
            ->groupBy('categories.name')
            ->get();
        $categorySubChart = [
            'categories' => $categorySubData->pluck('category_name'),
            'series' => [
                ['name' => 'Subcategories', 'data' => $categorySubData->pluck('sub_count')],
                ['name' => 'Products', 'data' => $categorySubData->pluck('product_count')],
            ]
        ];

        $expenseChartData = [];
        // Total Expenses
        $totalExpensesQuery = DB::table('expense_vouchers')
           ->whereBetween('expense_vouchers.created_at', [$startObj->format('Y-m-d 00:00:00'), $endObj->format('Y-m-d 23:59:59')]);
        if (!is_all_branches()) {
            $totalExpensesQuery->where('expense_vouchers.branch_id', active_branch_id());
        }
        $totalExpenses = $totalExpensesQuery->sum('total_amount');

        $expenseRawQuery = DB::table('expense_vouchers')
           ->join('accounts', 'expense_vouchers.party_id', '=', 'accounts.id')
           ->join('account_heads', 'accounts.head_id', '=', 'account_heads.id')
           ->whereBetween('expense_vouchers.created_at', [$startObj->format('Y-m-d 00:00:00'), $endObj->format('Y-m-d 23:59:59')]);
        if (!is_all_branches()) {
            $expenseRawQuery->where('expense_vouchers.branch_id', active_branch_id());
        }
        $expenseRaw = $expenseRawQuery
           ->select('account_heads.id as head_id', 'account_heads.name as head_name', 'accounts.title as account_name', DB::raw('SUM(expense_vouchers.total_amount) as total_expense'))
           ->groupBy('account_heads.id', 'account_heads.name', 'accounts.title')
           ->get()
           ->groupBy('head_id');

        foreach ($expenseRaw as $headId => $rows) {
           $expenseChartData[$headId] = [
               'head_name' => $rows->first()->head_name,
               'categories' => $rows->pluck('account_name'),
               'series' => [['name' => 'Expense', 'data' => $rows->pluck('total_expense')]]
           ];
        }

        // Profit/Loss Calculation
        $netSales = $totalSales - $totalSalesReturns;
        $netPurchases = $totalPurchases - $totalPurchaseReturns;
        $grossProfit = $netSales - $netPurchases - $totalExpenses;

        // Additional Metrics for New Dashboard Design
        $suppliersCount = DB::table('vendors')->count();
        $employeesCount = DB::table('users')->count();

        // Growth rates calculation vs previous month
        $prevStart = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d 00:00:00');
        $prevEnd = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d 23:59:59');

        $prevSales = DB::table('sales')->whereBetween('created_at', [$prevStart, $prevEnd])->sum('total_net');
        $prevPurchases = DB::table('purchases')->whereBetween('created_at', [$prevStart, $prevEnd])->sum('net_amount');
        $prevExpenses = DB::table('expense_vouchers')->whereBetween('created_at', [$prevStart, $prevEnd])->sum('total_amount');
        $prevGrossProfit = ($prevSales) - ($prevPurchases) - ($prevExpenses);

        $calcGrowth = function($current, $previous) {
            if ($previous > 0) {
                return round((($current - $previous) / $previous) * 100, 1);
            }
            return $current > 0 ? 100 : 0;
        };

        $salesGrowth = $calcGrowth($totalSales, $prevSales);
        $purchaseGrowth = $calcGrowth($totalPurchases, $prevPurchases);
        $grossProfitGrowth = $calcGrowth($grossProfit, $prevGrossProfit);
        $expenseGrowth = $calcGrowth($totalExpenses, $prevExpenses);
        $netProfit = $grossProfit;
        $netProfitGrowth = $grossProfitGrowth;

        $prevCustomers = DB::table('customers')->where('created_at', '<', Carbon::now()->startOfMonth())->count();
        $customersGrowth = $calcGrowth($customerscount, $prevCustomers);

        $prevSuppliers = DB::table('vendors')->where('created_at', '<', Carbon::now()->startOfMonth())->count();
        $suppliersGrowth = $calcGrowth($suppliersCount, $prevSuppliers);

        $prevProducts = DB::table('products')->where('created_at', '<', Carbon::now()->startOfMonth())->count();
        $productsGrowth = $calcGrowth($productCount, $prevProducts);

        $prevEmployees = DB::table('users')->where('created_at', '<', Carbon::now()->startOfMonth())->count();
        $employeesGrowth = $calcGrowth($employeesCount, $prevEmployees);

        // Top Products List
        $salesForMonth = DB::table('sales')
            ->whereBetween('created_at', [$start, $end])
            ->when(!is_all_branches(), function($q) {
                $q->where('branch_id', active_branch_id());
            })
            ->select('product', 'qty', 'per_total')
            ->get();

        $topProductAgg = [];
        $allProductsCategoryMap = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.id', 'products.item_name', 'categories.name as category_name')
            ->get()
            ->keyBy('id');

        $categorySalesTotals = [];
        foreach ($salesForMonth as $s) {
            $pIds = explode(',', $s->product);
            $qtys = explode(',', $s->qty);
            $totals = explode(',', $s->per_total);
            foreach ($pIds as $idx => $pid) {
                $pid = trim($pid);
                if (!$pid) continue;
                $q = (float)($qtys[$idx] ?? 0);
                $t = (float)($totals[$idx] ?? 0);

                if (!isset($topProductAgg[$pid])) {
                    $topProductAgg[$pid] = ['qty' => 0, 'total' => 0];
                }
                $topProductAgg[$pid]['qty'] += $q;
                $topProductAgg[$pid]['total'] += $t;

                $catName = $allProductsCategoryMap[$pid]->category_name ?? 'General';
                if (!isset($categorySalesTotals[$catName])) {
                    $categorySalesTotals[$catName] = 0;
                }
                $categorySalesTotals[$catName] += $t;
            }
        }

        uasort($topProductAgg, function($a, $b) {
            return $b['qty'] <=> $a['qty'];
        });

        $topProducts = [];
        $rank = 1;
        foreach (array_slice($topProductAgg, 0, 6, true) as $pid => $data) {
            if (isset($allProductsCategoryMap[$pid])) {
                $topProducts[] = [
                    'rank' => $rank++,
                    'name' => strtoupper($allProductsCategoryMap[$pid]->item_name),
                    'units_sold' => (int)$data['qty'],
                    'revenue' => $data['total']
                ];
            }
        }

        // If no sales in current range, populate top active products from database catalog
        if (empty($topProducts)) {
            $catalogProds = DB::table('products')->limit(6)->get();
            foreach ($catalogProds as $idx => $sp) {
                $topProducts[] = [
                    'rank' => $idx + 1,
                    'name' => strtoupper($sp->item_name),
                    'units_sold' => 0,
                    'revenue' => (float)($sp->price ?: $sp->wholesale_price)
                ];
            }
        }

        // Category Donut Breakdown Data (Top 10 Only)
        $catDonutList = [];
        $totalCategorySales = array_sum($categorySalesTotals);
        $colorsList = ['#0f766e', '#14b8a6', '#334155', '#0d9488', '#475569', '#059669', '#64748b', '#10b981', '#1e293b', '#0f172a'];
        $colorIdx = 0;

        if ($totalCategorySales > 0) {
            arsort($categorySalesTotals);
            foreach ($categorySalesTotals as $cName => $cAmt) {
                $pct = round(($cAmt / $totalCategorySales) * 100);
                $catDonutList[] = [
                    'name' => $cName,
                    'percentage' => $pct,
                    'amount' => $cAmt,
                    'color' => $colorsList[$colorIdx % count($colorsList)]
                ];
                $colorIdx++;
            }
        } else {
            // Group products by category if no sales
            $categoriesInDb = DB::table('categories')
                ->leftJoin('products', 'categories.id', '=', 'products.category_id')
                ->select('categories.name', DB::raw('COUNT(products.id) as p_count'))
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('p_count')
                ->get();
            
            $totalPCount = $categoriesInDb->sum('p_count');
            foreach ($categoriesInDb as $cRow) {
                $pct = $totalPCount > 0 ? round(($cRow->p_count / $totalPCount) * 100) : 0;
                $catDonutList[] = [
                    'name' => $cRow->name,
                    'percentage' => $pct,
                    'amount' => $cRow->p_count,
                    'color' => $colorsList[$colorIdx % count($colorsList)]
                ];
                $colorIdx++;
            }
        }

        // Limit strictly to Top 10 categories
        $catDonutList = array_slice($catDonutList, 0, 10);

        // Cash Flow Overview Data
        $todayIn = DB::table('sales')->whereDate('created_at', date('Y-m-d'))->sum('total_net');
        $todayOut = DB::table('expense_vouchers')->whereDate('created_at', date('Y-m-d'))->sum('total_amount');
        $totalIn = $totalSales;
        $totalOut = $totalPurchases + $totalExpenses;

        // Financial Position Metrics
        $customerReceivables = DB::table('customer_ledgers')
            ->whereIn('id', function($q) {
                $q->select(DB::raw('MAX(id)'))->from('customer_ledgers')->groupBy('customer_id');
            })
            ->sum('closing_balance');

        $vendorPayables = DB::table('vendor_ledgers')
            ->whereIn('id', function($q) {
                $q->select(DB::raw('MAX(id)'))->from('vendor_ledgers')->groupBy('vendor_id');
            })
            ->sum('closing_balance');

        $stockInventoryValue = DB::table('stocks')
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->sum(DB::raw('stocks.qty * COALESCE(NULLIF(products.price, 0), products.wholesale_price, 0)'));

        $cashInHand = DB::table('accounts')
            ->where('title', 'like', '%Cash%')
            ->sum('opening_balance');

        $easyPaisaBalance = DB::table('accounts')
            ->where(function($q) {
                $q->where('title', 'like', '%Easy%')
                  ->orWhere('title', 'like', '%Jazz%');
            })
            ->sum('opening_balance');

        $meezanBalance = DB::table('accounts')
            ->where(function($q) {
                $q->where('title', 'like', '%Bank%')
                  ->orWhere('title', 'like', '%Meezan%');
            })
            ->sum('opening_balance');

        $cashBalance = DB::table('accounts')->sum('opening_balance');

        // Recent Activities List
        $recentSales = DB::table('sales')
            ->select('invoice_no as title', 'total_net as amount', 'created_at', DB::raw("'sale' as type"))
            ->orderBy('id', 'desc')
            ->limit(4)
            ->get();

        $recentPurchases = DB::table('purchases')
            ->select('invoice_no as title', 'net_amount as amount', 'created_at', DB::raw("'purchase' as type"))
            ->orderBy('id', 'desc')
            ->limit(4)
            ->get();

        $recentActivities = [];
        foreach ($recentSales->concat($recentPurchases)->sortByDesc('created_at')->take(4) as $actItem) {
            $recentActivities[] = [
                'title' => ($actItem->type == 'sale' ? 'Sale Invoice #' : 'Purchase Bill #') . $actItem->title,
                'category' => $actItem->type == 'sale' ? 'Sale Transaction' : 'Procurement',
                'time_ago' => Carbon::parse($actItem->created_at)->diffForHumans(),
                'amount' => (float)$actItem->amount,
                'type' => $actItem->type
            ];
        }

        // Branch Wise Sales Breakdown & History
        $allBranchesList = DB::table('branches')->get();
        if ($allBranchesList->isEmpty()) {
            $allBranchesList = collect([
                (object)['id' => 1, 'name' => 'Main Branch', 'address' => 'Head Office', 'number' => '00000000000']
            ]);
        }

        $branchSalesPerformance = [];
        $isSuperAdminUser = auth()->check() && (auth()->user()->email === 'admin@admin.com' || auth()->user()->hasRole('Super Admin'));
        $activeBranchId = active_branch_id();
        $isAll = is_all_branches();

        foreach ($allBranchesList as $br) {
            // For restricted branch staff, only include their assigned branch
            if (!$isSuperAdminUser && !empty(auth()->user()->branch_id) && auth()->user()->branch_id != $br->id) {
                continue;
            }

            $brSalesQuery = DB::table('sales')
                ->whereBetween('created_at', [$start, $end])
                ->where(function($q) use ($br) {
                    $q->where('branch_id', $br->id);
                    if ($br->id == 1) {
                        $q->orWhereNull('branch_id');
                    }
                });

            $brSalesCount = $brSalesQuery->count();
            $brTotalNet = (float) $brSalesQuery->sum('total_net');

            $brReturnsQuery = DB::table('sales_returns')
                ->whereBetween('created_at', [$start, $end])
                ->where(function($q) use ($br) {
                    $q->where('branch_id', $br->id);
                    if ($br->id == 1) {
                        $q->orWhereNull('branch_id');
                    }
                });
            $brTotalReturns = (float) $brReturnsQuery->sum('total_net');
            $brNetSales = $brTotalNet - $brTotalReturns;

            $branchSalesPerformance[] = [
                'id' => $br->id,
                'name' => $br->name,
                'address' => $br->address ?? 'Main Location',
                'number' => $br->number ?? 'N/A',
                'total_sales' => $brTotalNet,
                'total_returns' => $brTotalReturns,
                'net_sales' => $brNetSales,
                'invoice_count' => $brSalesCount,
                'share_pct' => $totalSales > 0 ? round(($brTotalNet / $totalSales) * 100, 1) : 0,
                'is_active' => (!$isAll && $activeBranchId == $br->id)
            ];
        }

        return compact(
            'categoryCount', 'subcategoryCount', 'productCount', 'customerscount',
            'suppliersCount', 'employeesCount',
            'totalPurchases', 'totalPurchaseReturns', 'totalSales', 'totalSalesReturns',
            'totalExpenses', 'netSales', 'netPurchases', 'grossProfit', 'netProfit', 'cashBalance',
            'salesGrowth', 'purchaseGrowth', 'grossProfitGrowth', 'expenseGrowth', 'netProfitGrowth',
            'customersGrowth', 'suppliersGrowth', 'productsGrowth', 'employeesGrowth',
            'salesChartStats', 'purchaseChartStats',
            'categoryProductChart', 'lowStockChart', 'categorySubChart', 'expenseChartData',
            'labels', 'salesData', 'purchaseData', 'topProducts', 'catDonutList',
            'todayIn', 'todayOut', 'totalIn', 'totalOut',
            'customerReceivables', 'vendorPayables', 'stockInventoryValue',
            'cashInHand', 'easyPaisaBalance', 'meezanBalance', 'recentActivities',
            'branchSalesPerformance', 'lowStockItems',
            'startDate', 'endDate', 'start', 'end'
        );
    }

    public function System_Reports(Request $request)
    {
        $data = $this->getReportData($request->start_date, $request->end_date);
        return view('admin_panel.system_reports', $data);
    }

    public function System_Reports_PDF(Request $request)
    {
        $data = $this->getReportData($request->start_date, $request->end_date);
        $data['startDate'] = $request->start_date;
        $data['endDate'] = $request->end_date;
        $pdf = Pdf::loadView('admin_panel.system_reports_pdf', $data);
        return $pdf->download('System_Reports_' . date('Y-m-d') . '.pdf');
    }

    public function System_Reports_PDF_BW(Request $request)
    {
        $data = $this->getReportData($request->start_date, $request->end_date);
        $data['startDate'] = $request->start_date;
        $data['endDate'] = $request->end_date;
        $pdf = Pdf::loadView('admin_panel.system_reports_pdf_bw', $data);
        return $pdf->download('System_Reports_BW_' . date('Y-m-d') . '.pdf');
    }

    public function categoryProducts(Request $request, $id)
    {
        $search = $request->search;

        $products = DB::table('products')
            ->leftJoin('stocks', 'products.id', '=', 'stocks.product_id')
            ->select(
                'products.id',
                'products.item_name',
                DB::raw('COALESCE(SUM(stocks.qty),0) as stock')
            )
            ->where('products.category_id', $id)
            ->when($search, function ($q) use ($search) {
                $q->where('products.item_name', 'like', "%{$search}%");
            })
            ->groupBy('products.id', 'products.item_name')
            ->orderByDesc('stock')
            ->paginate(100);

        return response()->json($products);
    }
}

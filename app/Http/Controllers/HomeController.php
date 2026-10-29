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
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user     = Auth::user();
        $usertype = $user->usertype;

        // Redirect Order Booker directly to dedicated booking screen
        if ($usertype == 'order_booker' || ($user->distributor_id && !$user->distributor && !$user->hasRole('Super Admin') && $user->email !== 'admin@admin.com')) {
            return redirect()->route('distributor.booker.create');
        }

        // Redirect Distributor to dedicated portal
        if ($usertype == 'distributor' || ($user->distributor && !$user->hasRole('Super Admin') && $user->email !== 'admin@admin.com')) {
            return redirect()->route('distributor.dashboard');
        }

        // ── Smart Permission-Aware Redirect ───────────────────────────────────
        // If the user lacks Dashboard permission, forward them to the first
        // module they ARE allowed to access instead of showing a 403 error.
        $canDashboard = $user->email === 'admin@admin.com'
            || $user->hasRole(['Super Admin', 'super-admin'])
            || $user->can('Dashboard');

        if (!$canDashboard) {
            // Priority-ordered fallback routes: [permission => route_name]
            $fallbacks = [
                'Sales'              => 'sale.screen',
                'Sales'              => 'sale.index',
                'Purchase'           => 'Purchase.home',
                'Products'           => 'product',
                'Customer'           => 'customers.index',
                'Vendor'             => 'vendors',
                'Production'         => 'production.index',
                'Raw Materials'      => 'raw-materials.index',
                'Stock Transfer'     => 'stock_transfers.index',
                'Item Stock Report'  => 'report.item_stock',
                'Sale Report'        => 'report.sale',
                'Purchase Report'    => 'report.purchase',
                'Customer Ledger'    => 'report.customer.ledger',
                'Vendor Ledger'      => 'report.vendor.ledger',
                'Char Of Accounts'   => 'view_all',
                'Payment Voucher'    => 'all-Payment-vochers',
                'Receipts Voucher'   => 'all-recepit-vochers',
                'Expense Voucher'    => 'all-expense-vochers',
                'Cashbook'           => 'cashbook',
                'Category'           => 'Category.home',
                'Brands'             => 'Brand.home',
                'Roles'              => 'roles.index',
                'Users'              => 'users.index',
                'Branch'             => 'branch.index',
                'Settings'           => 'settings.index',
            ];

            foreach ($fallbacks as $permission => $routeName) {
                if ($user->can($permission) && \Illuminate\Support\Facades\Route::has($routeName)) {
                    return redirect()->route($routeName);
                }
            }

            // Absolute last resort — show a friendly "no access" page
            abort(403, 'You do not have access to any module. Please contact your administrator.');
        }
        // ─────────────────────────────────────────────────────────────────────

        $startDate = $request->start_date ?: now()->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date   ?: now()->endOfMonth()->format('Y-m-d');

        $data = $this->getReportData($startDate, $endDate);
        return view('admin_panel.dashboard', $data);
    }

    private function getReportData($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?: now()->startOfMonth()->format('Y-m-d');
        $endDate   = $endDate   ?: now()->endOfMonth()->format('Y-m-d');

        $categoryCount = DB::table('categories')->count();
        $subcategoryCount = DB::table('subcategories')->count();

        $productCountQuery = DB::table('products');
        $customersQuery = DB::table('customers');
        if (!is_all_branches()) {
            $productCountQuery->whereIn('id', function($q) {
                $q->select('product_id')->from('stocks')
                  ->where('branch_id', active_branch_id())
                  ->whereNull('warehouse_id');
            });
            $customersQuery->where('branch_id', active_branch_id());
        }
        $productCount = $productCountQuery->count();
        $customerscount = $customersQuery->count();

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
            $salesCount = $salesQuery->count();

            $rawMaterialPurchasesQuery = DB::table('raw_material_purchases')
                ->whereBetween('date', [$startObj->format('Y-m-d'), $endObj->format('Y-m-d')]);
            if (!is_all_branches()) {
                $rawMaterialPurchasesQuery->where('branch_id', active_branch_id());
            }
            $totalRawMaterialPurchases = (float) $rawMaterialPurchasesQuery->sum('total_cost');

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
        $suppliersQuery = DB::table('vendors');
        $employeesQuery = DB::table('users');
        if (!is_all_branches()) {
            $suppliersQuery->whereIn('id', function($q) {
                $q->select('vendor_id')->from('purchases')->where('branch_id', active_branch_id())
                  ->union(DB::table('raw_material_purchases')->select('vendor_id')->where('branch_id', active_branch_id()));
            });
            $employeesQuery->where('branch_id', active_branch_id());
        }
        $suppliersCount = $suppliersQuery->count();
        $employeesCount = $employeesQuery->count();

        // Growth rates calculation vs previous month
        $prevStart = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d 00:00:00');
        $prevEnd = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d 23:59:59');

        $prevSalesQ = DB::table('sales')->whereBetween('created_at', [$prevStart, $prevEnd]);
        $prevPurchasesQ = DB::table('purchases')->whereBetween('created_at', [$prevStart, $prevEnd]);
        $prevExpensesQ = DB::table('expense_vouchers')->whereBetween('created_at', [$prevStart, $prevEnd]);

        if (!is_all_branches()) {
            $prevSalesQ->where('branch_id', active_branch_id());
            $prevPurchasesQ->where('branch_id', active_branch_id());
            $prevExpensesQ->where('branch_id', active_branch_id());
        }

        $prevSales = $prevSalesQ->sum('total_net');
        $prevPurchases = $prevPurchasesQ->sum('net_amount');
        $prevExpenses = $prevExpensesQ->sum('total_amount');
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

        $prevRawPurchasesQ = DB::table('raw_material_purchases')
            ->whereBetween('date', [
                Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'),
                Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d')
            ]);
        if (!is_all_branches()) {
            $prevRawPurchasesQ->where('branch_id', active_branch_id());
        }
        $prevRawPurchases = (float) $prevRawPurchasesQ->sum('total_cost');
        $rawMaterialGrowth = $calcGrowth($totalRawMaterialPurchases, $prevRawPurchases);

        $prevCustomersQ = DB::table('customers')->where('created_at', '<', Carbon::now()->startOfMonth());
        $prevSuppliersQ = DB::table('vendors')->where('created_at', '<', Carbon::now()->startOfMonth());
        $prevProductsQ = DB::table('products')->where('created_at', '<', Carbon::now()->startOfMonth());
        $prevEmployeesQ = DB::table('users')->where('created_at', '<', Carbon::now()->startOfMonth());

        if (!is_all_branches()) {
            $prevCustomersQ->where('branch_id', active_branch_id());
            $prevSuppliersQ->whereIn('id', function($q) {
                $q->select('vendor_id')->from('purchases')->where('branch_id', active_branch_id())
                  ->union(DB::table('raw_material_purchases')->select('vendor_id')->where('branch_id', active_branch_id()));
            });
            $prevProductsQ->whereIn('id', function($q) {
                $q->select('product_id')->from('stocks')
                  ->where('branch_id', active_branch_id())
                  ->whereNull('warehouse_id');
            });
            $prevEmployeesQ->where('branch_id', active_branch_id());
        }

        $prevCustomers = $prevCustomersQ->count();
        $customersGrowth = $calcGrowth($customerscount, $prevCustomers);

        $prevSuppliers = $prevSuppliersQ->count();
        $suppliersGrowth = $calcGrowth($suppliersCount, $prevSuppliers);

        $prevProducts = $prevProductsQ->count();
        $productsGrowth = $calcGrowth($productCount, $prevProducts);

        $prevEmployees = $prevEmployeesQ->count();
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
            ->select('products.id', 'products.item_code', 'products.item_name', 'categories.name as category_name')
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

        $stockMapForTop = DB::table('stocks')
            ->select('product_id', DB::raw('COALESCE(SUM(qty), 0) as stock_qty'))
            ->groupBy('product_id')
            ->pluck('stock_qty', 'product_id');

        $topProducts = [];
        $rank = 1;
        foreach (array_slice($topProductAgg, 0, 10, true) as $pid => $data) {
            if (isset($allProductsCategoryMap[$pid])) {
                $pObj = $allProductsCategoryMap[$pid];
                $topProducts[] = [
                    'id' => $pid,
                    'rank' => $rank++,
                    'code' => $pObj->item_code ?: ('ITEM-' . $pid),
                    'name' => strtoupper($pObj->item_name),
                    'category' => $pObj->category_name ?? 'Nimko',
                    'units_sold' => (int)$data['qty'],
                    'revenue' => (float)$data['total'],
                    'stock_qty' => (float)($stockMapForTop[$pid] ?? 0)
                ];
            }
        }

        if (empty($topProducts)) {
            $catalogProdsQ = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->select('products.id', 'products.item_code', 'products.item_name', 'products.price', 'products.wholesale_price', 'categories.name as category_name');
            if (!is_all_branches()) {
                $catalogProdsQ->whereIn('products.id', function($q) {
                    $q->select('product_id')->from('stocks')
                      ->where('branch_id', active_branch_id())
                      ->whereNull('warehouse_id');
                });
            }
            $catalogProds = $catalogProdsQ->limit(10)->get();
            if ($catalogProds->isEmpty()) {
                $catalogProds = DB::table('products')
                    ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                    ->select('products.id', 'products.item_code', 'products.item_name', 'products.price', 'products.wholesale_price', 'categories.name as category_name')
                    ->limit(10)->get();
            }
            foreach ($catalogProds as $idx => $sp) {
                $topProducts[] = [
                    'id' => $sp->id,
                    'rank' => $idx + 1,
                    'code' => $sp->item_code ?: ('ITEM-' . $sp->id),
                    'name' => strtoupper($sp->item_name),
                    'category' => $sp->category_name ?? 'Nimko',
                    'units_sold' => 0,
                    'revenue' => (float)($sp->price ?: $sp->wholesale_price),
                    'stock_qty' => (float)($stockMapForTop[$sp->id] ?? 0)
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
            $catQuery = DB::table('categories')
                ->leftJoin('products', 'categories.id', '=', 'products.category_id')
                ->select('categories.name', DB::raw('COUNT(products.id) as p_count'));

            if (!is_all_branches()) {
                $catQuery->join('stocks', function($join) {
                    $join->on('products.id', '=', 'stocks.product_id')
                         ->where('stocks.branch_id', '=', active_branch_id())
                         ->whereNull('stocks.warehouse_id');
                });
            }

            $categoriesInDb = $catQuery->groupBy('categories.id', 'categories.name')
                ->orderByDesc('p_count')
                ->get();

            if ($categoriesInDb->isEmpty()) {
                $categoriesInDb = DB::table('categories')
                    ->leftJoin('products', 'categories.id', '=', 'products.category_id')
                    ->select('categories.name', DB::raw('COUNT(products.id) as p_count'))
                    ->groupBy('categories.id', 'categories.name')
                    ->orderByDesc('p_count')
                    ->get();
            }
            
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
        $todayInQ = DB::table('sales')->whereDate('created_at', date('Y-m-d'));
        $todayOutQ = DB::table('expense_vouchers')->whereDate('created_at', date('Y-m-d'));
        if (!is_all_branches()) {
            $todayInQ->where('branch_id', active_branch_id());
            $todayOutQ->where('branch_id', active_branch_id());
        }
        $todayIn = $todayInQ->sum('total_net');
        $todayOut = $todayOutQ->sum('total_amount');
        $totalIn = $totalSales;
        $totalOut = $totalPurchases + $totalExpenses;

        // Financial Position Metrics
        $custRecQ = DB::table('customer_ledgers')
            ->join('customers', 'customer_ledgers.customer_id', '=', 'customers.id')
            ->whereIn('customer_ledgers.id', function($q) {
                $q->select(DB::raw('MAX(id)'))->from('customer_ledgers')->groupBy('customer_id');
            });
        if (!is_all_branches()) {
            $custRecQ->where('customers.branch_id', active_branch_id());
        }
        $customerReceivables = $custRecQ->sum('customer_ledgers.closing_balance');

        $vendorLedgerQ = DB::table('vendor_ledgers')
            ->whereIn('id', function($q) {
                $q->select(DB::raw('MAX(id)'))->from('vendor_ledgers')->groupBy('vendor_id');
            });

        if (!is_all_branches()) {
            $branchVendorIds = DB::table('purchases')->where('branch_id', active_branch_id())->pluck('vendor_id')
                ->merge(DB::table('raw_material_purchases')->where('branch_id', active_branch_id())->pluck('vendor_id'))
                ->merge(DB::table('vendor_payments')->where('branch_id', active_branch_id())->pluck('vendor_id'))
                ->merge(DB::table('raw_material_purchase_returns')->where('branch_id', active_branch_id())->pluck('vendor_id'))
                ->filter()->unique();

            $otherBranchVendorIds = DB::table('purchases')->where('branch_id', '!=', active_branch_id())->pluck('vendor_id')
                ->merge(DB::table('raw_material_purchases')->where('branch_id', '!=', active_branch_id())->pluck('vendor_id'))
                ->merge(DB::table('vendor_payments')->where('branch_id', '!=', active_branch_id())->pluck('vendor_id'))
                ->filter()->unique();

            $applicableVendorIds = DB::table('vendors')
                ->where(function($q) use ($branchVendorIds, $otherBranchVendorIds) {
                    $q->whereIn('id', $branchVendorIds)
                      ->orWhereNotIn('id', $otherBranchVendorIds);
                })
                ->pluck('id');

            $vendorLedgerQ->whereIn('vendor_id', $applicableVendorIds);
        }

        $vendorPayables = (float) $vendorLedgerQ->sum('closing_balance');

        $stockValQ = DB::table('stocks')
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->whereNull('stocks.warehouse_id');
        if (!is_all_branches()) {
            $stockValQ->where('stocks.branch_id', active_branch_id());
        }
        $stockInventoryValue = $stockValQ->sum(DB::raw('stocks.qty * COALESCE(NULLIF(products.price, 0), products.wholesale_price, 0)'));

        if (!is_all_branches()) {
            $bId = active_branch_id();
            // Branch Cash In: Sales (cash part) + Customer Payments (cash)
            $bSalesCash = (float) DB::table('sales')->where('branch_id', $bId)->sum('cash');
            $bCustCash  = (float) DB::table('customer_payments')->where('branch_id', $bId)->where('payment_method', 'like', '%Cash%')->sum('amount');
            
            // Branch Cash Out: Vendor Payments (cash) + Expense Vouchers
            $bVendCash  = (float) DB::table('vendor_payments')->where('branch_id', $bId)->where('payment_method', 'like', '%Cash%')->sum('amount');
            $bExpCash   = (float) DB::table('expense_vouchers')->where('branch_id', $bId)->sum('total_amount');

            // Liquid cash in drawer for active branch
            $cashInHand = max(0, ($bSalesCash + $bCustCash) - ($bVendCash + $bExpCash));
            
            // Bank / Digital transactions for active branch
            $bSalesCard = (float) DB::table('sales')->where('branch_id', $bId)->sum('card');
            $bCustBank  = (float) DB::table('customer_payments')->where('branch_id', $bId)->where('payment_method', 'not like', '%Cash%')->sum('amount');
            $bVendBank  = (float) DB::table('vendor_payments')->where('branch_id', $bId)->where('payment_method', 'not like', '%Cash%')->sum('amount');
            $branchBankBal = max(0, ($bSalesCard + $bCustBank) - $bVendBank);

            $cashBalance      = $cashInHand + $branchBankBal;
            $meezanBalance    = $branchBankBal;
            $easyPaisaBalance = 0;
        } else {
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
        }

        // Recent Activities List
        $recentSalesQ = DB::table('sales')
            ->select('invoice_no as title', 'total_net as amount', 'created_at', DB::raw("'sale' as type"))
            ->orderBy('id', 'desc')
            ->limit(4);

        $recentPurchasesQ = DB::table('purchases')
            ->select('invoice_no as title', 'net_amount as amount', 'created_at', DB::raw("'purchase' as type"))
            ->orderBy('id', 'desc')
            ->limit(4);

        if (!is_all_branches()) {
            $recentSalesQ->where('branch_id', active_branch_id());
            $recentPurchasesQ->where('branch_id', active_branch_id());
        }

        $recentSales = $recentSalesQ->get();
        $recentPurchases = $recentPurchasesQ->get();

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

        
        // ==========================================
        // ADDITIONAL MODULES METRICS (WHOLE PROJECT)
        // ==========================================
        // 1. Distributor Network
        $distributorsCount = DB::table('distributors')->count();
        $activeDistributorsCount = DB::table('distributors')->where('status', 'active')->count();

        $distOrdersQuery = DB::table('distributor_orders');
        if ($startDate && $endDate) {
            $distOrdersQuery->whereBetween('order_date', [Carbon::parse($startDate)->format('Y-m-d'), Carbon::parse($endDate)->format('Y-m-d')]);
        }
        $distributorOrdersCount = $distOrdersQuery->count();
        $totalDistOrdersValue = (float) $distOrdersQuery->sum('total_amount');
        $allTimeDistOrdersValue = (float) DB::table('distributor_orders')->sum('total_amount');
        $allTimeDistOrdersCount = DB::table('distributor_orders')->count();

        $pendingDistOrdersCount = DB::table('distributor_orders')->where('status', 'pending')->count();
        $pendingDistPaymentsCount = DB::table('distributor_payments')->where('status', 'pending')->count();
        $pendingDistPaymentsTotal = (float) DB::table('distributor_payments')->where('status', 'pending')->sum('amount');
        $distributorReceivables = (float) DB::table('distributors')->where('status', 'active')->sum('current_balance');

        $recentDistOrders = DB::table('distributor_orders')
            ->leftJoin('distributors', 'distributor_orders.distributor_id', '=', 'distributors.id')
            ->select('distributor_orders.*', 'distributors.name as distributor_name', 'distributors.city as distributor_city')
            ->orderBy('distributor_orders.id', 'desc')
            ->limit(5)
            ->get();

        $channelRetail = (float) $totalSales;
        $channelDistributor = (float) ($allTimeDistOrdersValue > 0 ? $allTimeDistOrdersValue : $totalDistOrdersValue);

        // 2. Factory Production & Manufacturing
        $productionQuery = DB::table('production_entries');
        if (!is_all_branches()) {
            $productionQuery->where('branch_id', active_branch_id());
        }
        $productionCount = $productionQuery->count();
        $productionTotalCost = (float) $productionQuery->sum('production_cost');

        $recentProductions = DB::table('production_entries as pe')
            ->leftJoin('users as u', 'u.id', '=', 'pe.created_by')
            ->select('pe.*', 'u.name as user_name')
            ->when(!is_all_branches(), function($q) {
                $q->where('pe.branch_id', active_branch_id());
            })
            ->orderBy('pe.id', 'desc')
            ->limit(5)
            ->get();

        $rawMaterialsCount = DB::table('raw_materials')->count();
        $lowRawMaterialsCount = 0;
        try {
            $lowRawMaterialsCount = DB::table('raw_materials')
                ->leftJoin('raw_material_stocks', 'raw_materials.id', '=', 'raw_material_stocks.raw_material_id')
                ->select('raw_materials.id', 'raw_materials.alert_qty', DB::raw('COALESCE(SUM(raw_material_stocks.qty), 0) as current_stock'))
                ->groupBy('raw_materials.id', 'raw_materials.alert_qty')
                ->havingRaw('COALESCE(SUM(raw_material_stocks.qty), 0) <= COALESCE(raw_materials.alert_qty, 0)')
                ->get()
                ->count();
        } catch (\Throwable $e) {}

        // 3. Logistics, Warehousing & Bookings
        $warehousesCount = DB::table('warehouses')->count();

        $pendingTransfersQuery = DB::table('stock_transfers')->where('status', 'pending');
        if (!is_all_branches()) {
            $pendingTransfersQuery->where('to_branch_id', active_branch_id());
        }
        $pendingTransfersCount = $pendingTransfersQuery->count();

        $recentTransfers = DB::table('stock_transfers as st')
            ->leftJoin('products as p', 'p.id', '=', 'st.product_id')
            ->select('st.*', 'p.item_name')
            ->when(!is_all_branches(), function($q) {
                $q->where(function($sub) {
                    $sub->where('st.branch_id', active_branch_id())
                        ->orWhere('st.to_branch_id', active_branch_id());
                });
            })
            ->orderBy('st.id', 'desc')
            ->limit(5)
            ->get();

        $bookingsCount = DB::table('product_bookings')->count();
        $bookingsTotal = (float) DB::table('product_bookings')->sum('total_net');

        $recentReceipts = DB::table('receipts_vouchers')->orderBy('id', 'desc')->limit(4)->get();
        $recentPayments = DB::table('payment_vouchers')->orderBy('id', 'desc')->limit(4)->get();

        
        // Top 10 Active Distributors
        $topDistributors = DB::table('distributors')
            ->leftJoin('distributor_orders', 'distributors.id', '=', 'distributor_orders.distributor_id')
            ->select(
                'distributors.id',
                'distributors.name',
                'distributors.code',
                'distributors.city',
                'distributors.phone',
                'distributors.current_balance',
                'distributors.status',
                DB::raw('COUNT(distributor_orders.id) as orders_count'),
                DB::raw('COALESCE(SUM(distributor_orders.total_amount), 0) as total_ordered')
            )
            ->groupBy('distributors.id', 'distributors.name', 'distributors.code', 'distributors.city', 'distributors.phone', 'distributors.current_balance', 'distributors.status')
            ->orderByDesc('current_balance')
            ->limit(10)
            ->get();

        // Top 10 Customers / Debtors
        $topCustomers = DB::table('customers')
            ->leftJoin('customer_ledgers', function($join) {
                $join->on('customers.id', '=', 'customer_ledgers.customer_id')
                     ->whereIn('customer_ledgers.id', function($q) {
                         $q->select(DB::raw('MAX(id)'))->from('customer_ledgers')->groupBy('customer_id');
                     });
            })
            ->select(
                'customers.id',
                'customers.customer_name',
                'customers.customer_type',
                'customers.customer_category',
                'customers.mobile',
                'customers.address',
                DB::raw('COALESCE(customer_ledgers.closing_balance, customers.opening_balance, 0) as balance')
            )
            ->orderByDesc('balance')
            ->limit(10)
            ->get();

        // Recent 10 Sales Invoices
        $recentSalesList = DB::table('sales')
            ->leftJoin('customers', 'sales.customer', '=', 'customers.id')
            ->select(
                'sales.id',
                'sales.invoice_no',
                'sales.customer as customer_field',
                'customers.customer_name',
                'sales.total_net',
                'sales.cash',
                'sales.card',
                'sales.created_at'
            )
            ->when(!is_all_branches(), function($q) {
                $q->where('sales.branch_id', active_branch_id());
            })
            ->orderByDesc('sales.id')
            ->limit(10)
            ->get();

        return compact(
            'categoryCount', 'subcategoryCount', 'productCount', 'customerscount',
            'suppliersCount', 'employeesCount',
            'totalPurchases', 'totalPurchaseReturns', 'totalSales', 'totalSalesReturns',
            'totalRawMaterialPurchases', 'rawMaterialGrowth',
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
            'startDate', 'endDate', 'start', 'end',
            'salesCount',
            'distributorsCount', 'activeDistributorsCount', 'distributorOrdersCount',
            'totalDistOrdersValue', 'allTimeDistOrdersValue', 'allTimeDistOrdersCount',
            'pendingDistOrdersCount', 'pendingDistPaymentsCount', 'pendingDistPaymentsTotal',
            'distributorReceivables', 'recentDistOrders',
            'productionCount', 'productionTotalCost', 'recentProductions',
            'rawMaterialsCount', 'lowRawMaterialsCount',
            'warehousesCount', 'pendingTransfersCount', 'recentTransfers',
            'bookingsCount', 'bookingsTotal',
            'recentReceipts', 'recentPayments',
            'channelRetail', 'channelDistributor',
            'topDistributors', 'topCustomers', 'recentSalesList'
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

<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Stock;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductDiscount;
use App\Models\Customer;
use App\Models\SalesReturn;
use Illuminate\Http\Request;

use App\Models\CustomerLedger;
use App\Models\ProductBooking;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            
            // 🔹 Base Query
            $query = Sale::with(['customer_relation', 'user', 'branch'])->withCount('returns');

            // 🔹 Multi-Branch Scoping
            if (!is_all_branches()) {
                $query->where('branch_id', active_branch_id());
            }

            // 🔹 Restrict non-admin users to their own sales
            if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Super Admin')) {
                 $query->where('user_id', auth()->id());
            }

            // 🔹 Apply Filters
            if ($request->filled('from_date')) {
                $query->where('created_at', '>=', \Carbon\Carbon::parse($request->from_date)->startOfDay());
            }
            if ($request->filled('to_date')) {
                $query->where('created_at', '<=', \Carbon\Carbon::parse($request->to_date)->endOfDay());
            }
            if ($request->filled('filter_user')) {
                 $query->where('user_id', $request->filter_user);
            }

            // 🔹 Search Filter
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('invoice_no', 'like', "%{$search}%")
                      ->orWhere('reference', 'like', "%{$search}%")
                      ->orWhere('total_bill_amount', 'like', "%{$search}%") // Search Amount
                      ->orWhere('product_code', 'like', "%{$search}%")      // Search Barcode/Item Code
                      ->orWhereHas('customer_relation', function($c) use ($search) {
                          $c->where('customer_name', 'like', "%{$search}%");
                      });

                    // Search by Product Name (indirectly via IDs)
                    // 1. Find IDs of products matching the search name
                    $matchingProductIds = \App\Models\Product::where('item_name', 'like', "%{$search}%")
                        ->orWhere('barcode_path', 'like', "%{$search}%") // Extra safety
                        ->pluck('id')
                        ->toArray();
                    
                    if (!empty($matchingProductIds)) {
                        foreach ($matchingProductIds as $pid) {
                            // Search for this ID in the comma-separated product column
                            // We use precise matching to avoid finding '1' in '10', '21', etc.
                            // Patterns: "1", "1,2", "2,1", "3,1,4"
                            $q->orWhereRaw("FIND_IN_SET(?, product)", [$pid]);
                        }
                    }
                });
            }

            // 🔹 Total Records (before filtering)
            $totalRecordsQuery = Sale::query();
            if (!is_all_branches()) {
                $totalRecordsQuery->where('branch_id', active_branch_id());
            }
            if (!auth()->user()->hasRole('Admin')) {
                $totalRecordsQuery->where('user_id', auth()->id());
            }
            $totalRecords = $totalRecordsQuery->count();
            
            // 🔹 Filtered Records
            $filteredRecords = $query->count();

            // 🔹 Sorting
            if ($request->has('order')) {
                $orderColumnIndex = $request->order[0]['column'];
                $orderDir = $request->order[0]['dir'];
                
                // Map column index to database column (compact 9-column ERP table)
                $columns = [
                    1 => 'invoice_no',
                    2 => 'created_at',
                    3 => 'customer',
                    4 => 'user_id',
                    5 => 'total_items',
                    6 => 'total_net',
                    7 => 'sale_status'
                ]; 
                
                $colName = $columns[$orderColumnIndex] ?? 'id';
                
                // Fix: Invoice No (col 1) string sorting issue (999 > 1000). 
                // Use ID instead for correct numeric order.
                if ($colName === 'invoice_no') {
                     $query->orderBy('id', $orderDir);
                } elseif($colName == 'customer') {
                     // Sorting by relation is complex, fallback to id
                     $query->orderBy('id', $orderDir);
                } else {
                     $query->orderBy($colName, $orderDir);
                }
            } else {
                $query->orderBy('id', 'desc');
            }

            // 🔹 Pagination
            $skip = $request->start ?? 0;
            $take = $request->length ?? 10;
            
            $sales = $query->skip($skip)->take($take)->get();

            // 🔹 Optimized Product Fetching (Avoid N+1)
            $allProductIds = [];
            foreach ($sales as $sale) {
                if(!empty($sale->product)) {
                    $ids = explode(',', $sale->product);
                    foreach($ids as $id) $allProductIds[] = trim($id);
                }
            }
            $allProductIds = array_unique($allProductIds);

            // Fetch all needed products and variants in one query
            $productsMap = \App\Models\Product::whereIn('id', $allProductIds)->get()->keyBy('id');
            
            $allVariantIds = [];
            foreach ($sales as $sale) {
                if(!empty($sale->variant_id)) {
                    $vids = explode(',', $sale->variant_id);
                    foreach($vids as $vid) if($vid) $allVariantIds[] = trim($vid);
                }
            }
            $allVariantIds = array_unique($allVariantIds);
            $variantsMap = \App\Models\ProductVariant::whereIn('id', $allVariantIds)->get()->keyBy('id');

            // 🔹 Transform Data
            $data = [];
            foreach ($sales as $index => $sale) {
                
                // Parse CSV columns
                $prodIds = explode(',', $sale->product);
                $qtys = explode(',', $sale->qty);
                $prices = explode(',', $sale->per_price);
                $discounts = explode(',', $sale->per_discount);
                $totals = explode(',', $sale->per_total);
                $vIds = explode(',', $sale->variant_id);
                
                $barcodeHtml = '';
                $productHtml = '';
                
                foreach($prodIds as $i => $pid) {
                    $p = $productsMap[$pid] ?? null;
                    $vId = $vIds[$i] ?? null;
                    $v = $vId ? ($variantsMap[$vId] ?? null) : null;
                    
                    $barcodeHtml .= ($p ? $p->barcode_path : 'N/A') . '<br>';
                    
                    $pName = $p ? $p->item_name : 'N/A';
                    if ($v) {
                        $pName .= ' (' . ($v->size_label ?: $v->variant_name) . ')';
                    }
                    $productHtml .= $pName . '<br>';
                }

                // Helper closure for formatting
                $fmt = function($val) {
                    $val = (float)$val;
                    return ($val == (int)$val) ? number_format($val, 0) : number_format($val, 2);
                };

                // Build HTML for other list columns
                $cleanedQtys = array_map(function($q) use ($fmt) { return $fmt($q); }, $qtys);
                $qtyHtml = implode('<br>', $cleanedQtys);

                $priceHtml = '';
                foreach($prices as $pr) $priceHtml .= $fmt($pr) . '<br>';
                
                $discHtml = '';
                foreach($discounts as $d) $discHtml .= $fmt($d) . '<br>';

                $totalHtml = '';
                foreach($totals as $t) $totalHtml .= $fmt($t) . '<br>';

                // Status Badge
                $statusBadge = '<span class="badge bg-secondary">Unknown</span>';
                if ((string)$sale->sale_status === '2') {
                    $statusBadge = '<span class="badge badge-erp badge-erp-exchange"><i class="bi bi-arrow-repeat me-1"></i>Exchange</span>';
                } elseif ((string)$sale->sale_status === '1' || ($sale->returns_count ?? 0) > 0) {
                    $statusBadge = '<span class="badge badge-erp badge-erp-return"><i class="bi bi-arrow-return-left me-1"></i>Return</span>';
                } else {
                    $statusBadge = '<span class="badge badge-erp badge-erp-success"><i class="bi bi-check-circle-fill me-1"></i>Completed</span>';
                }

                // Financial Calculation (Gross, Discount, Net Amount after discount)
                $gross = (float)($sale->total_bill_amount ?: 0);
                $extraDisc = (float)($sale->total_extradiscount ?: 0);
                $lineDiscSum = 0;
                foreach($discounts as $d) {
                    $lineDiscSum += (float)trim($d);
                }
                $totDisc = $extraDisc > 0 ? $extraDisc : $lineDiscSum;
                $net = (float)($sale->total_net ?: ($gross - $totDisc));

                // Amount HTML with clear net calculation
                $amountHtml = '<div class="sl-amt-block">';
                $amountHtml .= '<div class="sl-amt-net"><span class="curr">PKR</span> ' . $fmt($net) . '</div>';
                if ($totDisc > 0) {
                    $amountHtml .= '<div class="sl-amt-breakdown">';
                    $amountHtml .= '<span class="sub" title="Gross Subtotal">Gross: ' . $fmt($gross) . '</span>';
                    $amountHtml .= '<span class="disc text-danger fw-bold" title="Total Discount">-' . $fmt($totDisc) . '</span>';
                    $amountHtml .= '</div>';
                }
                $amountHtml .= '</div>';

                // Professional Action Buttons with "View More" (Details) Modal Button
                $actions = '<div class="sl-actions-wrap">
                    <button type="button" class="sl-act sl-act-view btn-view-sale" data-id="'.$sale->id.'" title="View Invoice Details">
                        <i class="bi bi-eye-fill"></i><span>Details</span>
                    </button>
                    <a href="'.route('sales.recepit', $sale->id).'" class="sl-act sl-act-print" target="_blank" title="Thermal Print Bill / Slip">
                        <i class="bi bi-printer-fill"></i><span>Slip</span>
                    </a>
                    <a href="'.route('sales.invoice', $sale->id).'" class="sl-act sl-act-inv" target="_blank" title="Sale Invoice (A4)">
                        <i class="bi bi-file-earmark-text-fill"></i><span>A4</span>
                    </a>
                    <a href="'.route('sales.edit', $sale->id).'" class="sl-act sl-act-edit" title="Edit Sale">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <div class="dropdown sl-dropdown">
                        <button type="button" class="sl-act sl-act-more" data-bs-toggle="dropdown" aria-expanded="false" title="More Operations">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 sl-dropdown-menu">
                            <li><a class="dropdown-item" href="'.route('sales.dc', $sale->id).'" target="_blank"><i class="bi bi-truck text-emerald me-2"></i>Delivery Challan (DC)</a></li>
                            <li><a class="dropdown-item" href="'.route('sale.add', ['exchange_invoice' => $sale->invoice_no]).'"><i class="bi bi-arrow-repeat text-purple me-2"></i>Exchange Items</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item text-danger" href="'.route('sales.return.create', $sale->id).'"><i class="bi bi-arrow-return-left me-2"></i>Sale Return</a></li>
                        </ul>
                    </div>
                </div>';
                
                // Date formatting
                $dateObj = \Carbon\Carbon::parse($sale->created_at);
                $date = '<div class="sl-date-wrap"><span class="sl-d">' . $dateObj->format('d M Y') . '</span><span class="sl-t">' . $dateObj->format('h:i A') . '</span></div>';

                // Compact Total Items badge (full product list is in View Details modal)
                $prodIds = array_filter(array_map('trim', explode(',', $sale->product ?? '')));
                $itemCount = count($prodIds);
                $totalQty = array_sum(array_map('floatval', explode(',', $sale->qty ?? '')));
                $fmtQty = ($totalQty == (int)$totalQty) ? number_format($totalQty, 0) : number_format($totalQty, 2);

                $itemsBadge = '<div class="sl-items-badge" title="' . $itemCount . ' Products (' . $fmtQty . ' Qty)">'
                    . '<span class="badge bg-light text-primary border px-2 py-1"><i class="bi bi-box-seam me-1"></i>' . $itemCount . ' ' . ($itemCount === 1 ? 'Item' : 'Items') . ' (' . $fmtQty . ')</span>'
                    . '</div>';

                $data[] = [
                    $skip + $index + 1, // 0: S.No
                    '<span class="fw-bold font-monospace text-dark">' . $sale->invoice_no . '</span>', // 1: Invoice No
                    $date, // 2: Date | Time
                    $sale->customer_display_name, // 3: Customer
                    $sale->user ? $sale->user->name : 'N/A', // 4: Cashier / User
                    $itemsBadge, // 5: Total Items & Qty
                    $amountHtml, // 6: Net Amount with Gross and Discount calculation
                    $statusBadge, // 7: Status
                    $actions // 8: Actions
                ];
            }

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data" => $data
            ]);
        }
        $openingBalance = \App\Models\UserOpeningBalance::where('user_id', auth()->id())
            ->where('date', date('Y-m-d'))
            ->value('amount') ?? 0;

        // Calculate today's sales
        $todaySalesQuery = Sale::query()->whereDate('created_at', date('Y-m-d'));
        if (!is_all_branches()) {
            $todaySalesQuery->where('branch_id', active_branch_id());
        }
        if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
            $todaySalesQuery->where('user_id', auth()->id());
        }
        $todaySales = (clone $todaySalesQuery)->sum('total_bill_amount');
        $todayNetSales = (clone $todaySalesQuery)->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(total_net, total_bill_amount - COALESCE(total_extradiscount, 0))'));

        // Calculate today's expenses
        $todayExpenseQuery = \App\Models\ExpenseVoucher::query()->whereDate('date', date('Y-m-d'));
        if (!is_all_branches()) {
            $todayExpenseQuery->where('branch_id', active_branch_id());
        }
        if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
            $todayExpenseQuery->where('user_id', auth()->id());
        }
        $todayExpense = $todayExpenseQuery->sum('total_amount');

        $netCash = $openingBalance + $todayNetSales - $todayExpense;

        // KPI Summary Stats for ERP Header
        $statsQuery = Sale::query();
        if (!is_all_branches()) {
            $statsQuery->where('branch_id', active_branch_id());
        }
        if (!auth()->user()->hasRole('Admin')) {
            $statsQuery->where('user_id', auth()->id());
        }
        $totalSalesCount = (clone $statsQuery)->count();
        $totalGrossSales = (clone $statsQuery)->sum('total_bill_amount');
        $totalDiscountGiven = (clone $statsQuery)->sum('total_extradiscount');
        $totalNetSales = (clone $statsQuery)->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(total_net, total_bill_amount - COALESCE(total_extradiscount, 0))'));

        return view('admin_panel.sale.index', compact(
            'openingBalance', 'todaySales', 'todayNetSales', 'todayExpense', 'netCash',
            'totalSalesCount', 'totalGrossSales', 'totalDiscountGiven', 'totalNetSales'
        ));
    }

    public function addsale()
    {
        return $this->saleScreen();
    }

    public function posSale()
    {
        $customerQuery = Customer::where('status', '!=', 'inactive');
        if (!is_all_branches()) {
            $customerQuery->where('branch_id', active_branch_id());
        }
        $Customer = $customerQuery->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $tables = \App\Models\Table::orderBy('table_name')->get();
        $bankAccounts = \App\Models\Account::where('status', 1)->with('head')->orderBy('title')->get();
        return view('admin_panel.sale.add_sale', compact('Customer', 'categories', 'tables', 'bankAccounts'));
    }

    public function saleScreen()
    {
        $branchId = active_branch_id();
        $customerQuery = Customer::where('status', '!=', 'inactive');
        if (!is_all_branches()) {
            $customerQuery->where('branch_id', $branchId);
        }
        $customers = $customerQuery->orderBy('customer_name')->get();

        // 🔹 Fetch Active Distributors
        $distributors = \App\Models\Distributor::where('status', 'active')
            ->where(function($q) {
                $q->where('type', 'distributor')
                  ->orWhereNull('type');
            })
            ->where('name', 'not like', '%booker%')
            ->orderBy('name')
            ->get();

        // 🔹 Fetch Active Order Bookers (from distributors table with type=order_booker or name like booker)
        $orderBookers = \App\Models\Distributor::where('status', 'active')
            ->where(function($q) {
                $q->where('type', 'order_booker')
                  ->orWhere('name', 'like', '%booker%');
            })
            ->orderBy('name')
            ->get();

        // 🔹 Fetch Sales Officers (Field Order Bookers)
        $salesOfficers = \App\Models\SalesOfficer::orderBy('name')->get();

        // 🔹 Fetch Market Retail Customers (distributor_customers)
        $distributorCustomers = \App\Models\DistributorCustomer::where('status', 'active')
            ->orderBy('shop_name')
            ->get();

        $categories = \App\Models\Category::with(['products' => function($q) use ($branchId) {
            $q->with(['brand', 'variants', 'activeDiscount'])
              ->withSum(['stocks as total_stock' => function($sq) use ($branchId) {
                  $sq->where('branch_id', $branchId)->whereNull('warehouse_id');
              }], 'qty');
        }])->orderBy('name')->get();

        // ⚡ Fast In-Memory Product Catalog for 0ms Instant Client-Side Search & Scan
        $allProducts = \App\Models\Product::with(['brand', 'variants', 'activeDiscount'])
            ->withSum(['stocks as total_stock' => function($sq) use ($branchId) {
                $sq->where('branch_id', $branchId)->whereNull('warehouse_id');
            }], 'qty')
            ->orderBy('item_name')
            ->get();

        $stockMap = \App\Models\Stock::where('branch_id', $branchId)
            ->whereNull('warehouse_id')
            ->get();

        $productsCatalog = [];

        foreach ($allProducts as $p) {
            $isKg = strtolower($p->unit_type ?? '') === 'kg';
            if ($p->variants && $p->variants->count() > 0) {
                foreach ($p->variants as $v) {
                    $vStockRow = $stockMap->first(function($s) use ($p, $v) {
                        return $s->product_id == $p->id && $s->variant_id == $v->id;
                    });
                    $stk = $vStockRow ? (float)$vStockRow->qty : (float)($v->stock_qty ?? 0);
                    if ($isKg) {
                        $stk = (float) $stockMap->where('product_id', $p->id)->sum('qty');
                    }

                    $sizeLabel = $v->size_label ?: $v->variant_name ?: '-';
                    $fullName = $p->item_name . ($sizeLabel !== '-' ? ' (' . $sizeLabel . ')' : '');

                    $productsCatalog[] = [
                        'id'               => $p->id,
                        'product_id'       => $p->id,
                        'variant_id'       => $v->id,
                        'item_name'        => $fullName,
                        'base_name'        => $p->item_name,
                        'size'             => $sizeLabel,
                        'color'            => $p->color ?: '-',
                        'unit_type'        => $p->unit_type ?: 'piece',
                        'item_code'        => $p->item_code,
                        'barcode'          => $v->barcode_path ?: $p->barcode_path ?: $p->item_code,
                        'brand'            => $p->brand?->name,
                        'price'            => (float)($v->price ?: $p->price),
                        'original_price'   => (float)($v->price ?: $p->price),
                        'stock'            => $stk,
                        'category_id'      => $p->category_id,
                    ];
                }
            } else {
                $price = (float) $p->price;
                if ($p->activeDiscount) {
                    $price = (float) $p->activeDiscount->final_price;
                }
                $stk = (float) ($p->total_stock ?? 0);

                $productsCatalog[] = [
                    'id'               => $p->id,
                    'product_id'       => $p->id,
                    'variant_id'       => null,
                    'item_name'        => $p->item_name,
                    'base_name'        => $p->item_name,
                    'size'             => '-',
                    'color'            => $p->color ?: '-',
                    'unit_type'        => $p->unit_type ?: 'piece',
                    'item_code'        => $p->item_code,
                    'barcode'          => $p->barcode_path ?: $p->item_code,
                    'brand'            => $p->brand?->name,
                    'price'            => $price,
                    'original_price'   => (float)$p->price,
                    'stock'            => $stk,
                    'category_id'      => $p->category_id,
                ];
            }
        }

        $bankAccounts = \App\Models\Account::where('status', 1)->with('head')->orderBy('title')->get();
        $tables = \App\Models\Table::orderBy('table_name')->get();

        return view('admin_panel.sale.sale_screen', compact(
            'customers',
            'distributors',
            'orderBookers',
            'salesOfficers',
            'distributorCustomers',
            'categories',
            'productsCatalog',
            'bankAccounts',
            'tables',
            'branchId'
        ));
    }

    public function quickStoreProduct(Request $request)
    {
        $request->validate([
            'item_name'   => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'nullable|integer',
            'barcode'     => 'nullable|string|max:100',
            'stock'       => 'nullable|numeric|min:0',
            'unit_type'   => 'nullable|string|in:piece,kg,pound',
        ]);

        $branchId = active_branch_id();
        
        // Generate next item code (format: ITEM-1, ITEM-2, ...)
        $maxNum = Product::where('item_code', 'LIKE', 'ITEM-%')
            ->selectRaw("MAX(CAST(SUBSTRING(item_code, 6) AS UNSIGNED)) as max_num")
            ->value('max_num');
        $nextId = $maxNum !== null ? ((int)$maxNum + 1) : (Product::max('id') ? Product::max('id') + 1 : 1);
        $nextCode = 'ITEM-' . $nextId;

        $barcode = trim($request->barcode ?? '');
        if (empty($barcode)) {
            $barcode = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        }

        DB::beginTransaction();
        try {
            $product = Product::create([
                'creater_id'       => auth()->id() ?? 1,
                'category_id'      => $request->category_id ?: null,
                'item_code'        => $nextCode,
                'barcode_path'     => $barcode,
                'item_name'        => $request->item_name,
                'unit_type'        => $request->unit_type ?: 'piece',
                'price'            => $request->price,
                'initial_stock'    => $request->stock ?? 0,
                'alert_quantity'   => 5,
            ]);

            $variant = ProductVariant::create([
                'product_id'     => $product->id,
                'barcode_path'   => $barcode,
                'variant_name'   => 'Standard',
                'size_label'     => 'Standard',
                'size_value'     => 1,
                'size_unit'      => $request->unit_type ?: 'piece',
                'price'          => $request->price,
                'cost_price'     => 0,
                'stock_qty'      => $request->stock ?? 0,
                'is_default'     => true,
                'is_active'      => true,
            ]);

            $stockQty = floatval($request->stock ?? 0);
            $stock = Stock::create([
                'branch_id'    => $branchId,
                'warehouse_id' => null,
                'product_id'   => $product->id,
                'variant_id'   => $variant->id,
                'qty'          => $stockQty,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully!',
                'product' => [
                    'id'           => $product->id,
                    'variant_id'   => $variant->id,
                    'name'         => $product->item_name,
                    'code'         => $product->item_code,
                    'barcode'      => $barcode,
                    'price'        => (float) $product->price,
                    'stock'        => $stockQty,
                    'unit_type'    => $product->unit_type,
                    'size'         => 'Standard',
                    'color'        => '-',
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPosProducts(Request $request)
    {
        $q       = trim($request->get('q', ''));
        $catId   = $request->get('category_id', '');
        $perPage = (int) $request->get('per_page', 60);

        $branchId = active_branch_id();
        $query = Product::with(['brand', 'stock', 'variants', 'activeDiscount', 'category_relation'])
            ->withSum(['stocks as total_stock' => function($q) use ($branchId) {
                $q->where('branch_id', $branchId)->whereNull('warehouse_id');
            }], 'qty');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('item_name', 'like', "%{$q}%")
                    ->orWhere('item_code', 'like', "%{$q}%")
                    ->orWhere('barcode_path', 'like', "%{$q}%")
                    ->orWhereHas('variants', function($vq) use ($q) {
                        $vq->where('barcode_path', 'like', "%{$q}%")
                           ->orWhere('variant_name', 'like', "%{$q}%");
                    });
            });
        }

        if ($catId !== '') {
            $query->where('category_id', $catId);
        }

        $products = $query->orderBy('item_name')->paginate($perPage);

        $items = $products->getCollection()->map(function ($product) {
            // ─── Price Resolution ───────────────────────────────────────────
            // If product price is 0 or empty, fetch price from variants
            $rawPrice = (float) $product->price;
            if ($rawPrice <= 0 && $product->variants->count() > 0) {
                // Find default variant first
                $defaultVariant = $product->variants->firstWhere('is_default', true)
                    ?? $product->variants->first();
                $rawPrice = (float) ($defaultVariant->price ?? 0);
            }

            $price = $rawPrice;
            if ($product->activeDiscount) {
                $price = (float) ($product->activeDiscount->final_price ?? $rawPrice);
            }
            // ────────────────────────────────────────────────────────────────

            $stock    = (float) ($product->total_stock ?? 0);
            $imageUrl = null;
            if ($product->image) {
                $imgPath = public_path('uploads/products/' . $product->image);
                if (is_file($imgPath)) {
                    $imageUrl = asset('uploads/products/' . $product->image);
                }
            }

            return [
                'id'               => $product->id,
                'item_name'        => $product->item_name,
                'item_code'        => $product->item_code,
                'category_id'      => $product->category_id,
                'category'         => $product->category_relation?->name ?? 'Other',
                'brand'            => $product->brand?->name ?? '',
                'unit_id'          => $product->unit_id,
                'note'             => $product->note ?? '',
                'price'            => $price,
                'original_price'   => $rawPrice,
                'has_discount'     => $product->activeDiscount ? true : false,
                'discount_percent' => $product->activeDiscount?->discount_percentage ?? 0,
                'stock'            => $stock,
                'image'            => $imageUrl,
                'has_variants'     => $product->variants->count() > 0,
                'unit_type'        => $product->unit_type ?? 'piece',
            ];
        });

        return response()->json([
            'data'         => $items,
            'current_page' => $products->currentPage(),
            'last_page'    => $products->lastPage(),
            'total'        => $products->total(),
        ]);
    }

    public function getRecentProducts(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['data' => []]);
        }

        $branchId = active_branch_id();
        $products = Product::with(['brand', 'stock', 'variants', 'activeDiscount', 'category_relation'])
            ->withSum(['stocks as total_stock' => function($q) use ($branchId) {
                $q->where('branch_id', $branchId)->whereNull('warehouse_id');
            }], 'qty')
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(function($p) use ($ids) { return array_search($p->id, $ids); })
            ->values();

        $items = $products->map(function ($product) {
            $rawPrice = (float) $product->price;
            if ($rawPrice <= 0 && $product->variants->count() > 0) {
                $defaultVariant = $product->variants->firstWhere('is_default', true)
                    ?? $product->variants->first();
                $rawPrice = (float) ($defaultVariant->price ?? 0);
            }
            $price = $rawPrice;
            if ($product->activeDiscount) {
                $price = (float) ($product->activeDiscount->final_price ?? $rawPrice);
            }
            $stock    = (float) ($product->total_stock ?? 0);
            $imageUrl = null;
            if ($product->image) {
                $imgPath = public_path('uploads/products/' . $product->image);
                if (is_file($imgPath)) {
                    $imageUrl = asset('uploads/products/' . $product->image);
                }
            }
            return [
                'id'               => $product->id,
                'item_name'        => $product->item_name,
                'item_code'        => $product->item_code,
                'category_id'      => $product->category_id,
                'category'         => $product->category_relation?->name ?? 'Other',
                'brand'            => $product->brand?->name ?? '',
                'unit_id'          => $product->unit_id,
                'note'             => $product->note ?? '',
                'price'            => $price,
                'original_price'   => $rawPrice,
                'has_discount'     => $product->activeDiscount ? true : false,
                'discount_percent' => $product->activeDiscount?->discount_percentage ?? 0,
                'stock'            => $stock,
                'image'            => $imageUrl,
                'has_variants'     => $product->variants->count() > 0,
                'unit_type'        => $product->unit_type ?? 'piece',
            ];
        });

        return response()->json(['data' => $items]);
    }

    public function searchInvoiceForExchange(Request $request)
    {
        $query = trim($request->get('q', ''));
        if (empty($query)) {
            return response()->json(['success' => false, 'message' => 'Please enter Invoice Number']);
        }

        $sale = Sale::with(['customer_relation'])
            ->where(function($q) use ($query) {
                $q->where('invoice_no', $query)
                  ->orWhere('invoice_no', 'like', "%{$query}%")
                  ->orWhere('id', $query);
            })
            ->orderBy('id', 'desc')
            ->first();

        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'Invoice not found']);
        }

        $products = explode(',', $sale->product);
        $codes = explode(',', $sale->product_code);
        $brands = explode(',', $sale->brand);
        $units = explode(',', $sale->unit);
        $prices = explode(',', $sale->per_price);
        $discounts = explode(',', $sale->per_discount);
        $qtys = explode(',', $sale->qty);
        $totals = explode(',', $sale->per_total);
        $variant_ids = explode(',', $sale->variant_id ?? '');

        $productMap = Product::whereIn('id', array_filter($products))->get()->keyBy('id');
        $variantMap = \App\Models\ProductVariant::whereIn('id', array_filter($variant_ids))->pluck('variant_name', 'id');

        $items = [];
        foreach ($products as $index => $pid) {
            if (empty($pid)) continue;
            $qty = floatval($qtys[$index] ?? 0);
            if ($qty <= 0) continue; // Only positive items can be returned

            $prod = $productMap->get($pid);
            $vId = $variant_ids[$index] ?? '';
            $name = $prod ? $prod->item_name : $pid;
            if ($vId && isset($variantMap[$vId])) {
                $name .= ' (' . $variantMap[$vId] . ')';
            }

            $items[] = [
                'prod_id'    => $pid,
                'var_id'     => $vId,
                'name'       => $name,
                'code'       => $codes[$index] ?? '',
                'brand'      => $brands[$index] ?? '',
                'unit'       => $units[$index] ?? '',
                'price'      => floatval($prices[$index] ?? 0),
                'disc'       => floatval($discounts[$index] ?? 0),
                'qty'        => $qty,
                'total'      => floatval($totals[$index] ?? 0),
                'unit_type'  => $prod ? ($prod->unit_type ?? 'piece') : 'piece'
            ];
        }

        return response()->json([
            'success'      => true,
            'sale_id'      => $sale->id,
            'invoice_no'   => $sale->invoice_no,
            'customer'     => $sale->customer_display_name,
            'date'         => \Carbon\Carbon::parse($sale->created_at)->format('d-m-Y h:i A'),
            'items'        => $items,
        ]);
    }

    public function getProductVariants($id)
    {
        $branchId = active_branch_id();
        $product = Product::with(['variants', 'stock'])->findOrFail($id);
        $isKg = strtolower($product->unit_type ?? '') === 'kg';

        $totalStock = Stock::where('product_id', $product->id)
            ->where('branch_id', $branchId)
            ->whereNull('warehouse_id')
            ->sum('qty');

        $variants = $product->variants->map(function ($v) use ($product, $branchId, $isKg, $totalStock) {
            if ($isKg) {
                // For KG products, stock is stored as bulk grams on main product (variant_id IS NULL)
                $stockQty = (float) $totalStock;
            } else {
                // Get real stock from stocks table for this variant
                $vStock = Stock::where('product_id', $product->id)
                    ->where('branch_id', $branchId)
                    ->whereNull('warehouse_id')
                    ->where('variant_id', $v->id)
                    ->first();
                $stockQty = $vStock ? (float) $vStock->qty : 0;
            }
            
            // Check cost price / purchase price
            $costPrice = (float)($v->cost_price ?? 0);
            if ($costPrice <= 0) {
                $lastPi = DB::table('purchase_items')
                    ->where('product_id', $product->id)
                    ->where('variant_id', $v->id)
                    ->orderBy('id', 'desc')
                    ->value('price');
                if ($lastPi && (float)$lastPi > 0) {
                    $costPrice = (float)$lastPi;
                }
            }

            return [
                'id'              => $v->id,
                'name'            => $v->variant_name,
                'size_label'      => $v->size_label ?: $v->variant_name,
                'size_value'      => $v->size_value,
                'size_unit'       => $v->size_unit,
                'price'           => (float) $v->price,
                'wholesale_price' => (float) ($v->wholesale_price ?: $v->price),
                'cost_price'      => $costPrice,
                'purchase_price'  => $costPrice,
                'stock'           => $stockQty,
                'is_default'      => $v->is_default,
            ];
        });

        $prodCostPrice = (float)($product->cost_price ?? 0);
        if ($prodCostPrice <= 0) {
            $lastProdPi = DB::table('purchase_items')
                ->where('product_id', $product->id)
                ->orderBy('id', 'desc')
                ->value('price');
            if ($lastProdPi && (float)$lastProdPi > 0) {
                $prodCostPrice = (float)$lastProdPi;
            }
        }

        return response()->json([
            'product_id'     => $product->id,
            'item_name'      => $product->item_name,
            'item_code'      => $product->item_code,
            'unit_type'      => $product->unit_type ?? 'piece',
            'price'          => (float) ($product->price ?? 0),
            'cost_price'     => $prodCostPrice,
            'purchase_price' => $prodCostPrice,
            'total_stock'    => (float) $totalStock,
            'variants'       => $variants,
        ]);
    }

    /**
     * Scan Barcode for POS (instantly returns variant/product for cart)
     */
    public function scanBarcode(Request $request)
    {
        $barcode = trim($request->get('barcode', ''));
        if ($barcode === '') {
            return response()->json([
                'success' => false,
                'found'   => false,
                'message' => 'Barcode cannot be empty.'
            ], 422);
        }

        $branchId = active_branch_id();

        // 1. Exact match in ProductVariant (Variant Barcode or Variant ID)
        $variant = ProductVariant::with(['product.brand', 'product.category_relation', 'product.activeDiscount'])
            ->where(function ($q) use ($barcode) {
                $q->where('barcode_path', $barcode)
                  ->orWhere('id', $barcode);
            })
            ->whereHas('product')
            ->first();

        if ($variant && $variant->product) {
            $product = $variant->product;
            $isKg    = strtolower($product->unit_type ?? '') === 'kg';

            // Calculate stock
            if ($isKg) {
                $stock = (float) Stock::where('product_id', $product->id)
                    ->where('branch_id', $branchId)
                    ->whereNull('warehouse_id')
                    ->sum('qty');
            } else {
                $vStock = Stock::where('product_id', $product->id)
                    ->where('branch_id', $branchId)
                    ->whereNull('warehouse_id')
                    ->where('variant_id', $variant->id)
                    ->first();
                $stock = $vStock ? (float) $vStock->qty : 0;
            }

            // Price & discount
            $rawPrice = (float) $variant->price;
            $price = $rawPrice;
            $discPercent = 0;
            if ($product->activeDiscount) {
                $discPercent = (float) $product->activeDiscount->discount_percentage;
                $price = (float) ($product->activeDiscount->final_price ?? $rawPrice);
            }

            $variantTitle = $variant->size_label ?: $variant->variant_name;
            $displayLabel = $product->item_name . ' (' . $variantTitle . ')';

            $imageUrl = null;
            if ($product->image) {
                $imgPath = public_path('uploads/products/' . $product->image);
                if (is_file($imgPath)) {
                    $imageUrl = asset('uploads/products/' . $product->image);
                }
            }

            return response()->json([
                'success' => true,
                'found'   => true,
                'type'    => 'variant',
                'item'    => [
                    'id'               => $product->id,
                    'variant_id'       => $variant->id,
                    'product_id'       => $product->id,
                    'name'             => $product->item_name,
                    'code'             => $product->item_code ?: $variant->barcode_path,
                    'barcode'          => $variant->barcode_path,
                    'label'            => $displayLabel,
                    'variant_name'     => $variantTitle,
                    'price'            => $price,
                    'original_price'   => $rawPrice,
                    'qty'              => 1,
                    'disc'             => $discPercent,
                    'note'             => $product->note ?? '',
                    'image'            => $imageUrl,
                    'unit_id'          => $product->unit_id,
                    'unit_type'        => $product->unit_type ?? 'piece',
                    'stock'            => $stock,
                ],
                'message' => "{$product->item_name} ({$variantTitle}) added to cart!"
            ]);
        }

        // 2. Search in Product (Main Product Barcode or Item Code or ID)
        $product = Product::with(['brand', 'variants', 'activeDiscount', 'category_relation'])
            ->where(function ($q) use ($barcode) {
                $q->where('barcode_path', $barcode)
                  ->orWhere('item_code', $barcode)
                  ->orWhere('id', $barcode);
            })
            ->first();

        if ($product) {
            $isKg = strtolower($product->unit_type ?? '') === 'kg';
            $stock = (float) Stock::where('product_id', $product->id)
                ->where('branch_id', $branchId)
                ->whereNull('warehouse_id')
                ->sum('qty');

            // If product has variants, pick default or first variant
            $variant = null;
            if ($product->variants && $product->variants->count() > 0) {
                $variant = $product->variants->firstWhere('is_default', true)
                    ?? $product->variants->first();
            }

            $rawPrice = $variant ? (float) $variant->price : (float) $product->price;
            $price = $rawPrice;
            $discPercent = 0;
            if ($product->activeDiscount) {
                $discPercent = (float) $product->activeDiscount->discount_percentage;
                $price = (float) ($product->activeDiscount->final_price ?? $rawPrice);
            }

            $variantTitle = $variant ? ($variant->size_label ?: $variant->variant_name) : null;
            $displayLabel = $product->item_name;
            if ($variantTitle) {
                $displayLabel .= ' (' . $variantTitle . ')';
            }

            $imageUrl = null;
            if ($product->image) {
                $imgPath = public_path('uploads/products/' . $product->image);
                if (is_file($imgPath)) {
                    $imageUrl = asset('uploads/products/' . $product->image);
                }
            }

            return response()->json([
                'success' => true,
                'found'   => true,
                'type'    => $variant ? 'variant' : 'product',
                'item'    => [
                    'id'               => $product->id,
                    'variant_id'       => $variant ? $variant->id : null,
                    'product_id'       => $product->id,
                    'name'             => $product->item_name,
                    'code'             => $product->item_code,
                    'barcode'          => $variant ? $variant->barcode_path : $product->barcode_path,
                    'label'            => $displayLabel,
                    'variant_name'     => $variantTitle,
                    'price'            => $price,
                    'original_price'   => $rawPrice,
                    'qty'              => 1,
                    'disc'             => $discPercent,
                    'note'             => $product->note ?? '',
                    'image'            => $imageUrl,
                    'unit_id'          => $product->unit_id,
                    'unit_type'        => $product->unit_type ?? 'piece',
                    'stock'            => $stock,
                ],
                'message' => "{$displayLabel} added to cart!"
            ]);
        }

        // 3. Search in ProductDiscount by discount_code
        $discount = \App\Models\ProductDiscount::with(['product.variants', 'product.brand'])
            ->where('discount_code', $barcode)
            ->where('status', 1)
            ->first();

        if ($discount && $discount->product) {
            $product = $discount->product;
            $variant = $product->variants ? ($product->variants->firstWhere('is_default', true) ?? $product->variants->first()) : null;
            $price   = (float) ($discount->final_price ?: $product->price);
            $variantTitle = $variant ? ($variant->size_label ?: $variant->variant_name) : null;
            $label   = $product->item_name . ($variantTitle ? ' (' . $variantTitle . ')' : '');

            return response()->json([
                'success' => true,
                'found'   => true,
                'type'    => 'discount',
                'item'    => [
                    'id'               => $product->id,
                    'variant_id'       => $variant ? $variant->id : null,
                    'product_id'       => $product->id,
                    'name'             => $product->item_name,
                    'code'             => $product->item_code,
                    'barcode'          => $discount->discount_code,
                    'label'            => $label,
                    'variant_name'     => $variantTitle,
                    'price'            => $price,
                    'original_price'   => (float) $product->price,
                    'qty'              => 1,
                    'disc'             => (float) $discount->discount_percentage,
                    'note'             => $product->note ?? '',
                    'image'            => null,
                    'unit_id'          => $product->unit_id,
                    'unit_type'        => $product->unit_type ?? 'piece',
                    'stock'            => 100,
                ],
                'message' => "Discounted {$label} added to cart!"
            ]);
        }

        return response()->json([
            'success' => false,
            'found'   => false,
            'message' => "Barcode [{$barcode}] nahi mila."
        ], 404);
    }

    public function searchpname(Request $request)
    {
        $q = trim($request->q ?? '');
        $branchId = active_branch_id();

        // 1. Search products
        $pQuery = Product::with(['brand', 'activeDiscount', 'variants'])
            ->orderBy('item_name');

        if ($q !== '') {
            $keywords = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
            $pQuery->where(function($query) use ($keywords, $q) {
                $query->where('item_name', 'like', "%{$q}%")
                      ->orWhere('item_code', 'like', "%{$q}%")
                      ->orWhere('barcode_path', 'like', "%{$q}%");

                foreach ($keywords as $kw) {
                    $query->orWhere('item_name', 'like', "%{$kw}%")
                          ->orWhere('item_code', 'like', "%{$kw}%")
                          ->orWhere('barcode_path', 'like', "%{$kw}%");
                }
            });
        }

        $products = $pQuery->limit(40)->get();

        // 2. Search variants directly
        $variantSearchResults = $q !== ''
            ? \App\Models\ProductVariant::with('product.brand')
                ->where(function($query) use ($q) {
                    $query->where('variant_name', 'like', "%{$q}%")
                          ->orWhere('barcode_path', 'like', "%{$q}%")
                          ->orWhere('size_label', 'like', "%{$q}%");
                })
                ->limit(30)
                ->get()
            : collect();

        $results = collect();

        // Pre-fetch stocks for found product IDs for instant speed
        $prodIds = $products->pluck('id')->merge($variantSearchResults->pluck('product_id'))->unique()->filter();
        $stockMap = \App\Models\Stock::whereIn('product_id', $prodIds)
            ->where('branch_id', $branchId)
            ->whereNull('warehouse_id')
            ->get();

        // Handle products
        foreach ($products as $p) {
            $isKg = strtolower($p->unit_type ?? '') === 'kg';
            if ($p->variants && $p->variants->count() > 0) {
                foreach ($p->variants as $v) {
                    $vStockRow = $stockMap->first(function($s) use ($p, $v) {
                        return $s->product_id == $p->id && $s->variant_id == $v->id;
                    });
                    $stk = $vStockRow ? (float)$vStockRow->qty : (float)($v->stock_qty ?? 0);
                    if ($isKg) {
                        $stk = (float) $stockMap->where('product_id', $p->id)->sum('qty');
                    }

                    $results->push([
                        'id'               => $p->id,
                        'variant_id'       => $v->id,
                        'item_name'        => $p->item_name . ' (' . ($v->size_label ?: $v->variant_name) . ')',
                        'base_name'        => $p->item_name,
                        'size'             => $v->size_label ?: $v->variant_name ?: '-',
                        'color'            => $p->color ?: '-',
                        'unit_type'        => $p->unit_type ?: 'piece',
                        'item_code'        => $p->item_code,
                        'barcode'          => $v->barcode_path ?: $p->barcode_path ?: $p->item_code,
                        'brand'            => $p->brand?->name,
                        'unit_id'          => $p->unit_id,
                        'note'             => $p->note,
                        'wholesale_price'  => $v->wholesale_price ?: $p->wholesale_price,
                        'price'            => (float)($v->price ?: $p->price),
                        'cost_price'       => (float)($v->cost_price ?: ($p->cost_price ?? 0)),
                        'purchase_price'   => (float)($v->cost_price ?: ($p->cost_price ?? 0)),
                        'original_price'   => (float)($v->price ?: $p->price),
                        'stock'            => $stk,
                        'discount_percent' => 0,
                        'discount_amount'  => 0,
                        'has_discount'     => false,
                    ]);
                }
            } else {
                $price = (float) $p->price;
                if ($p->activeDiscount) {
                    $price = (float) $p->activeDiscount->final_price;
                }
                $stk = (float) $stockMap->where('product_id', $p->id)->sum('qty');

                $results->push([
                    'id'               => $p->id,
                    'variant_id'       => null,
                    'item_name'        => $p->item_name,
                    'base_name'        => $p->item_name,
                    'size'             => '-',
                    'color'            => $p->color ?: '-',
                    'unit_type'        => $p->unit_type ?: 'piece',
                    'item_code'        => $p->item_code,
                    'barcode'          => $p->barcode_path ?: $p->item_code,
                    'brand'            => $p->brand?->name,
                    'unit_id'          => $p->unit_id,
                    'note'             => $p->note,
                    'wholesale_price'  => $p->wholesale_price,
                    'price'            => $price,
                    'cost_price'       => (float)($p->cost_price ?? 0),
                    'purchase_price'   => (float)($p->cost_price ?? 0),
                    'original_price'   => (float)$p->price,
                    'stock'            => $stk,
                    'discount_percent' => $p->activeDiscount?->discount_percentage ?? 0,
                    'discount_amount'  => $p->activeDiscount?->total_discount ?? 0,
                    'has_discount'     => $p->activeDiscount ? true : false,
                ]);
            }
        }

        // Handle variants found directly
        foreach ($variantSearchResults as $v) {
            if ($results->where('variant_id', $v->id)->count() > 0) continue;

            $p = $v->product;
            if (!$p) continue;

            $vStockRow = $stockMap->first(function($s) use ($p, $v) {
                return $s->product_id == $p->id && $s->variant_id == $v->id;
            });
            $stk = $vStockRow ? (float)$vStockRow->qty : (float)($v->stock_qty ?? 0);

            $results->push([
                'id'               => $p->id,
                'variant_id'       => $v->id,
                'item_name'        => $p->item_name . ' (' . ($v->size_label ?: $v->variant_name) . ')',
                'base_name'        => $p->item_name,
                'size'             => $v->size_label ?: $v->variant_name ?: '-',
                'color'            => $p->color ?: '-',
                'unit_type'        => $p->unit_type ?: 'piece',
                'item_code'        => $p->item_code,
                'barcode'          => $v->barcode_path ?: $p->barcode_path ?: $p->item_code,
                'brand'            => $p->brand?->name,
                'unit_id'          => $p->unit_id,
                'note'             => $p->note,
                'wholesale_price'  => $v->wholesale_price ?: $p->wholesale_price,
                'price'            => (float)($v->price ?: $p->price),
                'cost_price'       => (float)($v->cost_price ?: ($p->cost_price ?? 0)),
                'purchase_price'   => (float)($v->cost_price ?: ($p->cost_price ?? 0)),
                'original_price'   => (float)($v->price ?: $p->price),
                'stock'            => $stk,
                'discount_percent' => 0,
                'discount_amount'  => 0,
                'has_discount'     => false,
            ]);
        }

        return response()->json($results->values());
    }

    public function getActiveSaleForTable($table_id)
    {
        if (!config('app.enable_restaurant_tables', false)) {
            return response()->json(['success' => false, 'message' => 'Table facility is currently disabled.'], 403);
        }

        $table = \App\Models\Table::find($table_id);
        if (!$table || $table->status !== 'occupied') {
            return response()->json(['success' => false, 'message' => 'Table not occupied']);
        }

        $sale = \App\Models\Sale::where('table_id', $table_id)
            ->where('order_type', 'Dine-in')
            ->orderBy('id', 'desc')
            ->first();

        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'No active sale found']);
        }

        $products = explode(',', $sale->product);
        $codes = explode(',', $sale->product_code);
        $brands = explode(',', $sale->brand);
        $units = explode(',', $sale->unit);
        $prices = explode(',', $sale->per_price);
        $discounts = explode(',', $sale->per_discount);
        $qtys = explode(',', $sale->qty);
        $variant_ids = explode(',', $sale->variant_id ?? '');
        $colors = json_decode($sale->color, true) ?: [];

        // Fetch Product models to get item_name
        $productMap = Product::whereIn('id', array_filter($products))->get()->keyBy('id');
        $variantMap = \App\Models\ProductVariant::whereIn('id', array_filter($variant_ids))->pluck('variant_name', 'id');

        $items = [];
        foreach ($products as $index => $pid) {
            if (empty($pid)) continue;
            
            $prod = $productMap->get($pid);
            $vId = $variant_ids[$index] ?? '';
            $display_name = $prod ? $prod->item_name : $pid;

            // Optional: Append variant name
            if ($vId && isset($variantMap[$vId])) {
                $display_name .= ' - ' . $variantMap[$vId];
            }

            // Extract label (color field)
            $label = '';
            if (isset($colors[$index])) {
                $rawLabel = $colors[$index];
                $decoded = json_decode($rawLabel, true);
                if (json_last_error() === JSON_ERROR_NONE && !is_null($decoded)) {
                    $label = is_array($decoded) ? ($decoded[0] ?? '') : $decoded;
                } else {
                    $label = $rawLabel;
                }
            }

            $items[] = [
                'prod_id' => $pid,
                'var_id'  => $vId,
                'name'    => $display_name,
                'code'    => $codes[$index] ?? '',
                'brand'   => $brands[$index] ?? '',
                'unit'    => $units[$index] ?? '',
                'price'   => floatval($prices[$index] ?? 0),
                'disc'    => $discounts[$index] ?? 0,
                'qty'     => floatval($qtys[$index] ?? 0),
                'old_qty' => floatval($qtys[$index] ?? 0), 
                'label'   => $label,
            ];
        }

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'items'   => $items,
            'customer' => $sale->customer,
            'reference' => $sale->reference,
        ]);
    }

    public function printTableBill($table_id)
    {
        if (!config('app.enable_restaurant_tables', false)) {
            return response('<h3>Restaurant tables facility is currently disabled.</h3>', 403);
        }

        $table = \App\Models\Table::find($table_id);
        if (!$table || $table->status !== 'occupied') {
            return response('<h3>Table not occupied</h3>');
        }

        $sale = \App\Models\Sale::where('table_id', $table_id)
            ->where('order_type', 'Dine-in')
            ->orderBy('id', 'desc')
            ->first();

        if (!$sale) {
            return response('<h3>No active sale found</h3>');
        }

        $products = explode(',', $sale->product);
        $prices = explode(',', $sale->per_price);
        $discounts = explode(',', $sale->per_discount);
        $qtys = explode(',', $sale->qty);
        $totals = explode(',', $sale->per_total);
        $variant_ids = explode(',', $sale->variant_id ?? '');
        $colors = json_decode($sale->color, true) ?: [];

        $productMap = Product::whereIn('id', array_filter($products))->get()->keyBy('id');
        $variantMap = \App\Models\ProductVariant::whereIn('id', array_filter($variant_ids))->pluck('variant_name', 'id');

        $items = [];
        $subtotal = 0;
        $totalDiscount = 0;
        foreach ($products as $index => $pid) {
            if (empty($pid)) continue;

            $prod = $productMap->get($pid);
            $vId = $variant_ids[$index] ?? '';
            $name = $prod ? $prod->item_name : $pid;
            if ($vId && isset($variantMap[$vId])) {
                $name .= ' - ' . $variantMap[$vId];
            }

            $label = '';
            if (isset($colors[$index])) {
                $rawLabel = $colors[$index];
                $decoded = json_decode($rawLabel, true);
                if (json_last_error() === JSON_ERROR_NONE && !is_null($decoded)) {
                    $label = is_array($decoded) ? ($decoded[0] ?? '') : $decoded;
                } else {
                    $label = $rawLabel;
                }
            }

            $price = (float)($prices[$index] ?? 0);
            $qty = (float)($qtys[$index] ?? 0);
            $total = (float)($totals[$index] ?? 0);
            $gross = $price * $qty;
            $disc = $gross - $total;

            $subtotal += $total;
            $totalDiscount += $disc;

            $items[] = [
                'name'   => $label ?: $name,
                'price'  => $price,
                'qty'    => $qty,
                'disc'   => $disc,
                'total'  => $total,
            ];
        }

        $extraDiscount = (float)($sale->total_extradiscount ?? 0);
        $totalDiscount += $extraDiscount;
        $netTotal = $subtotal - $extraDiscount;

        return view('admin_panel.sale.print_bill', [
            'table_name' => $table->table_name,
            'items' => $items,
            'subtotal' => $subtotal,
            'extra_discount' => $extraDiscount,
            'total_discount' => $totalDiscount,
            'net_total' => $netTotal,
            'sale' => $sale,
        ]);
    }

    public function store(Request $request)
    {
        $running_sale_id = $request->input('running_sale_id');
        if (!empty($running_sale_id)) {
            return $this->updateRunningSale($request, $running_sale_id);
        }

        $action = $request->input('action'); // 'booking' or 'sale'
        $booking_id = $request->booking_id; // <-- existing booking ID if editing

        // --- Basic validation: require customer, reference, and at least one valid product row ---
        $validator = \Validator::make($request->all(), [
            'customer' => 'required',
            // We'll validate products manually below (because arrays mixed)
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return back()->withInput()->withErrors($validator);
        }

        // Validate there's at least one filled product row
        $product_ids = is_array($request->product_id) ? $request->product_id : [];
        $qtys = is_array($request->qty) ? $request->qty : [];
        $prices = is_array($request->price) ? $request->price : [];

        $hasRow = false;
        foreach ($product_ids as $i => $pid) {
            $q = isset($qtys[$i]) ? floatval($qtys[$i]) : 0;
            $p = isset($prices[$i]) ? floatval($prices[$i]) : 0;
            if (!empty($pid) && $q != 0 && $p > 0) {
                $hasRow = true;
                break;
            }
        }

        if (! $hasRow) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please add at least one product with quantity and price.'
                ], 422);
            }
            return back()->withInput()->with('error', 'Please add at least one product with quantity and price.');
        }

        DB::beginTransaction();

        try {
            // --- Input arrays (safely handle missing keys) ---
            $product_ids     = is_array($request->product_id) ? $request->product_id : [];
            $product_names   = is_array($request->product_name) ? $request->product_name : [];
            $product_codes   = is_array($request->item_code) ? $request->item_code : [];
            $brands          = is_array($request->uom) ? $request->uom : [];
            $units           = is_array($request->unit) ? $request->unit : [];
            $prices          = is_array($request->price) ? $request->price : [];
            $discounts       = is_array($request->item_disc) ? $request->item_disc : [];
            $quantities      = is_array($request->qty) ? $request->qty : [];
            $totals          = is_array($request->total) ? $request->total : [];
            $colors          = is_array($request->color) ? $request->color : [];

            // Arrays to be saved
            $combined_product_ids   = [];
            $combined_product_names = [];
            $combined_codes         = [];
            $combined_brands        = [];
            $combined_units         = [];
            $combined_prices        = [];
            $combined_discounts     = [];
            $combined_qtys          = [];
            $combined_totals        = [];
            $combined_colors        = [];
            $combined_variant_ids   = [];

            $variant_ids = is_array($request->variant_id) ? $request->variant_id : [];

            // Prevent sale exchange if only return items are present without any new items
            $checkHasReturn = false;
            $checkHasNew = false;
            foreach ($product_ids as $idx => $pid) {
                $q = isset($quantities[$idx]) ? floatval($quantities[$idx]) : 0;
                $pr = isset($prices[$idx]) ? floatval($prices[$idx]) : 0;
                if (!empty($pid) && $pr > 0) {
                    if ($q < 0) $checkHasReturn = true;
                    if ($q > 0) $checkHasNew = true;
                }
            }

            if ($checkHasReturn && !$checkHasNew) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'sale exchange is not allowed without add any new item'
                    ], 422);
                }
                return redirect()->back()->with('error', 'sale exchange is not allowed without add any new item')->withInput();
            }

            $total_items = 0;
            $hasExchangeReturn = false;
            $hasNewItem = false;

            // Pre-fetch all necessary models to avoid N+1 queries inside the loop
            $unique_product_ids = array_unique(array_filter($product_ids));
            
            // Fetch Products keyed by ID
            $productsMap = \App\Models\Product::whereIn('id', $unique_product_ids)->get()->keyBy('id');
            
            // Fetch Stocks keyed by product_id
            // Using logic from loop: we need stock for each product
            $stocksMap = \App\Models\Stock::whereIn('product_id', $unique_product_ids)->get()->keyBy('product_id');

            $processedProducts = [];
            foreach ($product_ids as $index => $product_id) {
                if (in_array($product_id, $processedProducts)) {
                    continue;
                }
                $processedProducts[] = $product_id;
                
                $qty   = isset($quantities[$index]) ? floatval($quantities[$index]) : 0;
                $price = isset($prices[$index]) ? floatval($prices[$index]) : 0;

                // skip incomplete rows (allow negative qty for exchange returns)
                if (empty($product_id) || $qty == 0 || $price <= 0) {
                    continue;
                }

                if ($qty < 0) {
                    $hasExchangeReturn = true;
                } elseif ($qty > 0) {
                    $hasNewItem = true;
                }

                $combined_product_ids[] = $product_id;

                $pname = $product_names[$index] ?? null;
                if (empty($pname)) {
                    // Use pre-fetched product
                    $prodModel = $productsMap[$product_id] ?? null;
                    $pname = $prodModel ? $prodModel->item_name : '';
                }
                $combined_product_names[] = $pname;

                $combined_codes[]      = $product_codes[$index] ?? '';
                $combined_brands[]     = $brands[$index] ?? '';
                $combined_units[]      = $units[$index] ?? '';
                $combined_prices[]     = $prices[$index] ?? 0;
                $combined_discounts[]  = $discounts[$index] ?? 0;
                $combined_qtys[]       = $quantities[$index] ?? 0;
                $combined_totals[]     = $totals[$index] ?? 0;
                $combined_colors[]     = $colors[$index] ?? '';

                $vId   = $variant_ids[$index] ?? null;
                if ($vId === '') $vId = null; // Ensure null strictly for DB
                $combined_variant_ids[] = $vId;

                // Use pre-fetched stock 
                // In Memon Nimko POS, we deduct stock for BOTH final sale and save_token
                $currentBranchId = active_branch_id();
                $prodModel = $productsMap[$product_id] ?? null;
                $isGram = $prodModel && strtolower($prodModel->unit_type ?? '') === 'kg';
                
                $dbVariantId = $isGram ? null : ($vId ?: null);
                $deductQty = $qty;

                if ($isGram) {
                    if (!empty($vId)) {
                        $vModel = \App\Models\ProductVariant::find($vId);
                        if ($vModel) {
                            $kgSize = floatval($vModel->size_value);
                            if (strtolower($vModel->size_unit ?? '') === 'kg') {
                                $deductQty = ($kgSize * $qty * 1000);
                            } else {
                                $deductQty = ($kgSize * $qty);
                            }
                        } else {
                            $deductQty = $qty * 1000;
                        }
                    } else {
                        $deductQty = $qty * 1000;
                    }
                }

                $stockQuery = Stock::where('product_id', $product_id)
                                   ->where('branch_id', $currentBranchId)
                                   ->whereNull('warehouse_id');

                if ($dbVariantId) {
                    $stockQuery->where('variant_id', $dbVariantId);
                } else {
                    $stockQuery->whereNull('variant_id');
                }
                $stock = $stockQuery->first();

                // Only Sale updates stock (We decided to deduct for save_token as well so we can always do diff updates safely)
                if ($action === 'sale' || $action === 'save_token') {
                    if ($stock) {
                        $stock->qty = $stock->qty - $deductQty;
                        $stock->save();
                    } else {
                        // For shop sales, create with null warehouse (shop stock)
                        $stock = \App\Models\Stock::create([
                            'branch_id'    => $currentBranchId,
                            'warehouse_id' => null,
                            'product_id'   => $product_id,
                            'variant_id'   => $dbVariantId,
                            'qty'          => 0 - $deductQty,
                        ]);
                    }

                    // 🔹 Sync with legacy/display stock column on variants table (only for non-kg)
                    if ($vId && !$isGram) {
                        $vModel = \App\Models\ProductVariant::find($vId);
                        if ($vModel) {
                            $vModel->stock_qty -= $qty; // Qty here is the primary unit quantity
                            $vModel->save();
                        }
                    }
                }
                $total_items += $qty;
            }

            // --- Choose model ---
            if ($action === 'booking' || ($action === 'sale' && !empty($booking_id))) {
                $model = !empty($booking_id) ? \App\Models\ProductBooking::findOrFail($booking_id) : new \App\Models\ProductBooking();
            } else { // 'sale' or 'save_token' (new direct sale)
                $model = new \App\Models\Sale();
                $model->invoice_no = \App\Models\Sale::generateInvoiceNo();
            }

            // --- Fill common fields ---
            $model->customer             = $request->customer;
            $model->reference            = $request->reference ?? '';
            $model->product              = implode(',', $combined_product_ids);
            $model->product_code         = implode(',', $combined_codes);
            $model->brand                = implode(',', $combined_brands);
            $model->unit                 = implode(',', $combined_units);
            $model->per_price            = implode(',', $combined_prices);
            $model->per_discount         = implode(',', $combined_discounts);
            $model->qty                  = implode(',', $combined_qtys);
            $model->per_total            = implode(',', $combined_totals);
            $model->color                = json_encode($combined_colors);
            $model->total_amount_Words   = $request->total_amount_Words ?? '';
            $model->total_bill_amount    = $request->total_subtotal ?? array_sum($combined_totals);
            $model->total_extradiscount  = $request->total_extra_cost ?? 0;
            $model->total_net            = $request->total_net ?? array_sum($combined_totals);
            $model->branch_id            = active_branch_id();
            
            // For bookings, we track advance and final payment
            if ($model instanceof \App\Models\ProductBooking) {
                if ($action === 'sale') {
                    $model->sale_date = now();
                    // Final payment reconciliation: 
                    // advance_payment stays as it was, 'cash' and 'card' are updated with what was received today.
                    // (Or we can store total in cash/card and keep advance separate)
                    $model->cash = $request->cash ?? 0;
                    $model->card = $request->card ?? 0;
                    $model->card_account_id = !empty($request->card_account_id) ? $request->card_account_id : null;
                    $model->change = $request->change ?? 0;
                } else {
                    // This is a new booking or edit of unconfirmed booking
                    $model->advance_payment = $request->advance_payment ?? $request->cash ?? 0;
                    $model->booking_date = $model->booking_date ?? now();
                }
            } else {
                // New direct sale
                $model->cash   = $request->cash ?? 0;
                $model->card   = $request->card ?? 0;
                $model->card_account_id = !empty($request->card_account_id) ? $request->card_account_id : null;
                $model->change = $request->change ?? 0;
            }

            $model->total_items          = $total_items;
            $model->variant_id           = implode(',', $combined_variant_ids);
            $model->total_pieces          = $request->total_pieces;
            $model->total_meter          = $request->total_meter;

            if ($model instanceof \App\Models\Sale) {
                $model->user_id = auth()->id();
                $model->order_type = $request->order_type ?? 'Walk-in';
                $model->table_id = $request->table_id ?? null;
                if ($hasExchangeReturn && $hasNewItem) {
                    $model->sale_status = 2; // 2 = Exchange
                }
            }
            
            // Table status logic for Dine-in
            if ($model instanceof \App\Models\Sale && $model->order_type === 'Dine-in' && $model->table_id) {
                $table = \App\Models\Table::find($model->table_id);
                if ($table) {
                    if ($action === 'save_token') {
                        $table->status = 'occupied';
                    } elseif ($action === 'sale') {
                        $totalNet   = floatval($request->total_net ?? 0);
                        $totalPaid  = floatval($request->cash ?? 0) + floatval($request->card ?? 0);
                        if ($totalPaid >= $totalNet && $totalNet > 0) {
                            $table->status = 'available';
                        } else {
                            $table->status = 'occupied';
                        }
                    }
                    $table->save();
                }
            }

            $model->save();

            // 💳 Bank Account Update: If payment received into Card / Bank account from Chart of Accounts
            $cardAccount = null;
            if ($action === 'sale' && !empty($model->card_account_id) && floatval($model->card) > 0) {
                $cardAccount = \App\Models\Account::find($model->card_account_id);
                if ($cardAccount) {
                    $cardAccount->opening_balance = (float)$cardAccount->opening_balance + floatval($model->card);
                    $cardAccount->save();

                    if ($model instanceof \App\Models\Sale) {
                        $model->is_account_credited = 1;
                        $model->save();
                    }
                }
            }

            // Ledger update (only for confirmed sales or direct sales)
            if ($action === 'sale') {
                $customer_id = $request->customer;
                if ($customer_id !== 'Walk-in Customer' && !empty($customer_id)) {
                    $net = floatval($request->total_net ?? 0);
                    $paidAmount = floatval($request->cash ?? 0) + floatval($request->card ?? 0);

                    if (str_starts_with($customer_id, 'dist_') || str_starts_with($customer_id, 'booker_')) {
                        // 🚚 DISTRIBUTOR / ORDER BOOKER LEDGER
                        $distId = (int) str_replace(['dist_', 'booker_'], '', $customer_id);
                        $distributor = \App\Models\Distributor::find($distId);
                        if ($distributor) {
                            $oldBalance = (float) $distributor->current_balance;
                            $balanceChange = $net - $paidAmount;
                            $newBalance = $oldBalance + $balanceChange;

                            \App\Models\DistributorLedger::create([
                                'distributor_id' => $distributor->id,
                                'date'           => $request->sale_date ?: now()->toDateString(),
                                'type'           => 'sale',
                                'reference_no'   => $model->invoice_no,
                                'description'    => "Sale Invoice #{$model->invoice_no}" . ($paidAmount > 0 ? " (Paid: Rs. " . number_format($paidAmount, 2) . ")" : " (Credit)"),
                                'debit'          => $net,
                                'credit'         => $paidAmount,
                                'balance'        => $newBalance,
                                'created_by'     => auth()->id(),
                            ]);

                            $distributor->update(['current_balance' => $newBalance]);

                            if ($paidAmount > 0) {
                                \App\Models\DistributorPayment::create([
                                    'payment_no'            => \App\Models\DistributorPayment::generatePaymentNo(),
                                    'distributor_id'        => $distributor->id,
                                    'amount'                => $paidAmount,
                                    'payment_date'          => $request->sale_date ?: now()->toDateString(),
                                    'payment_method'        => ($request->cash > 0 && $request->card > 0 ? 'Split' : ($request->card > 0 ? 'Card' : 'Cash')),
                                    'account_id'            => $model->card_account_id ?: null,
                                    'bank_name'             => $cardAccount ? $cardAccount->title : null,
                                    'transaction_reference' => $model->invoice_no,
                                    'notes'                 => "Payment on Sale Invoice #{$model->invoice_no}" . ($cardAccount ? " (Deposited into: {$cardAccount->title})" : ""),
                                    'status'                => 'approved',
                                    'approved_by'           => auth()->id(),
                                    'approved_at'           => now(),
                                    'created_by'            => auth()->id(),
                                ]);
                            }
                        }
                    } elseif (str_starts_with($customer_id, 'dc_')) {
                        // 🏪 DISTRIBUTOR CUSTOMER (RETAIL SHOP) LEDGER
                        $dcId = (int) str_replace('dc_', '', $customer_id);
                        $dc = \App\Models\DistributorCustomer::find($dcId);
                        if ($dc) {
                            $oldBalance = (float) $dc->current_balance;
                            $balanceChange = $net - $paidAmount;
                            $newBalance = $oldBalance + $balanceChange;

                            \App\Models\DistributorCustomerLedger::create([
                                'distributor_id'          => $dc->distributor_id ?? 1,
                                'distributor_customer_id' => $dc->id,
                                'date'                    => $request->sale_date ?: now()->toDateString(),
                                'type'                    => 'sale',
                                'reference_no'            => $model->invoice_no,
                                'description'             => "Direct Sale #{$model->invoice_no}",
                                'debit'                   => $net,
                                'credit'                  => $paidAmount,
                                'balance'                 => $newBalance,
                            ]);

                            $dc->update(['current_balance' => $newBalance]);
                        }
                    } elseif (str_starts_with($customer_id, 'officer_')) {
                        // Sales Officer - recorded on sale model, no balance table
                    } else {
                        // 📋 REGULAR CUSTOMER LEDGER
                        $ledger = \App\Models\CustomerLedger::where('customer_id', $customer_id)->latest('id')->first();
                        if ($ledger) {
                            $ledger->previous_balance = $ledger->closing_balance;
                            $ledger->closing_balance += $net;
                            $ledger->save();
                        } else {
                            \App\Models\CustomerLedger::create([
                                'customer_id'      => $customer_id,
                                'admin_or_user_id' => auth()->id(),
                                'previous_balance' => 0,
                                'closing_balance'  => $net,
                                'opening_balance'  => $net,
                            ]);
                        }

                        // If partial payment (udhar), also record what was paid in customer_payments
                        // so that the closing balance reduces by amount paid
                        if ($paidAmount > 0 && $paidAmount < $net) {
                            $lastCp = DB::table('customer_payments')->latest('id')->first();
                            $nextNo = 'REC-' . str_pad(($lastCp ? $lastCp->id + 1 : 1), 4, '0', STR_PAD_LEFT);
                            $payData = [
                                'customer_id'      => $customer_id,
                                'admin_or_user_id' => auth()->id(),
                                'payment_date'     => now()->toDateString(),
                                'amount'           => $paidAmount,
                                'cash'             => floatval($request->cash ?? 0),
                                'card'             => floatval($request->card ?? 0),
                                'card_account_id'  => $model->card_account_id ?: null,
                                'payment_method'   => ($request->cash > 0 && $request->card > 0 ? 'Split' : ($request->card > 0 ? 'Card' : 'Cash')),
                                'note'             => 'Paid on Sale (' . ($model->invoice_no ?? '') . ')',
                                'branch_id'        => active_branch_id(),
                                'created_at'       => now(),
                                'updated_at'       => now(),
                            ];
                            if (\Illuminate\Support\Facades\Schema::hasColumn('customer_payments', 'received_no')) {
                                $payData['received_no'] = $nextNo;
                            }
                            DB::table('customer_payments')->insert($payData);

                            // Reduce ledger by what was paid
                            $ledger2 = \App\Models\CustomerLedger::where('customer_id', $customer_id)->latest('id')->first();
                            if ($ledger2) {
                                $ledger2->previous_balance = $ledger2->closing_balance;
                                $ledger2->closing_balance -= $paidAmount;
                                $ledger2->save();
                            }
                        } elseif ($paidAmount >= $net && $net > 0) {
                            // Full payment — record in customer_payments too (reduces balance back to 0)
                            $lastCp = DB::table('customer_payments')->latest('id')->first();
                            $nextNo = 'REC-' . str_pad(($lastCp ? $lastCp->id + 1 : 1), 4, '0', STR_PAD_LEFT);
                            $payData = [
                                'customer_id'      => $customer_id,
                                'admin_or_user_id' => auth()->id(),
                                'payment_date'     => now()->toDateString(),
                                'amount'           => $net,
                                'cash'             => floatval($request->cash ?? 0),
                                'card'             => floatval($request->card ?? 0),
                                'card_account_id'  => $model->card_account_id ?: null,
                                'payment_method'   => ($request->cash > 0 && $request->card > 0 ? 'Split' : ($request->card > 0 ? 'Card' : 'Cash')),
                                'note'             => 'Paid on Sale (' . ($model->invoice_no ?? '') . ')',
                                'branch_id'        => active_branch_id(),
                                'created_at'       => now(),
                                'updated_at'       => now(),
                            ];
                            if (\Illuminate\Support\Facades\Schema::hasColumn('customer_payments', 'received_no')) {
                                $payData['received_no'] = $nextNo;
                            }
                            DB::table('customer_payments')->insert($payData);

                            $ledger2 = \App\Models\CustomerLedger::where('customer_id', $customer_id)->latest('id')->first();
                            if ($ledger2) {
                                $ledger2->previous_balance = $ledger2->closing_balance;
                                $ledger2->closing_balance -= $net;
                                $ledger2->save();
                            }
                        }
                    }
                }
            }

            DB::commit();

            // Redirect logic
            if ($model instanceof \App\Models\Sale) {
                $source = $request->input('source');
                $returnTo = ($source === 'pos') ? route('sale.pos') : route('sale.screen');
                $printMode = ($action === 'save_token') ? 'token_only' : 'invoice';
                if ($action === 'sale' && strtolower($model->order_type ?? '') === 'takeaway') {
                    $printMode = 'token_and_invoice';
                }
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success'     => true,
                        'message'     => $action === 'save_token' ? 'Order Saved.' : 'Sale completed successfully.',
                        'sale_id'     => $model->id,
                        'invoice_no'  => $model->invoice_no,
                        'invoice_url' => route('sales.invoice', $model->id) . '?return_to=' . urlencode($returnTo) . '&autoprint=1&mode=' . $printMode,
                        'receipt_url' => route('sales.recepit', $model->id) . '?return_to=' . urlencode($returnTo) . '&autoprint=1',
                        'redirect_to' => $returnTo,
                    ]);
                }
                $invoiceUrl = route('sales.invoice', $model->id) . '?return_to=' . urlencode($returnTo) . '&autoprint=1&mode=' . $printMode;
                return redirect()->to($invoiceUrl)->with('success', $action === 'save_token' ? 'Order Saved.' : 'Sale completed.');
            } else {
                // ProductBooking
                if ($action === 'sale') {
                    // Confirmation: Go back to list with alert
                    return redirect()->route('bookings.index')->with('success', 'Booking Confirmed Successfully.');
                }
                // New Booking or Edit: Show Receipt
                $returnTo = route('bookings.index');
                $receiptUrl = route('booking.receipt', $model->id) . '?return_to=' . urlencode($returnTo) . '&autoprint=1';
                return redirect()->to($receiptUrl)->with('success', 'Booking Saved Successfully.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sale Error: ' . $e->getMessage()
                ], 422);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function updateRunningSale(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $sale = \App\Models\Sale::findOrFail($id);
            $action = $request->input('action'); // 'sale' or 'save_token'

            // Fetch old items to find the newly added ones
            $old_product_ids = explode(',', $sale->product);
            $old_variant_ids = explode(',', $sale->variant_id ?? '');
            $old_qtys = explode(',', $sale->qty);
            $oldMap = [];
            foreach ($old_product_ids as $idx => $pid) {
                if(empty($pid)) continue;
                $vid = $old_variant_ids[$idx] ?? '';
                $key = $pid . '_' . ($vid ?: '0');
                $oldMap[$key] = ($oldMap[$key] ?? 0) + floatval($old_qtys[$idx] ?? 0);
            }

            // Arrays from request
            $product_ids    = $request->product_id ?? [];
            $variant_ids    = $request->variant_id ?? [];
            $product_codes  = $request->item_code ?? [];
            $brands         = $request->uom ?? $request->brand ?? [];
            $units          = $request->unit ?? [];
            $prices         = $request->price ?? [];
            $discounts      = $request->item_disc ?? [];
            $quantities     = $request->qty ?? [];
            $totals         = $request->total ?? [];
            $colors         = $request->color ?? [];

            $combined_products    = [];
            $combined_codes       = [];
            $combined_brands      = [];
            $combined_units       = [];
            $combined_prices      = [];
            $combined_discounts   = [];
            $combined_qtys        = [];
            $combined_totals      = [];
            $combined_colors      = [];
            $combined_variant_ids = [];

            $total_items = 0;
            $newItemsAddedForToken = []; // to pass to KOT

            foreach ($product_ids as $index => $product_id) {
                $vId   = $variant_ids[$index] ?? '';
                $key   = $product_id . '_' . ($vId ?: '0');
                $qty   = isset($quantities[$index]) ? floatval($quantities[$index]) : 0;
                $price = isset($prices[$index]) ? floatval($prices[$index]) : 0;

                if (empty($product_id) || $qty <= 0 || $price <= 0) continue;

                $combined_products[]    = $product_id;
                $combined_variant_ids[] = $vId;
                $combined_codes[]       = $product_codes[$index] ?? '';
                $combined_brands[]      = $brands[$index] ?? '';
                $combined_units[]       = $units[$index] ?? '';
                $combined_prices[]      = $prices[$index];
                $combined_discounts[]   = $discounts[$index] ?? 0;
                $combined_qtys[]        = $qty;
                $combined_totals[]      = $totals[$index] ?? 0;
                $combined_colors[]      = $colors[$index] ?? '';
                
                $total_items += $qty;

                $old_qty = $oldMap[$key] ?? 0;
                $qty_diff = $qty - $old_qty;

                if ($qty_diff > 0 && $action === 'save_token') {
                    // Collect diff for token ONLY if it's new/increased
                    $newItemsAddedForToken[] = [
                        'prod_id' => $product_id,
                        'var_id'  => $vId,
                        'qty'     => $qty_diff,
                        'price'   => $price
                    ];
                }

                unset($oldMap[$key]);

                // Stock update IF final sale or save_token
                if ($action === 'sale' || $action === 'save_token') {
                    $prodModel = \App\Models\Product::find($product_id);
                    $isGram = $prodModel && strtolower($prodModel->unit_type ?? '') === 'kg';
                    
                    $dbVariantId = $isGram ? null : ($vId === '' ? null : $vId);
                    $deductQtyDiff = $qty_diff;

                    if ($isGram) {
                        if (!empty($vId)) {
                            $vModel = \App\Models\ProductVariant::find($vId);
                            if ($vModel) {
                                $kgSize = floatval($vModel->size_value);
                                if (strtolower($vModel->size_unit ?? '') === 'kg') {
                                    $deductQtyDiff = ($kgSize * $qty_diff * 1000);
                                } else {
                                    $deductQtyDiff = ($kgSize * $qty_diff);
                                }
                            } else {
                                $deductQtyDiff = $qty_diff * 1000;
                            }
                        } else {
                            $deductQtyDiff = $qty_diff * 1000;
                        }
                    }

                    $stockQuery = \App\Models\Stock::where('product_id', $product_id)
                                                    ->where('branch_id', $sale->branch_id);
                    if (!empty($sale->warehouse_id)) {
                        $stockQuery->where('warehouse_id', $sale->warehouse_id);
                    } else {
                        $stockQuery->whereNull('warehouse_id');
                    }
                    if ($dbVariantId) $stockQuery->where('variant_id', $dbVariantId);
                    else $stockQuery->whereNull('variant_id');
                    $stock = $stockQuery->first();
                    
                    if ($stock) {
                        $stock->qty -= $deductQtyDiff;
                        $stock->save();
                    } else {
                        \App\Models\Stock::create([
                            'branch_id'    => $sale->branch_id ?? 1,
                            'warehouse_id' => $sale->warehouse_id ?? null,
                            'product_id'   => $product_id, 
                            'variant_id'   => $dbVariantId, 
                            'qty'          => -$deductQtyDiff
                        ]);
                    }
                }
            }

            if ($action === 'sale' || $action === 'save_token') {
                foreach ($oldMap as $key => $old_qty) {
                    if ($old_qty <= 0) continue;
                    $parts = explode('_', $key);
                    $pid = $parts[0];
                    $vid = $parts[1] === '0' ? null : $parts[1];

                    $prodModel = \App\Models\Product::find($pid);
                    $isGram = $prodModel && strtolower($prodModel->unit_type ?? '') === 'kg';
                    
                    $dbVariantId = $isGram ? null : $vid;
                    $addBackQty = $old_qty;

                    if ($isGram) {
                        if (!empty($vid)) {
                            $vModel = \App\Models\ProductVariant::find($vid);
                            if ($vModel) {
                                $kgSize = floatval($vModel->size_value);
                                if (strtolower($vModel->size_unit ?? '') === 'kg') {
                                    $addBackQty = ($kgSize * $old_qty * 1000);
                                } else {
                                    $addBackQty = ($kgSize * $old_qty);
                                }
                            } else {
                                $addBackQty = $old_qty * 1000;
                            }
                        } else {
                            $addBackQty = $old_qty * 1000;
                        }
                    }

                    $stockQuery = \App\Models\Stock::where('product_id', $pid)
                                                    ->where('branch_id', $sale->branch_id);
                    if (!empty($sale->warehouse_id)) {
                        $stockQuery->where('warehouse_id', $sale->warehouse_id);
                    } else {
                        $stockQuery->whereNull('warehouse_id');
                    }
                    if ($dbVariantId) $stockQuery->where('variant_id', $dbVariantId);
                    else $stockQuery->whereNull('variant_id');
                    
                    $stock = $stockQuery->first();
                    if ($stock) {
                        $stock->qty += $addBackQty;
                        $stock->save();
                    }
                }
            }

            // Update Sale record
            $sale->customer        = $request->customer;
            $sale->reference       = $request->reference ?? '';
            $sale->product         = implode(',', $combined_products);
            $sale->variant_id      = implode(',', $combined_variant_ids);
            $sale->product_code    = implode(',', $combined_codes);
            $sale->brand           = implode(',', $combined_brands);
            $sale->unit            = implode(',', $combined_units);
            $sale->per_price       = implode(',', $combined_prices);
            $sale->per_discount    = implode(',', $combined_discounts);
            $sale->qty             = implode(',', $combined_qtys);
            $sale->per_total       = implode(',', $combined_totals);
            $sale->color           = json_encode($combined_colors);
            $sale->total_items     = $total_items;
            // The POS submits hidden inputs 
            $sale->total_bill_amount = $request->input('total_subtotal', $sale->total_bill_amount ?? array_sum($combined_totals));
            $sale->total_extradiscount = $request->input('total_extra_cost', $sale->total_extradiscount ?? 0);
            $sale->total_net       = $request->input('total_net', $sale->total_net ?? array_sum($combined_totals));
            $sale->cash            = $request->input('cash', 0);
            $sale->card            = $request->input('card', 0);
            $sale->card_account_id = $request->input('card_account_id', null);
            $sale->change          = $request->input('change', 0);
            
            if ($action === 'sale') {
                $cardPaid = floatval($request->input('card', 0));
                $cardAccountId = $request->input('card_account_id', null);
                if ($cardPaid > 0 && $cardAccountId && empty($sale->is_account_credited)) {
                    $cardAcc = \App\Models\Account::find($cardAccountId);
                    if ($cardAcc) {
                        $cardAcc->opening_balance = (float)$cardAcc->opening_balance + $cardPaid;
                        $cardAcc->save();
                        $sale->is_account_credited = 1;
                    }
                }

                if ($request->filled('table_id')) {
                    $sale->table_id = $request->table_id;
                    $table = \App\Models\Table::find($request->table_id);
                    if ($table) {
                        // Only free table if full payment is made
                        $totalNet  = floatval($request->input('total_net', $sale->total_net ?? 0));
                        $cashPaid  = floatval($request->input('cash', 0));
                        $cardPaid  = floatval($request->input('card', 0));
                        $totalPaid = $cashPaid + $cardPaid;
                        if ($totalPaid >= $totalNet && $totalNet > 0) {
                            $table->status = 'available';
                        } else {
                            $table->status = 'occupied';
                        }
                        $table->save();
                    }
                }
            }

            $sale->save();

            DB::commit();

            // Redirects
            $returnTo = route('sale.add');
            if ($action === 'save_token') {
                session()->flash('kot_items_json', json_encode($newItemsAddedForToken));
                $invoiceUrl = route('sales.invoice', $sale->id) . '?mode=token_only&autoprint=1&return_to=' . urlencode($returnTo);
                return redirect()->to($invoiceUrl);
            }

            // Dine-in final sale: print only invoice wrapper (not token again)
            $printMode = 'invoice';
            $invoiceUrl = route('sales.invoice', $sale->id) . '?mode=' . $printMode . '&autoprint=1&return_to=' . urlencode($returnTo);
            return redirect()->to($invoiceUrl);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Update Error: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */


    public function convertFromBooking($id)
    {
        $booking = ProductBooking::with('customer_relation')->findOrFail($id);
        
        $customerQuery = Customer::where('status', '!=', 'inactive');
        if (!is_all_branches()) {
            $branchId = $booking->branch_id ?? active_branch_id();
            $customerQuery->where(function($q) use ($branchId, $booking) {
                $q->where('branch_id', $branchId);
                if ($booking->customer) {
                    $q->orWhere('id', $booking->customer);
                }
            });
        }
        $customers = $customerQuery->get();

        // --- Parsing logic (identical to ProductBookingController@receipt) ---
        $products    = explode(',', $booking->product);
        $codes       = explode(',', $booking->product_code);
        $brands      = explode(',', $booking->brand);
        $units       = explode(',', $booking->unit);
        $prices      = explode(',', $booking->per_price);
        $discounts   = explode(',', $booking->per_discount);
        $qtys        = explode(',', $booking->qty);
        $totals      = explode(',', $booking->per_total);
        $variant_ids = explode(',', $booking->variant_id ?? '');

        $items = [];
        $productIds = array_unique($products);
        $productMap = Product::with('category_relation', 'brand')->whereIn('id', $productIds)->get()->keyBy('id');
        $variantMap = \App\Models\ProductVariant::whereIn('id', array_filter($variant_ids))->get()->keyBy('id');

        foreach ($products as $index => $p) {
            $qty = (float)($qtys[$index] ?? 0);
            if ($qty <= 0) continue;

            $vId = $variant_ids[$index] ?? '';
            $vModel = $vId ? ($variantMap[$vId] ?? null) : null;
            $variantName = $vModel ? ($vModel->size_label ?: $vModel->variant_name) : '';
            $productModel = $productMap[$p] ?? null;
            $displayName = $productModel ? $productModel->item_name : $p;
            $catName = ($productModel && $productModel->category_relation) ? $productModel->category_relation->name : 'Uncategorized';

            if ($variantName && $productModel) {
                $cleanVariant = preg_replace('/\s*\([\d.]+\s*KG\)/i', '', $variantName);
                $cleanVariant = trim(str_ireplace($productModel->item_name, '', $cleanVariant));
                $cleanVariant = ltrim($cleanVariant, ' -');
                if ($cleanVariant !== '') $displayName .= ' (' . $cleanVariant . ')';
            } else if ($variantName) {
                $displayName .= ' - ' . $variantName;
            }

            $unit = $units[$index] ?? '';
            $price = (float)($prices[$index] ?? 0);

            // Weight conversion logic
            if ($vModel && $productModel && strtolower($productModel->unit_type ?? '') === 'kg' && $vModel->size_value > 0) {
                $multiplier = 1;
                $sUnit = strtolower($vModel->size_unit ?? 'kg');
                if ($sUnit === 'kg') $multiplier = (float)$vModel->size_value;
                elseif (in_array($sUnit, ['gm','gram','grams'])) $multiplier = (float)$vModel->size_value / 1000;
                
                $qty = $qty * $multiplier;
                if ($multiplier > 0) $price = $price / $multiplier;
                $unit = 'KG';
            } else if (empty($unit) || is_numeric($unit)) {
                if ($vModel && !empty($vModel->size_unit)) $unit = strtoupper($vModel->size_unit);
                else if ($productModel && !empty($productModel->unit_type)) $unit = strtoupper($productModel->unit_type);
                else $unit = 'PIECE';
            }

            $items[] = [
                'product_id' => $productModel->id ?? '',
                'item_name' => $displayName,
                'category'  => $catName,
                'item_code' => $codes[$index] ?? '',
                'uom'       => $productModel && $productModel->brand ? $productModel->brand->name : ($brands[$index] ?? ''),
                'unit'      => $unit,
                'price'     => $price,
                'discount'  => (float)($discounts[$index] ?? 0),
                'qty'       => $qty,
                'total'     => (float)($totals[$index] ?? 0),
                'available_colors' => [], // Add if needed, keeping for compatibility
            ];
        }

        return view('admin_panel.sale.booking_edit', [
            'Customer' => $customers,
            'booking' => $booking,
            'bookingItems' => $items,
        ]);
    }


    // sale return start
    public function saleretun($id)
    {
        $sale = \App\Models\Sale::findOrFail($id);
        
        $customerQuery = Customer::where('status', '!=', 'inactive');
        if (!is_all_branches()) {
            $branchId = $sale->branch_id ?? active_branch_id();
            $customerQuery->where(function($q) use ($branchId, $sale) {
                $q->where('branch_id', $branchId);
                if ($sale->customer) {
                    $q->orWhere('id', $sale->customer);
                }
            });
        }
        $customers = $customerQuery->get();

        // Split comma-based fields from the sale row
        $products  = array_map('trim', explode(',', $sale->product ?? ''));
        $codes     = array_map('trim', explode(',', $sale->product_code ?? ''));
        $brands    = array_map('trim', explode(',', $sale->brand ?? ''));
        $units     = array_map('trim', explode(',', $sale->unit ?? ''));
        $prices    = array_map('trim', explode(',', $sale->per_price ?? ''));
        $discounts = array_map('trim', explode(',', $sale->per_discount ?? ''));
        $qtys      = array_map('trim', explode(',', $sale->qty ?? ''));
        $totals    = array_map('trim', explode(',', $sale->per_total ?? ''));
        $vIds      = array_map('trim', explode(',', $sale->variant_id ?? ''));

        // decode color JSON array (if stored like ["[]","[]"])
        $colors_json = json_decode($sale->color ?? '[]', true);
        if (!is_array($colors_json)) {
            $colors_json = [];
        }

        // Fetch all previous returns for this sale from sales_returns table
        $previousReturns = \DB::table('sales_returns')->where('sale_id', $sale->id)->get();

        // Build list of previous return item records
        $returnedRows = [];
        foreach ($previousReturns as $ret) {
            $retProducts = array_map('trim', explode(',', $ret->product ?? ''));
            $retCodes    = array_map('trim', explode(',', $ret->product_code ?? ''));
            $retQtys     = array_map('trim', explode(',', $ret->qty ?? ''));

            foreach ($retProducts as $ri => $rprod) {
                $rqty = isset($retQtys[$ri]) ? floatval($retQtys[$ri]) : 0;
                if ($rqty <= 0) continue;
                $rcode = isset($retCodes[$ri]) ? trim($retCodes[$ri]) : '';
                $returnedRows[] = [
                    'product' => $rprod,
                    'code'    => $rcode,
                    'qty'     => $rqty
                ];
            }
        }

        $items = [];

        foreach ($products as $index => $p) {
            // try to find product model by id (if value numeric) or by code fallback
            $product = null;
            $productIdCandidate = null;
            $itemCodeCandidate = $codes[$index] ?? '';

            if (is_numeric($p) && intval($p) > 0) {
                $productIdCandidate = intval($p);
                $product = \App\Models\Product::find($productIdCandidate);
            }

            if (!$product && !empty($itemCodeCandidate)) {
                $product = \App\Models\Product::where('item_code', trim($itemCodeCandidate))->first();
                if ($product) {
                    $productIdCandidate = $product->id;
                }
            }

            // ---------- NOTE parsing (previously color) ----------
            $note_value = '';
            if (isset($colors_json[$index])) {
                $maybe = $colors_json[$index];
                if (is_string($maybe)) {
                    $try = json_decode($maybe, true);
                    if ($try !== null) {
                        if (is_array($try)) {
                            $note_value = implode("\n", $try);
                        } else {
                            $note_value = (string)$try;
                        }
                    } else {
                        $note_value = $maybe;
                    }
                } elseif (is_array($maybe)) {
                    $note_value = implode("\n", $maybe);
                } else {
                    $note_value = (string)$maybe;
                }
            }
            // ---------- end note parsing ----------

            $soldQty = isset($qtys[$index]) && is_numeric($qtys[$index]) ? floatval($qtys[$index]) : 0;
            $productNameCandidate = $product->item_name ?? (string)$p;

            // compute returned qty matching against $returnedRows
            $returnedQty = 0;
            foreach ($returnedRows as &$rRow) {
                if ($rRow['qty'] <= 0) continue;
                $match = false;
                if (!empty($itemCodeCandidate) && !empty($rRow['code']) && $itemCodeCandidate === $rRow['code']) {
                    $match = true;
                } elseif (!empty($productIdCandidate) && !empty($rRow['product']) && ($rRow['product'] == $productIdCandidate || $rRow['product'] === $productNameCandidate)) {
                    $match = true;
                } elseif (!empty($p) && !empty($rRow['product']) && ($rRow['product'] === $p || $rRow['product'] === $productNameCandidate)) {
                    $match = true;
                }

                if ($match) {
                    $deduct = min($rRow['qty'], $soldQty - $returnedQty);
                    $returnedQty += $deduct;
                    $rRow['qty'] -= $deduct;
                }
            }
            unset($rRow);

            $available = max(0, $soldQty - $returnedQty);

            $items[] = [
                'product_id'    => $product->id ?? ($productIdCandidate ?? ''),
                'variant_id'    => $vIds[$index] ?? null,
                'item_name'     => !empty($note_value) ? $note_value : $productNameCandidate,
                'item_code'     => $product->item_code ?? ($itemCodeCandidate ?? ''),
                'brand'         => $product->brand->name ?? ($brands[$index] ?? ''),
                'unit'          => $product->unit ?? ($units[$index] ?? ''),
                'price'         => floatval($prices[$index] ?? 0),
                'discount'      => floatval($discounts[$index] ?? 0),
                'qty'           => $soldQty,
                'total'         => floatval($totals[$index] ?? 0),
                // send note (plain text) so blade can show it
                'note'          => $note_value,
                'available_qty' => (float)$available == (int)$available ? (int)$available : number_format($available, 3, '.', ''),
            ];
        }

        return view('admin_panel.sale.return.create', [
            'sale' => $sale,
            'Customer' => $customers,
            'saleItems' => $items,
        ]);
    }



    // public function storeSaleReturn(Request $request)
    // {
    //     // dd($request->all());
    //     DB::beginTransaction();

    //     try {
    //         $product_ids     = $request->product_id;
    //         $product_names   = $request->product;
    //         $product_codes   = $request->item_code;
    //         $brands          = $request->uom;
    //         $units           = $request->unit;
    //         $prices          = $request->price;
    //         $discounts       = $request->item_disc;
    //         $quantities      = $request->qty;
    //         $totals          = $request->total;
    //         $colors          = $request->color;

    //         $combined_products   = [];
    //         $combined_codes      = [];
    //         $combined_brands     = [];
    //         $combined_units      = [];
    //         $combined_prices     = [];
    //         $combined_discounts  = [];
    //         $combined_qtys       = [];
    //         $combined_totals     = [];
    //         $combined_colors     = [];

    //         $total_items = 0;

    //         foreach ($product_ids as $index => $product_id) {
    //             $qty   = $quantities[$index] ?? 0;
    //             $price = $prices[$index] ?? 0;

    //             if (!$product_id || !$qty || !$price) continue;

    //             $combined_products[]   = $product_names[$index] ?? '';
    //             $combined_codes[]      = $product_codes[$index] ?? '';
    //             $combined_brands[]     = $brands[$index] ?? '';
    //             $combined_units[]      = $units[$index] ?? '';
    //             $combined_prices[]     = $price;
    //             $combined_discounts[]  = $discounts[$index] ?? 0;
    //             $combined_qtys[]       = $qty;
    //             $combined_totals[]     = $totals[$index] ?? 0;

    //             // Convert color to valid JSON array
    //             $decodedColor = $colors[$index] ?? [];
    //             if (is_array($decodedColor)) {
    //                 $combined_colors[] = json_encode($decodedColor);
    //             } else {
    //                 $decoded = json_decode($decodedColor, true);
    //                 $combined_colors[] = json_encode(is_array($decoded) ? $decoded : []);
    //             }

    //             // ➕ Restore stock
    //             $stock = \App\Models\Stock::where('product_id', $product_id)->first();
    //             if ($stock) {
    //                 $stock->qty += $qty;
    //                 $stock->save();
    //             }

    //             $total_items += $qty;
    //         }

    //         // ➕ Create Sale Return
    //         $saleReturn = new \App\Models\SalesReturn();
    //         $saleReturn->sale_id              = $request->sale_id;
    //         $saleReturn->customer             = $request->customer;
    //         $saleReturn->reference            = $request->reference;

    //         $saleReturn->product              = implode(',', $combined_products);
    //         $saleReturn->product_code         = implode(',', $combined_codes);
    //         $saleReturn->brand                = implode(',', $combined_brands);
    //         $saleReturn->unit                 = implode(',', $combined_units);
    //         $saleReturn->per_price            = implode(',', $combined_prices);
    //         $saleReturn->per_discount         = implode(',', $combined_discounts);
    //         $saleReturn->qty                  = implode(',', $combined_qtys);
    //         $saleReturn->per_total            = implode(',', $combined_totals);
    //         $saleReturn->color                = json_encode($combined_colors);

    //         $saleReturn->total_amount_Words   = $request->total_amount_Words;
    //         $saleReturn->total_bill_amount    = $request->total_subtotal;
    //         $saleReturn->total_extradiscount  = $request->total_extra_cost;
    //         $saleReturn->total_net            = $request->total_net;

    //         $saleReturn->cash                 = $request->cash;
    //         $saleReturn->card                 = $request->card;
    //         $saleReturn->change               = $request->change;

    //         $saleReturn->total_items          = $total_items;
    //         $saleReturn->return_note          = $request->return_note;

    //         $saleReturn->save();

    //         DB::commit();

    //         return redirect()->route('sale.index')->with('success', 'Sale return saved successfully.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Sale return failed: ' . $e->getMessage());
    //     }
    // }
    public function storeSaleReturn(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            // the arrays may be posted only for selected items, but validate generically
            'product'    => 'required|array',
            'product.*'  => 'nullable|string',
            'item_code'  => 'required|array',
            'item_code.*' => 'nullable|string',
            'unit'       => 'required|array',
            'unit.*'     => 'nullable|string',
            'price'      => 'required|array',
            'price.*'    => 'nullable|numeric',
            'item_disc'  => 'required|array',
            'item_disc.*' => 'nullable|numeric',
            'qty'        => 'required|array',
            'qty.*'      => 'nullable|numeric|min:0',
            'total'      => 'required|array',
            'total.*'    => 'nullable|numeric',
            'color'      => 'nullable|array',
            'variant_id' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $saleId = $request->sale_id;
            $sale = \App\Models\Sale::findOrFail($saleId);

            // Pre-calculate available returnable quantities per item for strict validation
            $previousReturns = \DB::table('sales_returns')->where('sale_id', $sale->id)->get();
            $returnedRows = [];
            foreach ($previousReturns as $ret) {
                $retProducts = array_map('trim', explode(',', $ret->product ?? ''));
                $retCodes    = array_map('trim', explode(',', $ret->product_code ?? ''));
                $retQtys     = array_map('trim', explode(',', $ret->qty ?? ''));

                foreach ($retProducts as $ri => $rprod) {
                    $rqty = isset($retQtys[$ri]) ? floatval($retQtys[$ri]) : 0;
                    if ($rqty <= 0) continue;
                    $rcode = isset($retCodes[$ri]) ? trim($retCodes[$ri]) : '';
                    $returnedRows[] = [
                        'product' => $rprod,
                        'code'    => $rcode,
                        'qty'     => $rqty
                    ];
                }
            }

            $saleProducts = array_map('trim', explode(',', $sale->product ?? ''));
            $saleCodes    = array_map('trim', explode(',', $sale->product_code ?? ''));
            $saleQtys     = array_map('trim', explode(',', $sale->qty ?? ''));

            $availableQtyMap = [];
            foreach ($saleProducts as $idx => $sp) {
                $sqty = isset($saleQtys[$idx]) ? floatval($saleQtys[$idx]) : 0;
                $scode = isset($saleCodes[$idx]) ? trim($saleCodes[$idx]) : '';

                $retQty = 0;
                foreach ($returnedRows as &$rRow) {
                    if ($rRow['qty'] <= 0) continue;
                    $match = false;
                    if (!empty($scode) && !empty($rRow['code']) && $scode === $rRow['code']) {
                        $match = true;
                    } elseif (!empty($sp) && !empty($rRow['product']) && ($sp === $rRow['product'] || (is_numeric($sp) && intval($sp) == intval($rRow['product'])))) {
                        $match = true;
                    }
                    if ($match) {
                        $deduct = min($rRow['qty'], $sqty - $retQty);
                        $retQty += $deduct;
                        $rRow['qty'] -= $deduct;
                    }
                }
                unset($rRow);

                $avail = max(0, $sqty - $retQty);
                if (!empty($scode)) $availableQtyMap['code_' . $scode] = $avail;
                if (!empty($sp)) $availableQtyMap['sp_' . $sp] = $avail;
            }

            // Incoming arrays (may contain only selected return rows)
            $product_names = $request->input('product', []);     // product name or id text
            $product_ids   = $request->input('product_id', []);  // may be empty strings for some rows
            $variant_ids   = $request->input('variant_id', []);
            $product_codes = $request->input('item_code', []);
            $brands        = $request->input('brand', $request->input('uom', [])); // some forms call it uom
            $units         = $request->input('unit', []);
            $prices        = $request->input('price', []);
            $discounts     = $request->input('item_disc', []);
            $quantities    = $request->input('qty', []);
            $totals        = $request->input('total', []);
            $colors        = $request->input('color', []); // may be array of single selected color value or json

            // We'll build combined arrays to save in sales_returns
            $combined_products   = [];
            $combined_codes      = [];
            $combined_brands     = [];
            $combined_units      = [];
            $combined_prices     = [];
            $combined_discounts  = [];
            $combined_qtys       = [];
            $combined_totals     = [];
            $combined_colors     = [];

            $total_items = 0;

            // Use length of product_names (should match other arrays); be defensive
            $rows = max(
                count($product_names),
                count($product_codes),
                count($quantities),
                count($prices)
            );

            for ($i = 0; $i < $rows; $i++) {
                $name = isset($product_names[$i]) ? trim($product_names[$i]) : '';
                $pid  = isset($product_ids[$i]) ? trim($product_ids[$i]) : '';
                $code = isset($product_codes[$i]) ? trim($product_codes[$i]) : '';
                $brand = isset($brands[$i]) ? trim($brands[$i]) : '';
                $unit  = isset($units[$i]) ? trim($units[$i]) : '';
                $price = isset($prices[$i]) ? floatval($prices[$i]) : 0;
                $disc  = isset($discounts[$i]) ? floatval($discounts[$i]) : 0;
                $qty   = isset($quantities[$i]) ? floatval($quantities[$i]) : 0;
                $total = isset($totals[$i]) ? floatval($totals[$i]) : ($price * $qty);
                $vId   = isset($variant_ids[$i]) ? trim($variant_ids[$i]) : '';
                $colorRaw = $colors[$i] ?? null;

                // Skip rows with zero qty (not selected)
                if ($qty <= 0) continue;

                // 🛑 Strict Server-Side Validation: Ensure requested return qty doesn't exceed available returnable qty
                $maxAvailable = null;
                if (!empty($code) && isset($availableQtyMap['code_' . $code])) {
                    $maxAvailable = $availableQtyMap['code_' . $code];
                } elseif (!empty($pid) && isset($availableQtyMap['sp_' . $pid])) {
                    $maxAvailable = $availableQtyMap['sp_' . $pid];
                } elseif (!empty($name) && isset($availableQtyMap['sp_' . $name])) {
                    $maxAvailable = $availableQtyMap['sp_' . $name];
                }

                if ($maxAvailable !== null && $qty > ($maxAvailable + 0.0001)) {
                    DB::rollBack();
                    $prodLabel = !empty($name) ? $name : (!empty($code) ? $code : 'Item');
                    return redirect()->back()->with('error', "Cannot return {$qty} units of '{$prodLabel}'. Maximum available returnable quantity is {$maxAvailable}.");
                }

                // Push to combined arrays (preserve name/code even if id missing)
                $combined_products[]  = $name;
                $combined_codes[]     = $code;
                $combined_brands[]    = $brand;
                $combined_units[]     = $unit;
                $combined_prices[]    = (string)$price;
                $combined_discounts[] = (string)$disc;
                $combined_qtys[]      = (string)$qty;
                $combined_totals[]    = (string)$total;

                // Normalize color: if array => json_encode; if string that's JSON => try decode
                if (is_array($colorRaw)) {
                    $combined_colors[] = json_encode($colorRaw);
                } else {
                    // try parse string -> if valid json array keep, else wrap single value
                    $decoded = null;
                    if (is_string($colorRaw)) {
                        $decoded = json_decode($colorRaw, true);
                    }
                    if (is_array($decoded)) {
                        $combined_colors[] = json_encode($decoded);
                    } elseif (!empty($colorRaw)) {
                        $combined_colors[] = json_encode([$colorRaw]);
                    } else {
                        $combined_colors[] = json_encode([]);
                    }
                }

                // Stock update: increase stock for returned items if we can find product by ID or code
                $foundProduct = null;
                if ($pid !== '') {
                    // If numeric id provided, try find by id
                    if (is_numeric($pid)) {
                        $foundProduct = \App\Models\Product::find(intval($pid));
                    } else {
                        // sometimes product_id may come as name; try to find by id or code fallback
                        $maybe = \App\Models\Product::find($pid);
                        if (!$maybe && $code) {
                            $maybe = \App\Models\Product::where('item_code', $code)->first();
                        }
                        $foundProduct = $maybe;
                    }
                } else if (!empty($code)) {
                    $foundProduct = \App\Models\Product::where('item_code', $code)->first();
                } else if (!empty($name)) {
                    $foundProduct = \App\Models\Product::where('item_name', $name)->first();
                }

                if ($foundProduct) {
                    // Update stock: find stock row for product in same branch/warehouse as the original sale
                    $saleBranchId = $sale->branch_id ?? 1;
                    $stockQuery = \App\Models\Stock::where('product_id', $foundProduct->id)
                                                    ->where('branch_id', $saleBranchId);

                    if (!empty($sale->warehouse_id)) {
                        $stockQuery->where('warehouse_id', $sale->warehouse_id);
                    } else {
                        $stockQuery->whereNull('warehouse_id');
                    }

                    $isKg = strtolower($foundProduct->unit_type ?? '') === 'kg';
                    $returnQtyInDb = $qty;
                    $dbVariantId = $isKg ? null : (!empty($vId) ? $vId : null);

                    if ($isKg) {
                        if (!empty($vId)) {
                            $vModel = \App\Models\ProductVariant::find($vId);
                            if ($vModel) {
                                $kgSize = floatval($vModel->size_value);
                                if (strtolower($vModel->size_unit ?? '') === 'kg') {
                                    $returnQtyInDb = ($kgSize * $qty * 1000);
                                } else {
                                    $returnQtyInDb = ($kgSize * $qty);
                                }
                            } else {
                                $returnQtyInDb = $qty * 1000;
                            }
                        } else {
                            $returnQtyInDb = $qty * 1000;
                        }
                    }

                    if ($dbVariantId) {
                        $stockQuery->where('variant_id', $dbVariantId);
                    } else {
                        $stockQuery->whereNull('variant_id');
                    }

                    $stock = $stockQuery->first();
                    if ($stock) {
                        $stock->qty = $stock->qty + $returnQtyInDb;
                        $stock->save();
                    } else {
                        // Create if not exists (defensive)
                        $saleBranchForCreate = $sale->branch_id ?? 1;
                        \App\Models\Stock::create([
                            'product_id'   => $foundProduct->id,
                            'variant_id'   => $dbVariantId,
                            'branch_id'    => $saleBranchForCreate,
                            'warehouse_id' => $sale->warehouse_id ?? null,
                            'qty'          => $returnQtyInDb
                        ]);
                    }

                    // Also sync back to variants table display column if applicable
                    if (!empty($vId) && !$isKg) {
                        $vModel = \App\Models\ProductVariant::find($vId);
                        if ($vModel) {
                            $vModel->stock_qty += $qty;
                            $vModel->save();
                        }
                    }
                }

                $total_items += $qty;
            }

            if (empty($combined_products)) {
                return redirect()->back()->with('error', 'No items selected for return.');
            }

            // Save sales_returns row (CSV arrays + json color array)
            $saleReturn = new \App\Models\SalesReturn();
            $saleReturn->sale_id = $saleId;
            $saleReturn->customer = $sale->customer ?? $request->customer;
            $saleReturn->reference = $request->reference;
            $saleReturn->product = implode(',', $combined_products);
            $saleReturn->product_code = implode(',', $combined_codes);
            $saleReturn->brand = implode(',', $combined_brands);
            $saleReturn->unit = implode(',', $combined_units);
            $saleReturn->per_price = implode(',', $combined_prices);
            $saleReturn->per_discount = implode(',', $combined_discounts);
            $saleReturn->qty = implode(',', $combined_qtys);
            $saleReturn->per_total = implode(',', $combined_totals);
            // colors as JSON array of json-encoded color-arrays (to keep compatible with your existing format)
            $saleReturn->color = json_encode($combined_colors);
            $saleReturn->total_amount_Words = $request->total_amount_Words ?? '';
            $saleReturn->total_bill_amount = $request->total_subtotal ?? array_sum($combined_totals);
            $saleReturn->total_extradiscount = $request->total_extra_cost ?? 0;
            $saleReturn->total_net = $request->total_net ?? array_sum($combined_totals);
            $saleReturn->cash = $request->cash ?? 0;
            $saleReturn->card = $request->card ?? 0;
            $saleReturn->change = $request->change ?? 0;
            $saleReturn->total_items = $total_items;
            $saleReturn->return_note = $request->return_note ?? null;
            $saleReturn->branch_id = $sale->branch_id ?? active_branch_id() ?? 1;
            $saleReturn->save();

            // Mark sale status as Returned
            $sale->sale_status = '1';
            $sale->save();


            // -----------------------
            // Customer ledger update (simple)
            // -----------------------
            $customer_id = $sale->customer ?? $request->customer;
            $netAmount = $saleReturn->total_net;

            // Only update ledger if customer is numeric (i.e., normal customer)
            if (is_numeric($customer_id) && $customer_id > 0) {
                $ledger = \App\Models\CustomerLedger::where('customer_id', $customer_id)->latest('id')->first();
                if ($ledger) {
                    $ledger->previous_balance = $ledger->closing_balance;
                    $ledger->closing_balance = $ledger->closing_balance - $netAmount;
                    $ledger->save();
                } else {
                    \App\Models\CustomerLedger::create([
                        'customer_id' => $customer_id,
                        'admin_or_user_id' => auth()->id(),
                        'previous_balance' => 0,
                        'opening_balance' => 0 - $netAmount,
                        'closing_balance' => 0 - $netAmount,
                    ]);
                }
            } else {
                // Walk-in Customer: do nothing in ledger
            }
            DB::commit();
            $returnTo = route('sale.index');
            $invoiceUrl = route('saleReturn.invoice', $saleReturn->id) . '?autoprint=1&return_to=' . urlencode($returnTo);
            return redirect()->to($invoiceUrl)->with('success', 'Sale return saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Sale return failed: ' . $e->getMessage());
        }
    }


    public function salereturnview()
    {
        // Fetch all sale returns with the original sale and customer info
        $salesReturns = SalesReturn::with('sale.customer_relation')->orderBy('created_at', 'desc')->get();
        return view('admin_panel.sale.return.index', [
            'salesReturns' => $salesReturns,
        ]);
    }

        /**
     * Return comprehensive sale invoice details for the View More / Details ERP modal
     */
    public function saleDetails($id)
    {
        $sale = Sale::with(['user', 'branch', 'customer_relation', 'cardAccount'])->findOrFail($id);

        $prodIds = array_filter(array_map('trim', explode(',', $sale->product ?? '')));
        $variantIds = array_filter(array_map('trim', explode(',', $sale->variant_id ?? '')));
        $productsMap = \App\Models\Product::with('category_relation')->whereIn('id', $prodIds)->get()->keyBy('id');
        $variantsMap = \App\Models\ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

        $codes     = explode(',', $sale->product_code ?? '');
        $units     = explode(',', $sale->unit ?? '');
        $prices    = explode(',', $sale->per_price ?? '');
        $discounts = explode(',', $sale->per_discount ?? '');
        $qtys      = explode(',', $sale->qty ?? '');
        $totals    = explode(',', $sale->per_total ?? '');
        $rawVariants = explode(',', $sale->variant_id ?? '');

        $items = [];
        $totalLineDiscount = 0;
        foreach ($prodIds as $i => $pid) {
            $qty = (float)($qtys[$i] ?? 0);
            if ($qty == 0 && empty($pid)) continue;

            $p = $productsMap[$pid] ?? null;
            $vId = $rawVariants[$i] ?? null;
            $v = $vId ? ($variantsMap[$vId] ?? null) : null;
            $price = (float)($prices[$i] ?? 0);
            $disc = (float)($discounts[$i] ?? 0);
            $lineTot = (float)($totals[$i] ?? ($qty * $price - $disc));
            $totalLineDiscount += $disc;

            $pName = $p ? $p->item_name : 'Item #' . $pid;
            $vLabel = $v ? ($v->size_label ?: $v->variant_name) : '';
            $unit = $units[$i] ?? ($p ? $p->unit_type : 'Piece');
            $code = $codes[$i] ?? ($p ? ($p->barcode_path ?: $p->item_code) : '');

            $items[] = [
                'index' => $i + 1,
                'item_id' => $pid,
                'item_name' => $pName,
                'variant' => $vLabel,
                'item_code' => $code,
                'unit' => strtoupper($unit),
                'qty' => $qty,
                'price' => $price,
                'discount' => $disc,
                'total' => $lineTot,
            ];
        }

        $gross = (float)($sale->total_bill_amount ?: 0);
        $extraDiscount = (float)($sale->total_extradiscount ?: 0);
        $totalDiscount = $extraDiscount > 0 ? $extraDiscount : $totalLineDiscount;
        $net = (float)($sale->total_net ?: ($gross - $totalDiscount));

        $customerInfo = [
            'name' => $sale->customer_display_name,
            'type' => 'Walk-in Customer',
            'phone' => '-',
            'address' => '-',
        ];

        if ($sale->customer_relation) {
            $customerInfo['type'] = 'Registered Customer';
            $customerInfo['phone'] = $sale->customer_relation->mobile_no ?: ($sale->customer_relation->phone ?: '-');
            $customerInfo['address'] = $sale->customer_relation->address ?: '-';
        } elseif (str_starts_with((string)$sale->customer, 'dist_')) {
            $dist = \App\Models\Distributor::find(str_replace('dist_', '', $sale->customer));
            if ($dist) {
                $customerInfo['type'] = 'Distributor';
                $customerInfo['phone'] = $dist->phone ?: '-';
                $customerInfo['address'] = $dist->address ?: '-';
            }
        } elseif (str_starts_with((string)$sale->customer, 'booker_')) {
            $dist = \App\Models\Distributor::find(str_replace('booker_', '', $sale->customer));
            if ($dist) {
                $customerInfo['type'] = 'Order Booker';
                $customerInfo['phone'] = $dist->phone ?: '-';
                $customerInfo['address'] = $dist->address ?: '-';
            }
        } elseif (str_starts_with((string)$sale->customer, 'officer_')) {
            $so = \App\Models\SalesOfficer::find(str_replace('officer_', '', $sale->customer));
            if ($so) {
                $customerInfo['type'] = 'Sales Officer';
                $customerInfo['phone'] = $so->phone ?: '-';
            }
        } elseif (str_starts_with((string)$sale->customer, 'dc_')) {
            $dc = \App\Models\DistributorCustomer::find(str_replace('dc_', '', $sale->customer));
            if ($dc) {
                $customerInfo['type'] = 'Retail Shop / Outlet';
                $customerInfo['phone'] = $dc->phone_no ?: '-';
                $customerInfo['address'] = $dc->address ?: '-';
            }
        }

        $statusLabel = 'Completed';
        $statusBadgeClass = 'badge-erp-success';
        if ((string)$sale->sale_status === '2') {
            $statusLabel = 'Exchanged';
            $statusBadgeClass = 'badge-erp-exchange';
        } elseif ((string)$sale->sale_status === '1' || ($sale->returns_count ?? 0) > 0) {
            $statusLabel = 'Returned';
            $statusBadgeClass = 'badge-erp-return';
        }

        return response()->json([
            'success' => true,
            'sale' => [
                'id' => $sale->id,
                'invoice_no' => $sale->invoice_no,
                'date' => \Carbon\Carbon::parse($sale->created_at)->format('d M Y, h:i A'),
                'cashier' => $sale->user ? $sale->user->name : 'N/A',
                'order_type' => $sale->order_type ?: 'Walk-in',
                'reference' => $sale->reference ?: '-',
                'status' => $statusLabel,
                'status_class' => $statusBadgeClass,
                'customer' => $customerInfo,
                'branch' => $sale->branch ? $sale->branch->name : 'Main Store',
                'financial' => [
                    'gross' => $gross,
                    'line_discount' => $totalLineDiscount,
                    'extra_discount' => $extraDiscount,
                    'total_discount' => $totalDiscount,
                    'net' => $net,
                    'cash' => (float)($sale->cash ?: 0),
                    'card' => (float)($sale->card ?: 0),
                    'card_account' => $sale->cardAccount ? $sale->cardAccount->title : null,
                    'change' => (float)($sale->change ?: 0),
                ],
                'items' => $items,
                'urls' => [
                    'receipt' => route('sales.recepit', $sale->id),
                    'invoice' => route('sales.invoice', $sale->id),
                    'dc' => route('sales.dc', $sale->id),
                    'edit' => route('sales.edit', $sale->id),
                    'exchange' => route('sale.add', ['exchange_invoice' => $sale->invoice_no]),
                    'return' => route('sales.return.create', $sale->id),
                ]
            ]
        ]);
    }
    public function saleinvoice($id)
    {
        $sale = Sale::with(['customer_relation', 'branch'])->findOrFail($id);

        $saleReturn = \App\Models\SalesReturn::where('sale_id', $sale->id)->first();

        // 🔥 IMPORTANT: decide source
        $bill = $sale;
        // items
        $products  = explode(',', $sale->product);
        $codes     = explode(',', $sale->product_code);
        $brands    = explode(',', $sale->brand);
        $units     = explode(',', $sale->unit);
        $prices    = explode(',', $sale->per_price);
        $discounts = explode(',', $sale->per_discount);
        $qtys      = explode(',', $sale->qty);
        $totals    = explode(',', $sale->per_total);
        $variant_ids = explode(',', $sale->variant_id ?? '');

        $items = [];
        $productIds = array_unique($products);
        $productMap = Product::with('category_relation')->whereIn('id', $productIds)
            ->get()->keyBy('id'); // [id => ProductModel]
            
        $variantMap = \App\Models\ProductVariant::whereIn('id', array_filter($variant_ids))
            ->get()->keyBy('id'); 

        foreach ($products as $index => $p) {

            $qty = (float) ($qtys[$index] ?? 0);

            // ❌ skip empty/zero qty
            if ($qty == 0) {
                continue;
            }
            
            $vId = $variant_ids[$index] ?? '';
            $vModel = $vId ? ($variantMap[$vId] ?? null) : null;
            $variantName = $vModel ? ($vModel->size_label ?: $vModel->variant_name) : '';
            $productModel = $productMap[$p] ?? null;
            $displayName = $productModel ? $productModel->item_name : $p;
            $catName = ($productModel && $productModel->category_relation) ? $productModel->category_relation->name : 'Uncategorized';
            
            if ($variantName && $productModel) {
                // Remove redundant info like "(1.000 KG)" from variant name
                $cleanVariant = preg_replace('/\s*\([\d.]+\s*KG\)/i', '', $variantName);
                
                // Remove product name from variant name if it repeats
                $cleanVariant = trim(str_ireplace($productModel->item_name, '', $cleanVariant));
                $cleanVariant = ltrim($cleanVariant, ' -');
                
                if ($cleanVariant !== '') {
                    $displayName .= ' (' . $cleanVariant . ')';
                }
            } else if ($variantName) {
                $displayName .= ' - ' . $variantName;
            }

            $unit = $units[$index] ?? '';
            $price = (float)($prices[$index] ?? 0);

            // WEIGHT CONVERSION LOGIC
            if ($vModel && $productModel && strtolower($productModel->unit_type ?? '') === 'kg' && $vModel->size_value > 0) {
                $multiplier = 1;
                $sUnit = strtolower($vModel->size_unit ?? 'kg');
                if ($sUnit === 'kg') $multiplier = (float)$vModel->size_value;
                elseif (in_array($sUnit, ['gm','gram','grams'])) $multiplier = (float)$vModel->size_value / 1000;
                
                $qty = $qty * $multiplier;
                if ($multiplier > 0) $price = $price / $multiplier;
                $unit = 'KG';
            } else if (empty($unit) || is_numeric($unit)) {
                if ($vModel && !empty($vModel->size_unit)) {
                    $unit = strtoupper($vModel->size_unit);
                } else if ($productModel && !empty($productModel->unit_type)) {
                    $unit = strtoupper($productModel->unit_type);
                } else {
                    $unit = 'PIECE';
                }
            }

            $items[] = [
                'item_name' => $displayName,
                'category'  => $catName,
                'item_code' => $codes[$index] ?? '',
                'brand'     => $brands[$index] ?? '',
                'unit'      => $unit,
                'price'     => $price,
                'discount'  => (float) ($discounts[$index] ?? 0),
                'qty'       => $qty,
                'total'     => (float) ($totals[$index] ?? 0),
            ];
        }

        $mode = request('mode', 'invoice');

        // Override items if we only want to print the newly added ones (Diff)
        if ($mode === 'token_only' && session()->has('kot_items_json')) {
            $diffItems = json_decode(session('kot_items_json'), true);
            if (is_array($diffItems) && count($diffItems) > 0) {
                $filteredItems = [];
                foreach ($diffItems as $diff) {
                    $pid = $diff['prod_id'];
                    $vid = $diff['var_id'] ?? '';
                    $productModel = $productMap[$pid] ?? null;
                    $vModel = $vid ? ($variantMap[$vid] ?? null) : null;
                    $variantName = $vModel ? ($vModel->size_label ?: $vModel->variant_name) : '';

                    $displayName = $productModel ? $productModel->item_name : $pid;
                    if ($variantName) $displayName .= ' - ' . $variantName;

                    $catName = ($productModel && $productModel->category_relation) ? $productModel->category_relation->name : 'Uncategorized';

                    $filteredItems[] = [
                        'item_name' => $displayName,
                        'category'  => $catName,
                        'item_code' => '',
                        'brand'     => '',
                        'unit'      => '',
                        'price'     => (float) $diff['price'],
                        'discount'  => 0,
                        'qty'       => (float) $diff['qty'],
                        'total'     => (float) $diff['qty'] * (float) $diff['price'],
                    ];
                }
                $items = $filteredItems;
            }
        }

        $receiptBranch = $sale->branch 
            ?? ($sale->branch_id ? \App\Models\Branch::find($sale->branch_id) : null)
            ?? (auth()->check() && auth()->user()->branch_id ? \App\Models\Branch::find(auth()->user()->branch_id) : null)
            ?? \App\Models\Branch::find(active_branch_id())
            ?? \App\Models\Branch::first();

        return view('admin_panel.sale.saleinvoice', [
            'sale'          => $sale,
            'bill'          => $bill,        // 👈 unified object
            'saleItems'     => $items,
            'saleReturn'    => $saleReturn,
            'mode'          => $mode,
            'receiptBranch' => $receiptBranch,
        ]);
    }
    public function saleedit($id)
    {
        $sale = Sale::findOrFail($id);
        $branchId = $sale->branch_id ?? active_branch_id();

        // 1. Fetch Customers
        $customerQuery = Customer::where('status', '!=', 'inactive');
        if (!is_all_branches()) {
            $customerQuery->where(function($q) use ($branchId, $sale) {
                $q->where('branch_id', $branchId);
                if ($sale->customer && is_numeric($sale->customer)) {
                    $q->orWhere('id', $sale->customer);
                }
            });
        }
        $customers = $customerQuery->orderBy('customer_name')->get();

        // 2. Fetch Active Distributors
        $distributors = \App\Models\Distributor::where('status', 'active')
            ->where(function($q) {
                $q->where('type', 'distributor')->orWhereNull('type');
            })
            ->where('name', 'not like', '%booker%')
            ->orderBy('name')
            ->get();

        // 3. Fetch Active Order Bookers
        $orderBookers = \App\Models\Distributor::where('status', 'active')
            ->where(function($q) {
                $q->where('type', 'order_booker')->orWhere('name', 'like', '%booker%');
            })
            ->orderBy('name')
            ->get();

        // 4. Fetch Sales Officers
        $salesOfficers = \App\Models\SalesOfficer::orderBy('name')->get();

        // 5. Fetch Market Retail Customers
        $distributorCustomers = \App\Models\DistributorCustomer::where('status', 'active')
            ->orderBy('shop_name')
            ->get();

        // 6. Categories for quick panel
        $categories = \App\Models\Category::with(['products' => function($q) use ($branchId) {
            $q->with(['brand', 'variants', 'activeDiscount'])
              ->withSum(['stocks as total_stock' => function($sq) use ($branchId) {
                  $sq->where('branch_id', $branchId)->whereNull('warehouse_id');
              }], 'qty');
        }])->orderBy('name')->get();

        // 7. Fast In-Memory Product Catalog for 0ms instant search
        $allProducts = \App\Models\Product::with(['brand', 'variants', 'activeDiscount'])
            ->withSum(['stocks as total_stock' => function($sq) use ($branchId) {
                $sq->where('branch_id', $branchId)->whereNull('warehouse_id');
            }], 'qty')
            ->orderBy('item_name')
            ->get();

        $stockMap = \App\Models\Stock::where('branch_id', $branchId)
            ->whereNull('warehouse_id')
            ->get();

        $productsCatalog = [];
        foreach ($allProducts as $p) {
            $isKg = strtolower($p->unit_type ?? '') === 'kg';
            if ($p->variants && $p->variants->count() > 0) {
                foreach ($p->variants as $v) {
                    $vStockRow = $stockMap->first(function($s) use ($p, $v) {
                        return $s->product_id == $p->id && $s->variant_id == $v->id;
                    });
                    $stk = $vStockRow ? (float)$vStockRow->qty : (float)($v->stock_qty ?? 0);
                    if ($isKg) {
                        $stk = (float) $stockMap->where('product_id', $p->id)->sum('qty');
                    }
                    $sizeLabel = $v->size_label ?: $v->variant_name ?: '-';
                    $fullName = $p->item_name . ($sizeLabel !== '-' ? ' (' . $sizeLabel . ')' : '');

                    $productsCatalog[] = [
                        'id'               => $p->id,
                        'product_id'       => $p->id,
                        'variant_id'       => $v->id,
                        'item_name'        => $fullName,
                        'base_name'        => $p->item_name,
                        'size'             => $sizeLabel,
                        'color'            => $p->color ?: '-',
                        'unit_type'        => $p->unit_type ?: 'piece',
                        'item_code'        => $p->item_code,
                        'barcode'          => $v->barcode_path ?: $p->barcode_path ?: $p->item_code,
                        'brand'            => $p->brand?->name,
                        'price'            => (float)($v->price ?: $p->price),
                        'original_price'   => (float)($v->price ?: $p->price),
                        'stock'            => $stk,
                        'category_id'      => $p->category_id,
                    ];
                }
            } else {
                $price = (float) $p->price;
                if ($p->activeDiscount) {
                    $price = (float) $p->activeDiscount->final_price;
                }
                $stk = (float) ($p->total_stock ?? 0);

                $productsCatalog[] = [
                    'id'               => $p->id,
                    'product_id'       => $p->id,
                    'variant_id'       => null,
                    'item_name'        => $p->item_name,
                    'base_name'        => $p->item_name,
                    'size'             => '-',
                    'color'            => $p->color ?: '-',
                    'unit_type'        => $p->unit_type ?: 'piece',
                    'item_code'        => $p->item_code,
                    'barcode'          => $p->barcode_path ?: $p->item_code,
                    'brand'            => $p->brand?->name,
                    'price'            => $price,
                    'original_price'   => (float)$p->price,
                    'stock'            => $stk,
                    'category_id'      => $p->category_id,
                ];
            }
        }

        // 8. Decode Existing Sale Items for the Excel Grid
        $products   = explode(',', $sale->product);
        $codes      = explode(',', $sale->product_code);
        $brands     = explode(',', $sale->brand);
        $units      = explode(',', $sale->unit);
        $prices     = explode(',', $sale->per_price);
        $discounts  = explode(',', $sale->per_discount);
        $qtys       = explode(',', $sale->qty);
        $totals     = explode(',', $sale->per_total);
        $vIds       = explode(',', $sale->variant_id ?? '');

        $items = [];
        foreach ($products as $index => $pidOrName) {
            $pidOrName = trim($pidOrName);
            if ($pidOrName === '') continue;

            $product = is_numeric($pidOrName)
                ? Product::with('variants')->find($pidOrName)
                : Product::where('item_name', $pidOrName)->orWhere('item_code', trim($codes[$index] ?? ''))->first();

            $vId = $vIds[$index] ?? null;
            $variant = ($vId && $product) ? $product->variants->firstWhere('id', $vId) : null;
            $sizeLabel = $variant ? ($variant->size_label ?: $variant->variant_name ?: '-') : '-';
            $pName = $product ? $product->item_name : $pidOrName;
            $fullName = ($sizeLabel !== '-') ? ($pName . ' (' . $sizeLabel . ')') : $pName;

            $pStock = 0;
            if ($product) {
                $stockRow = $stockMap->first(function($s) use ($product, $vId) {
                    return $s->product_id == $product->id && ($vId ? $s->variant_id == $vId : true);
                });
                $pStock = $stockRow ? (float)$stockRow->qty : 0;
            }

            $disc = floatval($discounts[$index] ?? 0);
            $price = floatval($prices[$index] ?? 0);
            $qty = floatval($qtys[$index] ?? 1);
            $lineSubtotal = $price * $qty;

            $discType = 'pkr';
            $discVal = $disc;
            if ($lineSubtotal > 0 && $disc > 0 && ($disc / $lineSubtotal <= 1.0)) {
                $pct = ($disc / $lineSubtotal) * 100;
                if (abs($pct - round($pct)) < 0.01) {
                    $discType = 'percent';
                    $discVal = round($pct);
                }
            }

            $items[] = [
                'id'             => $product->id ?? $pidOrName,
                'product_id'     => $product->id ?? $pidOrName,
                'variant_id'     => $vId ?? '',
                'item_name'      => $fullName,
                'base_name'      => $pName,
                'code'           => $codes[$index] ?? ($product->item_code ?? ''),
                'barcode'        => $variant->barcode_path ?? $product->barcode_path ?? ($codes[$index] ?? ''),
                'stock'          => $pStock,
                'qty'            => $qty,
                'size'           => $sizeLabel,
                'color'          => '-',
                'pcs'            => $units[$index] ?? ($product->unit_type ?? 'piece'),
                'price'          => $price,
                'original_price' => $price,
                'disc_val'       => $discVal,
                'disc_type'      => $discType,
                'total'          => floatval($totals[$index] ?? ($lineSubtotal - $disc)),
            ];
        }

        $bankAccounts = \App\Models\Account::where('status', 1)->with('head')->orderBy('title')->get();
        $tables = \App\Models\Table::orderBy('table_name')->get();

        return view('admin_panel.sale.sale_screen', compact(
            'sale',
            'items',
            'customers',
            'distributors',
            'orderBookers',
            'salesOfficers',
            'distributorCustomers',
            'categories',
            'productsCatalog',
            'bankAccounts',
            'tables',
            'branchId'
        ));
    }

    public function updatesale(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // --- Arrays from request ---
            $product_ids    = $request->product_id ?? [];
            $variant_ids    = $request->variant_id ?? [];
            $product_names  = $request->product_name ?? [];
            $product_codes  = $request->item_code ?? [];
            $brands         = $request->brand ?? $request->uom ?? [];
            $units          = $request->unit ?? [];
            $prices         = $request->price ?? [];
            $discounts      = $request->item_disc ?? [];
            $quantities     = $request->qty ?? [];
            $totals         = $request->total ?? [];
            $colors         = $request->color ?? [];

            $combined_products    = [];
            $combined_codes       = [];
            $combined_brands      = [];
            $combined_units       = [];
            $combined_prices      = [];
            $combined_discounts   = [];
            $combined_qtys        = [];
            $combined_totals      = [];
            $combined_colors      = [];
            $combined_variant_ids = [];

            $total_items = 0;

            // --- Get old sale to update stock differences ---
            $sale = Sale::findOrFail($id);
            $saleBranchId = $sale->branch_id ?? active_branch_id();
            $old_quantities = explode(',', $sale->qty);
            $old_variant_ids = explode(',', $sale->variant_id ?? '');
            $old_product_ids = explode(',', $sale->product);

            // Create a map of existing items for easy diff: key = pid_vid
            $oldMap = [];
            foreach ($old_product_ids as $idx => $pid) {
                $pid = trim($pid);
                if (empty($pid)) continue;
                $vid = trim($old_variant_ids[$idx] ?? '');

                // Fallback: If product has variants but vid is empty, attempt to resolve default variant ID
                if (empty($vid)) {
                    $prodModel = \App\Models\Product::with('variants')->find($pid);
                    if ($prodModel && strtolower($prodModel->unit_type ?? '') !== 'kg' && $prodModel->variants->count() > 0) {
                        $defV = $prodModel->variants->where('is_default', 1)->first() ?? $prodModel->variants->first();
                        if ($defV) $vid = (string)$defV->id;
                    }
                }

                $key = $pid . '_' . ($vid ?: '0');
                $oldMap[$key] = ($oldMap[$key] ?? 0) + floatval($old_quantities[$idx] ?? 0);
            }

            foreach ($product_ids as $index => $product_id) {
                $product_id = trim($product_id);
                $vId   = trim($variant_ids[$index] ?? '');
                $qty   = isset($quantities[$index]) ? floatval($quantities[$index]) : 0;
                $price = isset($prices[$index]) ? floatval($prices[$index]) : 0;

                if (!$product_id || $qty <= 0 || $price <= 0) continue;

                $prodModel = \App\Models\Product::with('variants')->find($product_id);
                $isGram = $prodModel && strtolower($prodModel->unit_type ?? '') === 'kg';

                if (!$isGram && empty($vId) && $prodModel && $prodModel->variants->count() > 0) {
                    $defV = $prodModel->variants->where('is_default', 1)->first() ?? $prodModel->variants->first();
                    if ($defV) $vId = (string)$defV->id;
                }

                $key = $product_id . '_' . ($vId ?: '0');

                $combined_products[]    = $product_id;
                $combined_variant_ids[] = $vId;
                $combined_codes[]       = $product_codes[$index] ?? '';
                $combined_brands[]      = $brands[$index] ?? '';
                $combined_units[]       = $units[$index] ?? '';
                $combined_prices[]      = $prices[$index];
                $combined_discounts[]   = $discounts[$index] ?? 0;
                $combined_qtys[]        = $qty;
                $combined_totals[]      = $totals[$index] ?? 0;
                $combined_colors[]      = $colors[$index] ?? '';

                $total_items += $qty;

                // --- Update stock ---
                $old_qty = $oldMap[$key] ?? 0;
                $qty_diff = $qty - $old_qty;
                
                // Clear from map so we know it's handled
                unset($oldMap[$key]);

                $dbVariantId = $isGram ? null : ($vId ?: null);
                $deductQtyDiff = $qty_diff;

                if ($isGram) {
                    if (!empty($vId)) {
                        $vModel = \App\Models\ProductVariant::find($vId);
                        if ($vModel) {
                            $kgSize = floatval($vModel->size_value);
                            if (strtolower($vModel->size_unit ?? '') === 'kg') {
                                $deductQtyDiff = ($kgSize * $qty_diff * 1000);
                            } else {
                                $deductQtyDiff = ($kgSize * $qty_diff);
                            }
                        } else {
                            $deductQtyDiff = $qty_diff * 1000;
                        }
                    } else {
                        $deductQtyDiff = $qty_diff * 1000;
                    }
                }

                $stockQuery = \App\Models\Stock::where('product_id', $product_id)
                                                ->where('branch_id', $saleBranchId);

                if (!empty($sale->warehouse_id)) {
                    $stockQuery->where('warehouse_id', $sale->warehouse_id);
                } else {
                    $stockQuery->whereNull('warehouse_id');
                }

                if ($dbVariantId) {
                    $stockQuery->where('variant_id', $dbVariantId);
                } else {
                    $stockQuery->whereNull('variant_id');
                }
                $stock = $stockQuery->first();
                
                if ($stock) {
                    $stock->qty -= $deductQtyDiff;
                    $stock->save();
                } else {
                    \App\Models\Stock::create([
                        'branch_id'    => $saleBranchId,
                        'warehouse_id' => $sale->warehouse_id ?? null,
                        'product_id'   => $product_id,
                        'variant_id'   => $dbVariantId,
                        'qty'          => -$deductQtyDiff
                    ]);
                }
            }

            // Handle items that were removed (old qty becomes 0, so diff = -old_qty)
            foreach ($oldMap as $key => $old_qty) {
                if ($old_qty <= 0) continue;
                $parts = explode('_', $key);
                $pid = $parts[0];
                $vid = $parts[1] === '0' ? null : $parts[1];

                $prodModel = \App\Models\Product::find($pid);
                $isGram = $prodModel && strtolower($prodModel->unit_type ?? '') === 'kg';
                
                $dbVariantId = $isGram ? null : $vid;
                $addBackQty = $old_qty;

                if ($isGram) {
                    if ($vid) {
                        $vModel = \App\Models\ProductVariant::find($vid);
                        if ($vModel) {
                            $kgSize = floatval($vModel->size_value);
                            if (strtolower($vModel->size_unit ?? '') === 'kg') {
                                $addBackQty = ($kgSize * $old_qty * 1000);
                            } else {
                                $addBackQty = ($kgSize * $old_qty);
                            }
                        } else {
                            $addBackQty = $old_qty * 1000;
                        }
                    } else {
                        $addBackQty = $old_qty * 1000;
                    }
                }

                $stockQuery = \App\Models\Stock::where('product_id', $pid)
                                                ->where('branch_id', $saleBranchId);

                if (!empty($sale->warehouse_id)) {
                    $stockQuery->where('warehouse_id', $sale->warehouse_id);
                } else {
                    $stockQuery->whereNull('warehouse_id');
                }
                
                if ($dbVariantId) {
                    $stockQuery->where('variant_id', $dbVariantId);
                } else {
                    $stockQuery->whereNull('variant_id');
                }
                
                $stock = $stockQuery->first();
                if ($stock) {
                    $stock->qty += $addBackQty;
                    $stock->save();
                }
            }


            // --- Save updated Sale ---
            $old_total = $sale->total_net;
            $oldCard = (float) $sale->card;
            $oldCardAccountId = $sale->card_account_id;
            $wasCredited = !empty($sale->is_account_credited);

            $newCard = (float) ($request->card ?? 0);
            $newCardAccountId = !empty($request->card_account_id) ? $request->card_account_id : null;

            // Adjust bank account balance
            if ($wasCredited) {
                if ($oldCardAccountId && $oldCard > 0) {
                    $oldAcc = \App\Models\Account::find($oldCardAccountId);
                    if ($oldAcc) {
                        $oldAcc->opening_balance = (float)$oldAcc->opening_balance - $oldCard;
                        $oldAcc->save();
                    }
                }
                if ($newCardAccountId && $newCard > 0) {
                    $newAcc = \App\Models\Account::find($newCardAccountId);
                    if ($newAcc) {
                        $newAcc->opening_balance = (float)$newAcc->opening_balance + $newCard;
                        $newAcc->save();
                    }
                }
            } else {
                if ($newCardAccountId && $newCard > 0) {
                    $newAcc = \App\Models\Account::find($newCardAccountId);
                    if ($newAcc) {
                        $newAcc->opening_balance = (float)$newAcc->opening_balance + $newCard;
                        $newAcc->save();
                        $sale->is_account_credited = 1;
                    }
                }
            }

            $sale->customer            = $request->customer;
            $sale->reference           = $request->reference ?? '';
            $sale->product             = implode(',', $combined_products);
            $sale->variant_id          = implode(',', $combined_variant_ids);
            $sale->product_code        = implode(',', $combined_codes);
            $sale->brand               = implode(',', $combined_brands);
            $sale->unit                = implode(',', $combined_units);
            $sale->per_price           = implode(',', $combined_prices);
            $sale->per_discount        = implode(',', $combined_discounts);
            $sale->qty                 = implode(',', $combined_qtys);
            $sale->per_total           = implode(',', $combined_totals);
            $sale->color               = json_encode($combined_colors);
            $sale->total_amount_Words  = $request->total_amount_Words;
            $sale->total_bill_amount   = $request->total_subtotal;
            $sale->total_extradiscount = $request->total_extra_cost;
            $sale->total_net           = $request->total_net;
            $sale->cash                = $request->cash;
            $sale->card                = $newCard;
            $sale->card_account_id     = $newCardAccountId;
            $sale->change              = $request->change;
            $sale->total_items         = $total_items;
            $sale->save();

            // Also sync distributor payment if linked
            $distPayment = \App\Models\DistributorPayment::where('transaction_reference', $sale->invoice_no)->first();
            if ($distPayment) {
                $newPaidAmount = (float)($request->cash ?? 0) + $newCard;
                $distPayment->update([
                    'amount'         => $newPaidAmount,
                    'account_id'     => $newCardAccountId,
                    'payment_method' => ($request->cash > 0 && $newCard > 0 ? 'Split' : ($newCard > 0 ? 'Card' : 'Cash')),
                ]);
            }

            // --- Ledger Update ---
            $customer_id = $request->customer;
            $new_total = (float)$sale->total_net;
            $difference = $new_total - $old_total;

            if ($customer_id !== 'Walk-in Customer' && !empty($customer_id)) {
                if (str_starts_with($customer_id, 'dist_') || str_starts_with($customer_id, 'booker_')) {
                    $distId = (int) str_replace(['dist_', 'booker_'], '', $customer_id);
                    $distributor = \App\Models\Distributor::find($distId);
                    if ($distributor) {
                        $newBalance = (float)$distributor->current_balance + $difference;
                        \App\Models\DistributorLedger::create([
                            'distributor_id' => $distributor->id,
                            'date'           => $request->sale_date ?: now()->toDateString(),
                            'type'           => 'sale_edit',
                            'reference_no'   => $sale->invoice_no,
                            'description'    => "Updated Sale Invoice #{$sale->invoice_no} (Net adjustment: Rs. " . number_format($difference, 2) . ")",
                            'debit'          => $difference > 0 ? $difference : 0,
                            'credit'         => $difference < 0 ? abs($difference) : 0,
                            'balance'        => $newBalance,
                            'created_by'     => auth()->id(),
                        ]);
                        $distributor->update(['current_balance' => $newBalance]);
                    }
                } elseif (str_starts_with($customer_id, 'dc_')) {
                    $dcId = (int) str_replace('dc_', '', $customer_id);
                    $dc = \App\Models\DistributorCustomer::find($dcId);
                    if ($dc) {
                        $newBalance = (float)$dc->current_balance + $difference;
                        \App\Models\DistributorCustomerLedger::create([
                            'distributor_id'          => $dc->distributor_id ?? 1,
                            'distributor_customer_id' => $dc->id,
                            'date'                    => $request->sale_date ?: now()->toDateString(),
                            'type'                    => 'sale_edit',
                            'reference_no'            => $sale->invoice_no,
                            'description'             => "Updated Sale #{$sale->invoice_no} (Diff: Rs. {$difference})",
                            'debit'                   => $difference > 0 ? $difference : 0,
                            'credit'                  => $difference < 0 ? abs($difference) : 0,
                            'balance'                 => $newBalance,
                        ]);
                        $dc->update(['current_balance' => $newBalance]);
                    }
                } elseif (!str_starts_with($customer_id, 'officer_')) {
                    // Regular customer
                    $ledger = CustomerLedger::where('customer_id', $customer_id)->latest('id')->first();
                    if ($ledger) {
                        $ledger->previous_balance = $ledger->closing_balance;
                        $ledger->closing_balance  += $difference;
                        $ledger->save();
                    } else {
                        CustomerLedger::create([
                            'customer_id'      => $customer_id,
                            'admin_or_user_id' => auth()->id(),
                            'previous_balance' => 0,
                            'closing_balance'  => $new_total,
                            'opening_balance'  => $new_total,
                        ]);
                    }
                }
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'     => true,
                    'message'     => 'Sale [' . $sale->invoice_no . '] updated successfully.',
                    'sale_id'     => $sale->id,
                    'invoice_no'  => $sale->invoice_no,
                    'invoice_url' => route('sales.invoice', $sale->id) . '?return_to=' . urlencode(route('sale.screen')) . '&autoprint=1&mode=invoice',
                    'receipt_url' => route('sales.recepit', $sale->id) . '?return_to=' . urlencode(route('sale.screen')) . '&autoprint=1',
                    'redirect_to' => route('sale.screen'),
                ]);
            }

            $returnTo   = route('sale.screen');
            $invoiceUrl = route('sales.invoice', $sale->id)
                . '?return_to=' . urlencode($returnTo)
                . '&autoprint=1';

            return redirect()->to($invoiceUrl)->with('success', 'Sale updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 422);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    public function saledc($id)
    {
        $sale = Sale::with('customer_relation')->findOrFail($id);

        // Decode sale pivot or comma fields
        $products = explode(',', $sale->product);
        $codes    = explode(',', $sale->product_code);
        $brands   = explode(',', $sale->brand);
        $units    = explode(',', $sale->unit);
        $prices   = explode(',', $sale->per_price);
        $discounts = explode(',', $sale->per_discount);
        $qtys     = explode(',', $sale->qty);
        $totals   = explode(',', $sale->per_total);
        $colors_json = json_decode($sale->color, true);

        $items = [];

        foreach ($products as $index => $p) {
            $product = Product::where('item_name', trim($p))
                ->orWhere('item_code', trim($codes[$index] ?? ''))
                ->first();

            $items[] = [
                'product_id' => $product->id ?? '',
                'item_name'  => $product->item_name ?? $p,
                'item_code'  => $product->item_code ?? ($codes[$index] ?? ''),
                'brand'      => $product->brand->name ?? ($brands[$index] ?? ''),
                'unit'       => $product->unit ?? ($units[$index] ?? ''),
                'price'      => floatval($prices[$index] ?? 0),
                'discount'   => floatval($discounts[$index] ?? 0),
                'qty'        => intval($qtys[$index] ?? 1),
                'total'      => floatval($totals[$index] ?? 0),
                'color'      => isset($colors_json[$index]) ? json_decode($colors_json[$index], true) : [],
            ];
        }

        return view('admin_panel.sale.saledc', [
            'sale' => $sale,
            'saleItems' => $items,
        ]);
    }

    public function salerecepit($id)
    {
        $sale = Sale::with(['customer_relation', 'branch'])->findOrFail($id);

        // Decode sale pivot or comma fields
        $products = explode(',', $sale->product);
        $codes    = explode(',', $sale->product_code);
        $brands   = explode(',', $sale->brand);
        $units    = explode(',', $sale->unit);
        $prices   = explode(',', $sale->per_price);
        $discounts = explode(',', $sale->per_discount);
        $qtys     = explode(',', $sale->qty);
        $totals   = explode(',', $sale->per_total);
        $vids     = explode(',', $sale->variant_id ?? '');
        $colors_json = json_decode($sale->color, true);

        $vObjects = \App\Models\ProductVariant::whereIn('id', array_filter($vids))->get()->keyBy('id');

        $pObjects = Product::with('brand')->whereIn('id', array_filter($products))->get()->keyBy('id');

        foreach ($products as $index => $pId) {
            $product = $pObjects[$pId] ?? null;
            if (!$product) {
                // Fallback search by name if ID fails (for old data)
                $product = Product::where('item_name', trim($pId))
                    ->orWhere('item_code', trim($codes[$index] ?? ''))
                    ->first();
            }

            $vId = trim($vids[$index] ?? '');
            $vModel = $vId ? ($vObjects[$vId] ?? null) : null;
            $variantName = $vModel ? ($vModel->size_label ?: $vModel->variant_name) : '';
            
            $displayName = $product ? $product->item_name : $pId;
            if ($variantName && $product) {
                // Remove redundant info like "(1.000 KG)" from variant name
                $cleanVariant = preg_replace('/\s*\([\d.]+\s*KG\)/i', '', $variantName);
                
                // Remove product name from variant name if it repeats
                $cleanVariant = trim(str_ireplace($product->item_name, '', $cleanVariant));
                $cleanVariant = ltrim($cleanVariant, ' -');
                
                if ($cleanVariant !== '') {
                    $displayName .= ' (' . $cleanVariant . ')';
                }
            } else if ($variantName) {
                $displayName .= ' - ' . $variantName;
            }

            $price = floatval($prices[$index] ?? 0);
            $qty = floatval($qtys[$index] ?? 1);
            $unit = $units[$index] ?? '';

            if ($vModel && $product && strtolower($product->unit_type ?? '') === 'kg' && $vModel->size_value > 0) {
                $multiplier = 1;
                $sUnit = strtolower($vModel->size_unit ?? 'kg');
                if ($sUnit === 'kg') $multiplier = (float)$vModel->size_value;
                elseif (in_array($sUnit, ['gm','gram','grams'])) $multiplier = (float)$vModel->size_value / 1000;
                
                $qty = $qty * $multiplier;
                if ($multiplier > 0) $price = $price / $multiplier;
                $unit = 'KG';
            } else if (empty($unit) || is_numeric($unit)) {
                if ($vModel && !empty($vModel->size_unit)) {
                    $unit = strtoupper($vModel->size_unit);
                } else if ($product && !empty($product->unit_type)) {
                    $unit = strtoupper($product->unit_type);
                } else {
                    $unit = 'PIECE';
                }
            }

            $items[] = [
                'product_id' => $product->id ?? '',
                'item_name'  => $displayName,
                'item_code'  => $product ? $product->item_code : ($codes[$index] ?? ''),
                'brand'      => $product && $product->brand ? $product->brand->name : ($brands[$index] ?? ''),
                'unit'       => $unit ?: 'PIECE',
                'price'      => $price,
                'discount'   => floatval($discounts[$index] ?? 0),
                'qty'        => $qty,
                'total'      => floatval($totals[$index] ?? 0),
                'color'      => isset($colors_json[$index]) ? json_decode($colors_json[$index], true) : [],
            ];
        }

        $currentBranch = $sale->branch 
            ?? ($sale->branch_id ? \App\Models\Branch::find($sale->branch_id) : null)
            ?? (auth()->check() && auth()->user()->branch_id ? \App\Models\Branch::find(auth()->user()->branch_id) : null)
            ?? \App\Models\Branch::find(active_branch_id())
            ?? \App\Models\Branch::first();

        return view('admin_panel.sale.salerecepit', [
            'sale'          => $sale,
            'saleItems'     => $items,
            'currentBranch' => $currentBranch,
        ]);
    }

    public function retrninvoice($id)
    {
        $return = \App\Models\SalesReturn::with('sale.customer_relation')->findOrFail($id);

        $products   = explode(',', $return->product);
        $codes      = explode(',', $return->product_code);
        $brands     = explode(',', $return->brand);
        $units      = explode(',', $return->unit);
        $prices     = explode(',', $return->per_price);
        $discounts  = explode(',', $return->per_discount);
        $qtys       = explode(',', $return->qty);
        $totals     = explode(',', $return->per_total);
        $colors_json = json_decode($return->color, true);

        // Map variant_id from parent sale
        $parentSale = $return->sale;
        $sale_codes = $parentSale ? explode(',', $parentSale->product_code) : [];
        $sale_vids  = $parentSale ? explode(',', $parentSale->variant_id ?? '') : [];
        $codeToVid  = [];
        foreach ($sale_codes as $i => $c) {
            $codeToVid[trim($c)] = trim($sale_vids[$i] ?? '');
        }

        $items = [];

        foreach ($products as $index => $p) {

            $qty = floatval($qtys[$index] ?? 0);

            // ❌ qty 0 ya empty ho to skip
            if ($qty <= 0) {
                continue;
            }

            $code = trim($codes[$index] ?? '');
            $product = \App\Models\Product::with('category_relation', 'brand')->where('item_name', trim($p))
                ->orWhere('item_code', $code)
                ->first();

            $vId = $codeToVid[$code] ?? '';
            $vModel = $vId ? \App\Models\ProductVariant::find($vId) : null;
            $variantName = $vModel ? ($vModel->size_label ?: $vModel->variant_name) : '';

            $displayName = $product->item_name ?? $p;
            if ($variantName && $product) {
                $cleanVariant = preg_replace('/\s*\([\d.]+\s*KG\)/i', '', $variantName);
                $cleanVariant = trim(str_ireplace($product->item_name, '', $cleanVariant));
                $cleanVariant = ltrim($cleanVariant, ' -');
                if ($cleanVariant !== '') {
                    $displayName .= ' (' . $cleanVariant . ')';
                }
            } else if ($variantName) {
                $displayName .= ' - ' . $variantName;
            }

            $price = floatval($prices[$index] ?? 0);
            $unit = ($product && $product->unit_type) ? $product->unit_type : ($units[$index] ?? '');

            if ($vModel && $product && strtolower($product->unit_type ?? '') === 'kg' && $vModel->size_value > 0) {
                $multiplier = 1;
                $sUnit = strtolower($vModel->size_unit ?? 'kg');
                if ($sUnit === 'kg') $multiplier = (float)$vModel->size_value;
                elseif (in_array($sUnit, ['gm','gram','grams'])) $multiplier = (float)$vModel->size_value / 1000;
                
                $qty = $qty * $multiplier;
                if ($multiplier > 0) $price = $price / $multiplier;
                $unit = 'KG';
            } else if (empty($unit) || is_numeric($unit)) {
                if ($vModel && !empty($vModel->size_unit)) {
                    $unit = strtoupper($vModel->size_unit);
                } else if ($product && !empty($product->unit_type)) {
                    $unit = strtoupper($product->unit_type);
                } else {
                    $unit = 'PIECE';
                }
            }

            $items[] = [
                'product_id' => $product->id ?? '',
                'item_name'  => $displayName,
                'category'   => ($product && $product->category_relation) ? $product->category_relation->name : 'Uncategorized',
                'item_code'  => $code,
                'brand'      => ($product && $product->brand) ? $product->brand->name : ($brands[$index] ?? ''),
                'unit'       => $unit,
                'price'      => $price,
                'discount'   => floatval($discounts[$index] ?? 0),
                'qty'        => $qty,
                'total'      => floatval($totals[$index] ?? 0),
                'color'      => isset($colors_json[$index])
                    ? json_decode($colors_json[$index], true)
                    : [],
            ];
        }

        $unitTotals = [
            'Pc'  => 0,
            'Mtr' => 0,
            'Yd'  => 0,
        ];

        foreach ($items as $item) {
            $unit = strtolower($item['unit']);

            if (in_array($unit, ['pc', 'pcs', 'piece'])) {
                $unitTotals['Pc'] += $item['qty'];
            }

            if (in_array($unit, ['mtr', 'meter'])) {
                $unitTotals['Mtr'] += $item['qty'];
            }

            if (in_array($unit, ['yd', 'yard'])) {
                $unitTotals['Yd'] += $item['qty'];
            }
        }

        return view('admin_panel.sale.return.salereturnrecepit', [
            'return' => $return,
            'returnItems' => $items,
            'unitTotals'   => $unitTotals,
        ]);
    }
}

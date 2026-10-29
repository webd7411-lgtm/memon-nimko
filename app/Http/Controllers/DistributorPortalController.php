<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\DistributorOrder;
use App\Models\DistributorOrderItem;
use App\Models\DistributorStock;
use App\Models\DistributorCustomer;
use App\Models\DistributorCustomerLedger;
use App\Models\DistributorSale;
use App\Models\DistributorSaleItem;
use App\Models\DistributorLedger;
use App\Models\DistributorPayment;
use App\Models\Account;
use App\Models\AccountHead;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DistributorShopBooking;
use App\Models\DistributorShopBookingItem;
use App\Models\User;
use App\Models\ProductVariant;
use App\Models\DistributorClaim;
use App\Models\DistributorClaimItem;
use Illuminate\Support\Facades\Hash;


class DistributorPortalController extends Controller
{
    /**
     * Helper to retrieve currently authenticated distributor profile.
     */
    protected function getDistributor()
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }
        // Order Booker direct distributor lookup
        if ($user->usertype === 'order_booker') {
            if ($user->distributor_id) {
                $bookerDist = Distributor::find($user->distributor_id);
                if ($bookerDist) {
                    return $bookerDist;
                }
            }
            abort(403, 'Order Booker account is not linked to any active distributor.');
        }


        $isAdmin = false;
        try {
            $isAdmin = ($user->email === 'admin@admin.com') || ($user->usertype === 'admin') || (method_exists($user, 'hasRole') && $user->hasRole(['Super Admin', 'super-admin']));
        } catch (\Throwable $e) {
            $isAdmin = ($user->email === 'admin@admin.com') || ($user->usertype === 'admin');
        }

        // Allow Admin to view any distributor via request parameter or session
        if ($isAdmin) {
            if (request()->has('distributor_id')) {
                $chosen = Distributor::find(request('distributor_id'));
                if ($chosen) {
                    session(['active_portal_distributor_id' => $chosen->id]);
                    return $chosen;
                }
            }

            if (session()->has('active_portal_distributor_id')) {
                $sessionDist = Distributor::find(session('active_portal_distributor_id'));
                if ($sessionDist) {
                    return $sessionDist;
                }
            }
        }

        // Check if distributor relation exists for current user
        $distributor = Distributor::where('user_id', $user->id)->first();

        // For Super Admin / Admin testing portal: if user's distributor is null or has zero records, fallback to active distributor
        if ($isAdmin) {
            if (!$distributor || $distributor->code === 'DIST-0001') {
                $activeDist = Distributor::where('id', '!=', 1)->first();
                if ($activeDist) {
                    return $activeDist;
                }
            }
        }

        if (!$distributor && ($user->email === 'admin@admin.com' || $isAdmin)) {
            $distributor = Distributor::first();
            if (!$distributor) {
                // Auto create demo distributor if none exists
                $distributor = Distributor::create([
                    'user_id'         => $user->id,
                    'name'            => 'Main Company Distributor',
                    'code'            => 'DIST-0001',
                    'phone'           => '0300-0000000',
                    'city'            => 'Karachi',
                    'area'            => 'Saddar',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                    'status'          => 'active',
                ]);
            }
        }

        if (!$distributor) {
            abort(403, 'No active distributor profile linked to this user account.');
        }

        return $distributor;
    }

    /**
     * Enforce strict role boundary: Order Bookers can ONLY access booking pages.
     */
    protected function enforceBookerRestriction()
    {
        $user = Auth::user();
        if ($user && $user->usertype === 'order_booker') {
            $rName = request()->route() ? request()->route()->getName() : '';
            if (!str_starts_with($rName, 'distributor.booker.') && $rName !== 'logout') {
                return redirect()->route('distributor.booker.create')->send();
            }
        }
    }


    /**
     * Distributor Portal Dashboard.
     */
    public function dashboard(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $user = Auth::user();
        $isAdmin = false;
        try {
            $isAdmin = ($user->email === 'admin@admin.com') || ($user->usertype === 'admin') || (method_exists($user, 'hasRole') && $user->hasRole(['Super Admin', 'super-admin']));
        } catch (\Throwable $e) {
            $isAdmin = ($user->email === 'admin@admin.com') || ($user->usertype === 'admin');
        }

        $allDistributors = $isAdmin ? Distributor::where('status', 'active')->orderBy('name')->get() : collect();

        // Date Filter (Matching Super Admin)
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // 1. Company Ledger & Settlement (Head Office Account)
        $companyLedgerEntries = DistributorLedger::where('distributor_id', $distributor->id)->get();
        $totalGoodsFromCompany = (float) $companyLedgerEntries->sum('debit');
        $totalPaidToCompany = (float) $companyLedgerEntries->sum('credit');
        $payableBalanceToCompany = (float) $distributor->current_balance;

        // 2. Retail Market & Trade Receivables (Field Operations)
        $retailCustomers = DistributorCustomer::where('distributor_id', $distributor->id)->get();
        $totalCustomers = $retailCustomers->count();
        $totalMarketDue = (float) $retailCustomers->sum('current_balance');
        $customersWithDueCount = $retailCustomers->where('current_balance', '>', 0)->count();

        $totalCustomerRecovery = (float) DistributorCustomerLedger::where('distributor_id', $distributor->id)
            ->where('type', 'payment_received')
            ->sum('credit');

        // Van Custody Stock
        $stockItems = DistributorStock::with('product')
            ->where('distributor_id', $distributor->id)
            ->get();
        $totalStockUnits = (float) $stockItems->sum('qty');
        $stockValuation = (float) $stockItems->sum(function($s) {
            return (float)$s->qty * (float)($s->avg_price ?: 0);
        });

        // 3. Secondary Sales Metrics
        $todaySales = (float) DistributorSale::where('distributor_id', $distributor->id)
            ->whereDate('sale_date', now()->format('Y-m-d'))
            ->sum('grand_total');

        $monthSales = (float) DistributorSale::where('distributor_id', $distributor->id)
            ->whereBetween('sale_date', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')])
            ->sum('grand_total');

        $periodSales = (float) DistributorSale::where('distributor_id', $distributor->id)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->sum('grand_total');

        $periodSalesCount = DistributorSale::where('distributor_id', $distributor->id)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->count();

        $allSales = DistributorSale::where('distributor_id', $distributor->id)->get();
        $totalSalesCount = $allSales->count();
        $totalSalesSum = (float) $allSales->sum('grand_total');
        $averageSaleValue = $totalSalesCount > 0 ? ($totalSalesSum / $totalSalesCount) : 0;

        // Demand Orders
        $pendingDemandsCount = DistributorOrder::where('distributor_id', $distributor->id)
            ->where('status', 'pending')
            ->count();
        $approvedDemandsCount = DistributorOrder::where('distributor_id', $distributor->id)
            ->where('status', 'approved')
            ->count();
        $totalDemandsCount = DistributorOrder::where('distributor_id', $distributor->id)->count();

        // 4. Trend Charts (Last 14 Days)
        $salesTrendLabels = [];
        $salesTrendData = [];
        $recoveryTrendData = [];

        for ($i = 13; $i >= 0; $i--) {
            $dayDate = now()->subDays($i)->format('Y-m-d');
            $salesTrendLabels[] = now()->subDays($i)->format('d M');

            $daySaleVal = (float) DistributorSale::where('distributor_id', $distributor->id)
                ->whereDate('sale_date', $dayDate)
                ->sum('grand_total');

            $dayRecVal = (float) DistributorCustomerLedger::where('distributor_id', $distributor->id)
                ->where('type', 'payment_received')
                ->whereDate('date', $dayDate)
                ->sum('credit');

            $salesTrendData[] = $daySaleVal;
            $recoveryTrendData[] = $dayRecVal;
        }

        // 5. Stock Breakdown for Donut/Bar Chart
        $stockBreakdownLabels = [];
        $stockBreakdownData = [];
        foreach ($stockItems as $st) {
            $name = $st->product->item_name ?? ('Item #' . $st->product_id);
            $stockBreakdownLabels[] = $name;
            $stockBreakdownData[] = (float) $st->qty;
        }

        // 6. Action Lists
        $recentRecoveries = DistributorCustomerLedger::with('customer')
            ->where('distributor_id', $distributor->id)
            ->where('type', 'payment_received')
            ->latest('id')
            ->take(6)
            ->get();

        $recentSales = DistributorSale::with('customer')
            ->where('distributor_id', $distributor->id)
            ->latest('id')
            ->take(6)
            ->get();

        $recentOrders = DistributorOrder::where('distributor_id', $distributor->id)
            ->latest('id')
            ->take(5)
            ->get();

        $topRetailers = DistributorCustomer::where('distributor_id', $distributor->id)
            ->orderByDesc('current_balance')
            ->take(5)
            ->get();

        $topStockItems = DistributorStock::with('product')
            ->where('distributor_id', $distributor->id)
            ->orderByDesc('qty')
            ->take(5)
            ->get();

        return view('distributor_panel.dashboard', compact(
            'distributor',
            'isAdmin',
            'allDistributors',
            'startDate',
            'endDate',
            'periodSales',
            'periodSalesCount',
            'totalGoodsFromCompany',
            'totalPaidToCompany',
            'payableBalanceToCompany',
            'totalMarketDue',
            'customersWithDueCount',
            'totalCustomerRecovery',
            'totalStockUnits',
            'stockValuation',
            'todaySales',
            'monthSales',
            'totalSalesCount',
            'totalSalesSum',
            'averageSaleValue',
            'totalCustomers',
            'pendingDemandsCount',
            'approvedDemandsCount',
            'totalDemandsCount',
            'salesTrendLabels',
            'salesTrendData',
            'recoveryTrendData',
            'stockBreakdownLabels',
            'stockBreakdownData',
            'recentRecoveries',
            'recentSales',
            'recentOrders',
            'topRetailers',
            'topStockItems'
        ));
    }

    /**
     * View stock in distributor custody.
     */
    public function myStock(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $query = DistributorStock::with(['product.unit', 'variant'])
            ->where('distributor_id', $distributor->id);

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%");
            });
        }

        $stocks = $query->orderBy('qty', 'desc')->paginate(20);
        $totalItems = $stocks->total();
        $totalQty = DistributorStock::where('distributor_id', $distributor->id)->sum('qty');

        return view('distributor_panel.stock.index', compact('distributor', 'stocks', 'totalItems', 'totalQty'));
    }

    /**
     * List of purchase orders / demands placed to company.
     */
    public function myOrders(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $query = DistributorOrder::with(['items.product'])
            ->where('distributor_id', $distributor->id);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest('id')->paginate(15);

        return view('distributor_panel.orders.index', compact('distributor', 'orders'));
    }

    /**
     * Form to create a new purchase demand to Super Admin.
     */
    public function createOrder()
    {
        $distributor = $this->getDistributor();
        $products = Product::with(['unit', 'variants'])->orderBy('item_name')->get();

        return view('distributor_panel.orders.create', compact('distributor', 'products'));
    }

    /**
     * Store new purchase order / demand request.
     */
    public function storeOrder(Request $request)
    {
        $distributor = $this->getDistributor();

        $request->validate([
            'product_id'     => 'required|array|min:1',
            'product_id.*'   => 'required|exists:products,id',
            'requested_qty'  => 'required|array|min:1',
            'requested_qty.*'=> 'required|numeric|min:0.01',
            'notes'          => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $totalQty = 0;
            $totalAmount = 0;
            $itemsData = [];

            foreach ($request->product_id as $index => $productId) {
                $qty = (float) ($request->requested_qty[$index] ?? 0);
                if ($qty <= 0) continue;

                $variantId = $request->variant_id[$index] ?? null;
                $product = Product::find($productId);
                $unitPrice = (float) ($product->wholesale_price ?? $product->price ?? 0);
                $subtotal = $qty * $unitPrice;

                $totalQty += $qty;
                $totalAmount += $subtotal;

                $itemsData[] = [
                    'product_id'    => $productId,
                    'variant_id'    => $variantId,
                    'requested_qty' => $qty,
                    'approved_qty'  => 0,
                    'unit_price'    => $unitPrice,
                    'subtotal'      => $subtotal,
                ];
            }

            if (empty($itemsData)) {
                return back()->withInput()->with('error', 'Please enter at least one item quantity.');
            }

            $order = DistributorOrder::create([
                'order_no'       => DistributorOrder::generateOrderNo(),
                'distributor_id' => $distributor->id,
                'user_id'        => Auth::id(),
                'order_date'     => now()->format('Y-m-d'),
                'total_items'    => count($itemsData),
                'total_qty'      => $totalQty,
                'total_amount'   => $totalAmount,
                'status'         => 'pending',
                'notes'          => $request->notes,
            ]);

            foreach ($itemsData as $item) {
                $item['distributor_order_id'] = $order->id;
                DistributorOrderItem::create($item);
            }

            DB::commit();
            return redirect()->route('distributor.orders.index')->with('success', "Order #{$order->order_no} submitted successfully to Head Office!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error submitting demand: ' . $e->getMessage());
        }
    }

    /**
     * Show demand request details.
     */
    public function showOrder($id)
    {
        $distributor = $this->getDistributor();
        $order = DistributorOrder::with(['items.product.unit', 'items.variant'])
            ->where('distributor_id', $distributor->id)
            ->findOrFail($id);

        return view('distributor_panel.orders.show', compact('distributor', 'order'));
    }

    /**
     * List of retail customers / shops belonging to this distributor.
     */
    public function myCustomers(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $query = DistributorCustomer::where('distributor_id', $distributor->id);

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('shop_name', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest('id')->paginate(15);
        $totalCustomers = $customers->total();
        $totalReceivable = DistributorCustomer::where('distributor_id', $distributor->id)->sum('current_balance');

        return view('distributor_panel.customers.index', compact('distributor', 'customers', 'totalCustomers', 'totalReceivable'));
    }

    /**
     * Store new retail customer.
     */
    public function storeCustomer(Request $request)
    {
        $distributor = $this->getDistributor();

        $request->validate([
            'shop_name'       => 'required|string|max:255',
            'owner_name'      => 'nullable|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'area'            => 'nullable|string|max:100',
            'address'         => 'nullable|string',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        $openingBalance = (float) ($request->opening_balance ?? 0);

        DB::beginTransaction();
        try {
            $customer = DistributorCustomer::create([
                'distributor_id'  => $distributor->id,
                'shop_name'       => $request->shop_name,
                'owner_name'      => $request->owner_name,
                'phone'           => $request->phone,
                'area'            => $request->area,
                'address'         => $request->address,
                'opening_balance' => $openingBalance,
                'current_balance' => $openingBalance,
                'status'          => 'active',
            ]);

            if ($openingBalance > 0) {
                DistributorCustomerLedger::create([
                    'distributor_customer_id' => $customer->id,
                    'distributor_id'          => $distributor->id,
                    'date'                    => now()->format('Y-m-d'),
                    'type'                    => 'opening',
                    'reference_no'            => 'OP-' . $customer->id,
                    'description'             => 'Opening Balance',
                    'debit'                   => $openingBalance,
                    'credit'                  => 0,
                    'balance'                 => $openingBalance,
                ]);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'customer' => $customer,
                    'message' => "Customer '{$customer->shop_name}' added successfully!"
                ]);
            }

            return back()->with('success', "Customer '{$customer->shop_name}' added successfully!");
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error adding customer: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()->with('error', 'Error adding customer: ' . $e->getMessage());
        }
    }

    /**
     * Dedicated POS / Sale Screen for Distributor to sell to Retailers.
     */
    public function createSale()
    {
        $distributor = $this->getDistributor();

        // Get registered retail customers belonging to distributor
        $customers = DistributorCustomer::where('distributor_id', $distributor->id)
            ->where('status', 'active')
            ->orderBy('shop_name')
            ->get();

        // Get products available in distributor's inventory
        $stocks = DistributorStock::with(['product.unit', 'product.category_relation', 'variant'])
            ->where('distributor_id', $distributor->id)
            ->where('qty', '>', 0)
            ->get();

        // Extract categories for quick panel
        $categoryIds = $stocks->pluck('product.category_id')->filter()->unique();
        $categories = \App\Models\Category::whereIn('id', $categoryIds)->get();

        $invoiceNo = DistributorSale::generateInvoiceNo();
        // ONLY load distributor's own active accounts (Super Admin accounts are NEVER loaded)
        $bankAccounts = \App\Models\Account::where('status', 1)
            ->where('distributor_id', $distributor->id)
            ->get();
        $accountHeads = \App\Models\AccountHead::where('status', 1)->get();

        return view('distributor_panel.sales.create', compact('distributor', 'customers', 'stocks', 'invoiceNo', 'categories', 'bankAccounts', 'accountHeads'));
    }

    /**
     * Store secondary sale to retailer.
     */
    public function storeSale(Request $request)
    {
        $distributor = $this->getDistributor();

        $request->validate([
            'distributor_customer_id' => 'required|exists:distributor_customers,id',
            'product_id'              => 'required|array|min:1',
            'product_id.*'            => 'nullable',
            'qty'                     => 'required|array|min:1',
            'qty.*'                   => 'nullable|numeric|min:0',
            'unit_price'              => 'nullable|array',
            'price'                   => 'nullable|array',
            'discount'                => 'nullable|numeric|min:0',
            'paid_amount'             => 'nullable|numeric|min:0',
            'payment_type'            => 'nullable|in:cash,credit,partial',
        ]);

        DB::beginTransaction();
        try {
            $customer = DistributorCustomer::where('distributor_id', $distributor->id)
                ->findOrFail($request->distributor_customer_id);

            $subtotal = 0;
            $itemsData = [];

            foreach ($request->product_id as $index => $productId) {
                if (empty($productId)) continue;
                $qty = (float) ($request->qty[$index] ?? 0);
                $price = (float) ($request->unit_price[$index] ?? $request->price[$index] ?? 0);
                $variantId = $request->variant_id[$index] ?? null;

                if ($qty <= 0) continue;

                // Check distributor stock
                $distStock = DistributorStock::where('distributor_id', $distributor->id)
                    ->where('product_id', $productId)
                    ->when($variantId, fn($q) => $q->where('variant_id', $variantId))
                    ->first();

                if (!$distStock || (float)$distStock->qty < $qty) {
                    $prodName = Product::find($productId)->item_name ?? 'Item';
                    $avail = $distStock ? (float)$distStock->qty : 0;
                    throw new \Exception("Insufficient van stock for '{$prodName}'. Available: {$avail}, Requested: {$qty}");
                }

                $itemSubtotal = $qty * $price;
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'qty'        => $qty,
                    'unit_price' => $price,
                    'subtotal'   => $itemSubtotal,
                    'dist_stock' => $distStock,
                ];
            }

            if (empty($itemsData)) {
                throw new \Exception('No valid items in sale. Please add at least one product with quantity.');
            }

            $discount = (float) ($request->discount ?? 0);
            $grandTotal = max(0, $subtotal - $discount);
            $paidAmount = min($grandTotal, (float) ($request->paid_amount ?? 0));
            $dueAmount = max(0, $grandTotal - $paidAmount);

            // Determine payment_type if not sent explicitly
            $paymentType = $request->payment_type;
            if (!$paymentType) {
                if ($paidAmount >= $grandTotal && $grandTotal > 0) {
                    $paymentType = 'cash';
                } elseif ($paidAmount > 0) {
                    $paymentType = 'partial';
                } else {
                    $paymentType = 'credit';
                }
            }

            // 1. Create Sale
            $accountId = null;
            $accountDesc = "Cash";
            if (!empty($request->payment_account) && is_array($request->payment_account)) {
                $firstAcc = $request->payment_account[0];
                if (is_numeric($firstAcc)) {
                    $distAcc = Account::where('id', (int)$firstAcc)->where('distributor_id', $distributor->id)->first();
                    if ($distAcc) {
                        $accountId = $distAcc->id;
                        $accountDesc = $distAcc->title;
                    }
                }
            }

            // 1. Create Sale
            $sale = DistributorSale::create([
                'invoice_no'              => DistributorSale::generateInvoiceNo(),
                'distributor_id'          => $distributor->id,
                'distributor_customer_id' => $customer->id,
                'sale_date'               => now()->format('Y-m-d'),
                'subtotal'                => $subtotal,
                'discount'                => $discount,
                'grand_total'             => $grandTotal,
                'paid_amount'             => $paidAmount,
                'due_amount'              => $dueAmount,
                'payment_type'            => $paymentType,
                'account_id'              => $accountId,
                'notes'                   => $request->notes,
            ]);

            // 2. Deduct distributor stock & save items
            foreach ($itemsData as $row) {
                DistributorSaleItem::create([
                    'distributor_sale_id' => $sale->id,
                    'product_id'          => $row['product_id'],
                    'variant_id'          => $row['variant_id'],
                    'qty'                 => $row['qty'],
                    'unit_price'          => $row['unit_price'],
                    'subtotal'            => $row['subtotal'],
                ]);

                $distStock = $row['dist_stock'];
                $distStock->qty = max(0, (float)$distStock->qty - $row['qty']);
                $distStock->save();
            }

            // 3. Update Retailer Ledger (Debit for sale total)
            $newCustBalance = (float) $customer->current_balance + $grandTotal;
            DistributorCustomerLedger::create([
                'distributor_customer_id' => $customer->id,
                'distributor_id'          => $distributor->id,
                'date'                    => now()->format('Y-m-d'),
                'type'                    => 'sale',
                'reference_no'            => $sale->invoice_no,
                'description'             => "Invoice #{$sale->invoice_no} (Items: " . count($itemsData) . ")",
                'debit'                   => $grandTotal,
                'credit'                  => 0,
                'balance'                 => $newCustBalance,
            ]);

            // 4. If Paid amount > 0, Credit entry in Retailer Ledger
            if ($paidAmount > 0) {
                $newCustBalance = $newCustBalance - $paidAmount;
                DistributorCustomerLedger::create([
                    'distributor_customer_id' => $customer->id,
                    'distributor_id'          => $distributor->id,
                    'date'                    => now()->format('Y-m-d'),
                    'type'                    => 'payment_received',
                    'reference_no'            => 'RCP-' . $sale->invoice_no,
                    'description'             => "Payment received via {$accountDesc} for Invoice #{$sale->invoice_no}",
                    'debit'                   => 0,
                    'credit'                  => $paidAmount,
                    'balance'                 => $newCustBalance,
                ]);
            }

            $customer->update(['current_balance' => $newCustBalance]);

            DB::commit();
            return redirect()->route('distributor.sales.receipt', $sale->id)->with('success', "Sale invoice #{$sale->invoice_no} created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Printable invoice / receipt for the retailer.
     */
    public function saleReceipt($id)
    {
        $distributor = $this->getDistributor();
        $sale = DistributorSale::with(['customer', 'items.product.unit', 'items.variant'])
            ->where('distributor_id', $distributor->id)
            ->findOrFail($id);

        return view('distributor_panel.sales.receipt', compact('distributor', 'sale'));
    }

    /**
     * History of sales made by distributor.
     */
    public function salesList(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $query = DistributorSale::with('customer')
            ->where('distributor_id', $distributor->id);

        if ($request->customer_id) {
            $query->where('distributor_customer_id', $request->customer_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('sale_date', [$request->start_date, $request->end_date]);
        }

        $sales = $query->latest('sale_date')->latest('id')->paginate(15);
        $customers = DistributorCustomer::where('distributor_id', $distributor->id)->get();

        return view('distributor_panel.sales.index', compact('distributor', 'sales', 'customers'));
    }

    /**
     * Retailer customer ledger view & payment collection.
     */
    public function customerLedger($id, Request $request)
    {
        $distributor = $this->getDistributor();
        $customer = DistributorCustomer::where('distributor_id', $distributor->id)->findOrFail($id);

        $query = DistributorCustomerLedger::where('distributor_customer_id', $customer->id)
            ->where('distributor_id', $distributor->id);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $entries = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        $totalDebit = $entries->sum('debit');
        $totalCredit = $entries->sum('credit');

        return view('distributor_panel.customers.ledger', compact('distributor', 'customer', 'entries', 'totalDebit', 'totalCredit'));
    }

    /**
     * Record cash collected from retail customer.
     */
    public function storeCustomerPayment(Request $request, $id)
    {
        $distributor = $this->getDistributor();
        $customer = DistributorCustomer::where('distributor_id', $distributor->id)->findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date'   => 'required|date',
            'notes'  => 'nullable|string',
        ]);

        $amount = (float) $request->amount;

        DB::beginTransaction();
        try {
            $newBalance = (float) $customer->current_balance - $amount;

            DistributorCustomerLedger::create([
                'distributor_customer_id' => $customer->id,
                'distributor_id'          => $distributor->id,
                'date'                    => $request->date,
                'type'                    => 'payment_received',
                'reference_no'            => 'REC-' . date('ymd') . '-' . rand(100, 999),
                'description'             => "Payment collection. " . ($request->notes ? "Notes: {$request->notes}" : ''),
                'debit'                   => 0,
                'credit'                  => $amount,
                'balance'                 => $newBalance,
            ]);

            $customer->update(['current_balance' => $newBalance]);

            DB::commit();
            return back()->with('success', "Payment of Rs. " . number_format($amount, 2) . " received from '{$customer->shop_name}'!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * Distributor's statement of account with Head Office / Company.
     */
    public function companyLedger(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $query = DistributorLedger::where('distributor_id', $distributor->id);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $entries = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        $totalGoodsReceived = $entries->sum('debit');
        $totalPaidToCompany = $entries->sum('credit');
        $currentBalance = (float) $distributor->current_balance;

        return view('distributor_panel.company_ledger.index', compact('distributor', 'entries', 'totalGoodsReceived', 'totalPaidToCompany', 'currentBalance'));
    }

    /**
     * Display payments made/submitted to Company / Head Office.
     */
    public function payments(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $query = DistributorPayment::where('distributor_id', $distributor->id)
            ->with(['approver', 'account.head'])
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
        }

        $payments = $query->paginate(20)->withQueryString();

        $totalApproved = DistributorPayment::where('distributor_id', $distributor->id)->where('status', 'approved')->sum('amount');
        $totalPending = DistributorPayment::where('distributor_id', $distributor->id)->where('status', 'pending')->sum('amount');
        $pendingCount = DistributorPayment::where('distributor_id', $distributor->id)->where('status', 'pending')->count();
        $currentBalance = (float) $distributor->current_balance;

        // Auto fetch active company Cash & Bank accounts from Chart of Accounts
        $companyAccounts = Account::where('status', 1)
            ->whereNull('distributor_id')
            ->with('head')
            ->orderBy('head_id')
            ->orderBy('title')
            ->get();

        return view('distributor_panel.payments.index', compact(
            'distributor',
            'payments',
            'totalApproved',
            'totalPending',
            'pendingCount',
            'currentBalance',
            'companyAccounts'
        ));
    }

    /**
     * Submit a payment made to Company / Head Office.
     */
    public function storePayment(Request $request)
    {
        $distributor = $this->getDistributor();

        $request->validate([
            'amount'                => 'required|numeric|min:1',
            'payment_date'          => 'required|date',
            'account_id'            => 'nullable|exists:accounts,id',
            'payment_method'        => 'nullable|string|max:50',
            'bank_name'             => 'nullable|string|max:100',
            'account_number'        => 'nullable|string|max:100',
            'transaction_reference' => 'nullable|string|max:100',
            'slip_image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'notes'                 => 'nullable|string|max:1000',
        ]);

        $accountId = $request->account_id ? (int)$request->account_id : null;
        $bankName = $request->bank_name;
        $accountNumber = $request->account_number;
        $paymentMethod = $request->payment_method ?: 'bank_transfer';

        // Auto populate bank & account details if selected from company accounts
        if ($accountId) {
            $selectedAccount = Account::with('head')->find($accountId);
            if ($selectedAccount) {
                $headName = strtolower($selectedAccount->head->name ?? '');
                $accTitle = strtolower($selectedAccount->title ?? '');
                if (!$request->filled('payment_method')) {
                    $paymentMethod = (str_contains($headName, 'cash') || str_contains($accTitle, 'cash')) ? 'cash' : 'bank_transfer';
                }
                if (empty($bankName)) {
                    $bankName = $selectedAccount->title;
                }
                if (empty($accountNumber)) {
                    $accountNumber = $selectedAccount->account_code;
                }
            }
        }

        $slipFileName = null;
        if ($request->hasFile('slip_image')) {
            $file = $request->file('slip_image');
            $slipFileName = 'slip_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/distributor_payments'), $slipFileName);
        }

        $payment = DistributorPayment::create([
            'payment_no'            => DistributorPayment::generatePaymentNo(),
            'distributor_id'        => $distributor->id,
            'amount'                => (float) $request->amount,
            'payment_date'          => $request->payment_date,
            'payment_method'        => $paymentMethod,
            'account_id'            => $accountId,
            'bank_name'             => $bankName,
            'account_number'        => $accountNumber,
            'transaction_reference' => $request->transaction_reference,
            'slip_image'            => $slipFileName,
            'notes'                 => $request->notes,
            'status'                => 'pending',
            'created_by'            => Auth::id(),
        ]);

        return redirect()->route('distributor.payments.index')
            ->with('success', "Payment #{$payment->payment_no} of Rs. " . number_format($request->amount, 2) . " submitted successfully! It will be verified and approved by Head Office.");
    }

    /**
     * Update an unapproved payment submission.
     * Approved payments CANNOT be edited by distributors (only Super Admin).
     */
    public function updatePayment(Request $request, $id)
    {
        $distributor = $this->getDistributor();
        $payment = DistributorPayment::where('distributor_id', $distributor->id)->findOrFail($id);

        if ($payment->status === 'approved') {
            return back()->with('error', 'Approved payments cannot be edited by distributor. Only Super Admin has permission to modify approved payments.');
        }

        $request->validate([
            'amount'                => 'required|numeric|min:1',
            'payment_date'          => 'required|date',
            'account_id'            => 'nullable|exists:accounts,id',
            'payment_method'        => 'nullable|string|max:50',
            'bank_name'             => 'nullable|string|max:100',
            'account_number'        => 'nullable|string|max:100',
            'transaction_reference' => 'nullable|string|max:100',
            'slip_image'            => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
            'notes'                 => 'nullable|string|max:1000',
        ]);

        $accountId = $request->account_id ? (int)$request->account_id : null;
        $bankName = $request->bank_name;
        $accountNumber = $request->account_number;
        $paymentMethod = $request->payment_method ?: $payment->payment_method;

        if ($accountId) {
            $selectedAccount = Account::with('head')->find($accountId);
            if ($selectedAccount) {
                $headName = strtolower($selectedAccount->head->name ?? '');
                $accTitle = strtolower($selectedAccount->title ?? '');
                if (!$request->filled('payment_method')) {
                    $paymentMethod = (str_contains($headName, 'cash') || str_contains($accTitle, 'cash')) ? 'cash' : 'bank_transfer';
                }
                if (empty($bankName)) {
                    $bankName = $selectedAccount->title;
                }
                if (empty($accountNumber)) {
                    $accountNumber = $selectedAccount->account_code;
                }
            }
        }

        $slipFileName = $payment->slip_image;
        if ($request->hasFile('slip_image')) {
            if ($payment->slip_image && file_exists(public_path('uploads/distributor_payments/' . $payment->slip_image))) {
                @unlink(public_path('uploads/distributor_payments/' . $payment->slip_image));
            }
            $file = $request->file('slip_image');
            $slipFileName = 'slip_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/distributor_payments'), $slipFileName);
        }

        $payment->update([
            'amount'                => (float) $request->amount,
            'payment_date'          => $request->payment_date,
            'payment_method'        => $paymentMethod,
            'account_id'            => $accountId,
            'bank_name'             => $bankName,
            'account_number'        => $accountNumber,
            'transaction_reference' => $request->transaction_reference,
            'slip_image'            => $slipFileName,
            'notes'                 => $request->notes,
            'status'                => 'pending',
            'rejection_reason'      => null,
        ]);

        return redirect()->route('distributor.payments.index')
            ->with('success', "Payment #{$payment->payment_no} updated successfully!");
    }

    /**
     * Delete an unapproved payment submission.
     * Approved payments CANNOT be deleted.
     */
    public function destroyPayment($id)
    {
        $distributor = $this->getDistributor();
        $payment = DistributorPayment::where('distributor_id', $distributor->id)->findOrFail($id);

        if ($payment->status === 'approved') {
            return back()->with('error', 'Approved payments cannot be deleted as they have already been credited to ledger.');
        }

        if ($payment->slip_image && file_exists(public_path('uploads/distributor_payments/' . $payment->slip_image))) {
            @unlink(public_path('uploads/distributor_payments/' . $payment->slip_image));
        }

        $paymentNo = $payment->payment_no;
        $payment->delete();

        return redirect()->route('distributor.payments.index')
            ->with('success', "Payment submission #{$paymentNo} has been deleted successfully.");
    }

    /**
     * Chart of Accounts for Distributor.
     */
    public function accountsList()
    {
        $distributor = $this->getDistributor();

        $accounts = Account::with('head')
            ->where('distributor_id', $distributor->id)
            ->latest('id')
            ->get();

        $heads = AccountHead::where('status', 1)->get();

        $stats = [
            'total_accounts'  => $accounts->count(),
            'bank_accounts'   => $accounts->filter(fn($a) => str_contains(strtolower($a->head->name ?? ''), 'bank'))->count(),
            'cash_accounts'   => $accounts->filter(fn($a) => !str_contains(strtolower($a->head->name ?? ''), 'bank'))->count(),
            'opening_balance' => $accounts->sum('opening_balance'),
        ];

        return view('distributor_panel.accounts.index', compact('distributor', 'accounts', 'heads', 'stats'));
    }

    /**
     * Store new or update existing account for distributor.
     */
    public function storeAccount(Request $request)
    {
        $distributor = $this->getDistributor();

        $request->validate([
            'head_id'         => 'required|exists:account_heads,id',
            'title'           => 'required|string|max:150',
            'account_code'    => 'nullable|string|max:50',
            'account_number'  => 'nullable|string|max:100',
            'bank_name'       => 'nullable|string|max:100',
            'branch_name'     => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric',
        ]);

        $status = $request->has('status') ? 1 : 0;
        $accCode = $request->account_code ?: ('D' . $distributor->id . '-ACC-' . rand(1000, 9999));

        if ($request->account_id) {
            $account = Account::where('distributor_id', $distributor->id)->findOrFail($request->account_id);
            $account->update([
                'head_id'         => $request->head_id,
                'account_code'    => $request->account_code ?: $account->account_code,
                'title'           => $request->title,
                'account_number'  => $request->account_number,
                'bank_name'       => $request->bank_name,
                'branch_name'     => $request->branch_name,
                'opening_balance' => $request->opening_balance ?? 0,
                'status'          => $status,
            ]);

            return redirect()->route('distributor.accounts.index')->with('success', 'Account updated successfully.');
        }

        Account::create([
            'distributor_id'  => $distributor->id,
            'head_id'         => $request->head_id,
            'account_code'    => $accCode,
            'title'           => $request->title,
            'account_number'  => $request->account_number,
            'bank_name'       => $request->bank_name,
            'branch_name'     => $request->branch_name,
            'type'            => 'Debit',
            'opening_balance' => $request->opening_balance ?? 0,
            'status'          => 1,
        ]);

        return redirect()->route('distributor.accounts.index')->with('success', 'Account added successfully to your Chart of Accounts.');
    }

    /**
     * Quick store account via AJAX from the secondary POS sale screen.
     */
    public function storeQuickAccount(Request $request)
    {
        $distributor = $this->getDistributor();

        $request->validate([
            'title'           => 'required|string|max:150',
            'head_id'         => 'nullable|exists:account_heads,id',
            'account_number'  => 'nullable|string|max:100',
            'bank_name'       => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric',
        ]);

        $headId = $request->head_id;
        if (!$headId) {
            $bankHead = AccountHead::where('name', 'like', '%Bank%')->first();
            $headId = $bankHead ? $bankHead->id : (AccountHead::first()->id ?? 1);
        }

        $cleanSlug = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->title), 0, 6));
        $accCode = 'D' . $distributor->id . '-' . ($cleanSlug ?: 'ACC') . '-' . rand(100, 999);

        $account = Account::create([
            'distributor_id'  => $distributor->id,
            'head_id'         => $headId,
            'account_code'    => $accCode,
            'title'           => $request->title,
            'account_number'  => $request->account_number,
            'bank_name'       => $request->bank_name ?? $request->title,
            'type'            => 'Debit',
            'opening_balance' => $request->opening_balance ?? 0,
            'status'          => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully!',
            'account' => [
                'id'             => $account->id,
                'title'          => $account->title,
                'account_code'   => $account->account_code,
                'account_number' => $account->account_number,
            ],
        ]);
    }

    /**
     * Delete account owned by distributor.
     */
    public function destroyAccount($id)
    {
        $distributor = $this->getDistributor();
        $account = Account::where('distributor_id', $distributor->id)->findOrFail($id);

        $usedInSales = DistributorSale::where('account_id', $account->id)->exists();
        if ($usedInSales) {
            return back()->with('error', 'Cannot delete this account because secondary sales have been recorded against it.');
        }

        $account->delete();
        return redirect()->route('distributor.accounts.index')->with('success', 'Account deleted successfully.');
    }



    // ==========================================
    // ORDER BOOKER MANAGEMENT (Under Distributor)
    // ==========================================
    public function bookersList(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $query = User::where('distributor_id', $distributor->id)
            ->where('usertype', 'order_booker');

        if ($request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $bookers = $query->latest()->paginate(15);

        // Stats
        $todayBookingsCount = DistributorShopBooking::where('distributor_id', $distributor->id)
            ->whereDate('booking_date', now()->format('Y-m-d'))
            ->count();
        $todayBookingsTotal = (float) DistributorShopBooking::where('distributor_id', $distributor->id)
            ->whereDate('booking_date', now()->format('Y-m-d'))
            ->sum('grand_total');

        return view('distributor_panel.bookers.index', compact('distributor', 'bookers', 'todayBookingsCount', 'todayBookingsTotal'));
    }

    public function storeBooker(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'usertype'       => 'order_booker',
            'distributor_id' => $distributor->id,
            'branch_id'      => $distributor->user ? $distributor->user->branch_id : null,
        ]);

        try {
            $user->assignRole('Distributor');
        } catch (\Throwable $e) {}

        return redirect()->route('distributor.bookers.index')->with('success', "Order Booker '{$request->name}' registered successfully! They can now log in.");
    }

    public function destroyBooker($id)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();
        $booker = User::where('distributor_id', $distributor->id)
            ->where('usertype', 'order_booker')
            ->findOrFail($id);

        $hasBookings = DistributorShopBooking::where('booker_user_id', $booker->id)->exists();
        if ($hasBookings) {
            $booker->usertype = 'inactive_booker';
            $booker->save();
            return redirect()->route('distributor.bookers.index')->with('success', 'Order Booker marked inactive (bookings history preserved).');
        }

        $booker->delete();
        return redirect()->route('distributor.bookers.index')->with('success', 'Order Booker removed successfully.');
    }

    // ==========================================
    // ORDER BOOKING INTERFACE (Booker takes order)
    // ==========================================
    public function createShopBooking(Request $request)
    {
        $distributor = $this->getDistributor();
        $user = Auth::user();

        // Retail shops assigned to this distributor
        $customers = DistributorCustomer::where('distributor_id', $distributor->id)
            ->where('status', 'active')
            ->orderBy('shop_name')
            ->get();

        // Products catalog
        $products = Product::with(['unit', 'variants', 'category_relation'])
            ->orderBy('item_name')
            ->get();

        // Stock availability map for indicators
        $stockMap = DistributorStock::where('distributor_id', $distributor->id)
            ->get()
            ->keyBy(function($st) {
                return $st->product_id . '_' . ($st->variant_id ?? 0);
            });

        $bookingNo = DistributorShopBooking::generateBookingNo();
        $isBookerUser = ($user->usertype === 'order_booker');

        return view('distributor_panel.booker.create', compact('distributor', 'customers', 'products', 'stockMap', 'bookingNo', 'isBookerUser'));
    }

    public function getShopCreditInfo($id)
    {
        $distributor = $this->getDistributor();
        $customer = DistributorCustomer::where('distributor_id', $distributor->id)->findOrFail($id);

        $balance = (float) $customer->current_balance;

        return response()->json([
            'success'         => true,
            'id'              => $customer->id,
            'shop_name'       => $customer->shop_name,
            'owner_name'      => $customer->owner_name ?: 'N/A',
            'phone'           => $customer->phone ?: 'N/A',
            'area'            => $customer->area ?: 'N/A',
            'address'         => $customer->address ?: 'N/A',
            'current_balance' => $balance,
            'formatted_due'   => 'Rs ' . number_format(abs($balance), 2),
            'has_due'         => $balance > 0,
            'due_status'      => $balance > 0 ? 'Outstanding Balance Due' : ($balance < 0 ? 'Advance Balance Available' : 'Account Clear / Zero Balance'),
            'badge_class'     => $balance > 0 ? 'bg-danger' : ($balance < 0 ? 'bg-info' : 'bg-success'),
        ]);
    }

    public function storeShopBooking(Request $request)
    {
        $distributor = $this->getDistributor();

        $request->validate([
            'distributor_customer_id' => 'required|exists:distributor_customers,id',
            'product_id'              => 'required|array|min:1',
            'qty'                     => 'required|array|min:1',
            'unit_price'              => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $customer = DistributorCustomer::where('distributor_id', $distributor->id)
                ->findOrFail($request->distributor_customer_id);

            $subtotal = 0;
            $totalQty = 0;
            $itemsData = [];

            foreach ($request->product_id as $index => $pId) {
                if (empty($pId)) continue;
                $qty = (float) ($request->qty[$index] ?? 0);
                $price = (float) ($request->unit_price[$index] ?? 0);
                $variantId = $request->variant_id[$index] ?? null;

                if ($qty <= 0) continue;

                $lineSubtotal = $qty * $price;
                $subtotal += $lineSubtotal;
                $totalQty += $qty;

                $itemsData[] = [
                    'product_id' => $pId,
                    'variant_id' => $variantId,
                    'qty'        => $qty,
                    'unit_price' => $price,
                    'subtotal'   => $lineSubtotal,
                ];
            }

            if (empty($itemsData)) {
                throw new \Exception('Please add at least one product with quantity to book order.');
            }

            $discount = (float) ($request->discount ?? 0);
            $grandTotal = max(0, $subtotal - $discount);

            $booking = DistributorShopBooking::create([
                'booking_no'              => DistributorShopBooking::generateBookingNo(),
                'distributor_id'          => $distributor->id,
                'booker_user_id'          => Auth::id(),
                'distributor_customer_id' => $customer->id,
                'booking_date'            => $request->booking_date ?? now()->toDateString(),
                'total_items'             => count($itemsData),
                'total_qty'               => $totalQty,
                'subtotal'                => $subtotal,
                'discount'                => $discount,
                'grand_total'             => $grandTotal,
                'status'                  => 'booked',
                'notes'                   => $request->notes,
            ]);

            foreach ($itemsData as $it) {
                DistributorShopBookingItem::create([
                    'booking_id' => $booking->id,
                    'product_id' => $it['product_id'],
                    'variant_id' => $it['variant_id'],
                    'qty'        => $it['qty'],
                    'unit_price' => $it['unit_price'],
                    'subtotal'   => $it['subtotal'],
                ]);
            }

            // NOTE: Stock is NOT deducted!
            // Customer Ledger is NOT debited!
            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success'    => true,
                    'message'    => "Booking {$booking->booking_no} recorded successfully for {$customer->shop_name}!",
                    'booking_id' => $booking->id,
                    'booking_no' => $booking->booking_no,
                ]);
            }

            return redirect()->route('distributor.booker.my_bookings')->with('success', "Order #{$booking->booking_no} successfully booked for {$customer->shop_name}! It is now sent to Distributor for dispatch.");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', 'Error recording booking: ' . $e->getMessage());
        }
    }

    public function myShopBookings(Request $request)
    {
        $distributor = $this->getDistributor();
        $user = Auth::user();

        $query = DistributorShopBooking::with(['customer', 'items.product', 'items.variant'])
            ->where('distributor_id', $distributor->id);

        if ($user->usertype === 'order_booker') {
            $query->where('booker_user_id', $user->id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->latest('id')->paginate(15);
        $isBookerUser = ($user->usertype === 'order_booker');

        return view('distributor_panel.booker.my_bookings', compact('distributor', 'bookings', 'isBookerUser'));
    }

    public function showShopBookingReceipt($id)
    {
        $distributor = $this->getDistributor();
        $booking = DistributorShopBooking::with(['customer', 'booker', 'items.product.unit', 'items.variant'])
            ->where('distributor_id', $distributor->id)
            ->findOrFail($id);

        return view('distributor_panel.booker.receipt', compact('distributor', 'booking'));
    }

    // ==========================================
    // DISTRIBUTOR VIEW & EXECUTION OF BOOKINGS
    // ==========================================
    public function shopBookingsList(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $query = DistributorShopBooking::with(['customer', 'booker', 'sale', 'items.product.unit', 'items.variant'])
            ->where('distributor_id', $distributor->id);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->booker_id) {
            $query->where('booker_user_id', $request->booker_id);
        }

        if ($request->customer_id) {
            $query->where('distributor_customer_id', $request->customer_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('booking_date', [$request->start_date, $request->end_date]);
        }

        $bookings = $query->latest('id')->paginate(15);

        // Stats
        $pendingBookingsCount = DistributorShopBooking::where('distributor_id', $distributor->id)->where('status', 'booked')->count();
        $pendingBookingsAmount = (float) DistributorShopBooking::where('distributor_id', $distributor->id)->where('status', 'booked')->sum('grand_total');
        $deliveredTodayCount = DistributorShopBooking::where('distributor_id', $distributor->id)->where('status', 'delivered')->whereDate('converted_at', now()->format('Y-m-d'))->count();
        $deliveredTodayAmount = (float) DistributorShopBooking::where('distributor_id', $distributor->id)->where('status', 'delivered')->whereDate('converted_at', now()->format('Y-m-d'))->sum('grand_total');

        $bookers = User::where('distributor_id', $distributor->id)->where('usertype', 'order_booker')->get();
        $customers = DistributorCustomer::where('distributor_id', $distributor->id)->orderBy('shop_name')->get();

        return view('distributor_panel.shop_bookings.index', compact('distributor', 'bookings', 'bookers', 'customers', 'pendingBookingsCount', 'pendingBookingsAmount', 'deliveredTodayCount', 'deliveredTodayAmount'));
    }

    public function showShopBookingDetails($id)
    {
        $distributor = $this->getDistributor();
        $booking = DistributorShopBooking::with(['customer', 'booker', 'items.product.unit', 'items.variant', 'sale'])
            ->where('distributor_id', $distributor->id)
            ->findOrFail($id);

        // Check stock availability for each item
        $stockCheck = [];
        $canDeliver = true;
        foreach ($booking->items as $item) {
            $st = DistributorStock::where('distributor_id', $distributor->id)
                ->where('product_id', $item->product_id)
                ->when($item->variant_id, fn($q) => $q->where('variant_id', $item->variant_id))
                ->first();
            $avail = $st ? (float)$st->qty : 0;
            $stockCheck[$item->id] = [
                'available' => $avail,
                'is_ok'     => $avail >= (float)$item->qty,
            ];
            if ($avail < (float)$item->qty) {
                $canDeliver = false;
            }
        }

        return response()->json([
            'success'     => true,
            'booking'     => $booking,
            'stock_check' => $stockCheck,
            'can_deliver' => $canDeliver,
        ]);
    }

    public function deliverShopBooking(Request $request, $id)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $booking = DistributorShopBooking::with(['items', 'customer'])
            ->where('distributor_id', $distributor->id)
            ->findOrFail($id);

        if ($booking->status === 'delivered') {
            return back()->with('error', 'This booking has already been delivered and converted to sale.');
        }

        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Cannot deliver a cancelled booking.');
        }

        DB::beginTransaction();
        try {
            $customer = $booking->customer;

            // 1. Verify and deduct stock for all items
            foreach ($booking->items as $item) {
                $distStock = DistributorStock::where('distributor_id', $distributor->id)
                    ->where('product_id', $item->product_id)
                    ->when($item->variant_id, fn($q) => $q->where('variant_id', $item->variant_id))
                    ->first();

                if (!$distStock || (float)$distStock->qty < (float)$item->qty) {
                    $prodName = Product::find($item->product_id)->item_name ?? 'Item';
                    $avail = $distStock ? (float)$distStock->qty : 0;
                    throw new \Exception("Insufficient stock in your custody for '{$prodName}'. Available: {$avail}, Booked: {$item->qty}. Please replenish stock first.");
                }

                $distStock->qty = (float)$distStock->qty - (float)$item->qty;
                $distStock->save();
            }

            // 2. Create DistributorSale (Invoice)
            $paidAmount = (float) ($request->paid_amount ?? 0);
            $grandTotal = (float) $booking->grand_total;
            $dueAmount = max(0, $grandTotal - $paidAmount);
            $paymentType = $paidAmount >= $grandTotal ? 'cash' : ($paidAmount > 0 ? 'partial' : 'credit');

            $invoiceNo = DistributorSale::generateInvoiceNo();
            $bookerName = $booking->booker ? $booking->booker->name : 'Order Booker';

            $sale = DistributorSale::create([
                'invoice_no'              => $invoiceNo,
                'distributor_id'          => $distributor->id,
                'distributor_customer_id' => $customer->id,
                'sale_date'               => now()->toDateString(),
                'subtotal'                => $booking->subtotal,
                'discount'                => $booking->discount,
                'grand_total'             => $grandTotal,
                'paid_amount'             => $paidAmount,
                'due_amount'              => $dueAmount,
                'payment_type'            => $paymentType,
                'notes'                   => "Delivered from Booking #{$booking->booking_no} (Booked by: {$bookerName})",
            ]);

            // 3. Create Sale Items
            foreach ($booking->items as $item) {
                DistributorSaleItem::create([
                    'distributor_sale_id' => $sale->id,
                    'product_id'          => $item->product_id,
                    'variant_id'          => $item->variant_id,
                    'qty'                 => $item->qty,
                    'unit_price'          => $item->unit_price,
                    'subtotal'            => $item->subtotal,
                ]);
            }

            // 4. Update Customer Balance & Post to Retailer Ledger
            $newBalance = (float)$customer->current_balance + $dueAmount;
            $customer->current_balance = $newBalance;
            $customer->save();

            // Ledger Entry: Sale (Debit)
            DistributorCustomerLedger::create([
                'distributor_customer_id' => $customer->id,
                'distributor_id'          => $distributor->id,
                'date'                    => now()->toDateString(),
                'type'                    => 'sale',
                'reference_no'            => $invoiceNo,
                'description'             => "Delivered goods (Booking #{$booking->booking_no})",
                'debit'                   => $grandTotal,
                'credit'                  => 0,
                'balance'                 => (float)$customer->current_balance - $dueAmount + $grandTotal,
            ]);

            // If cash paid on delivery, Ledger Entry: Payment Received (Credit)
            if ($paidAmount > 0) {
                DistributorCustomerLedger::create([
                    'distributor_customer_id' => $customer->id,
                    'distributor_id'          => $distributor->id,
                    'date'                    => now()->toDateString(),
                    'type'                    => 'payment_received',
                    'reference_no'            => 'REC-' . $invoiceNo,
                    'description'             => "Cash received on delivery ({$invoiceNo})",
                    'debit'                   => 0,
                    'credit'                  => $paidAmount,
                    'balance'                 => $newBalance,
                ]);
            }

            // 5. Update Booking status to delivered
            $booking->update([
                'status'            => 'delivered',
                'converted_sale_id' => $sale->id,
                'converted_at'      => now(),
                'converted_by'      => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('distributor.shop_bookings.index')->with('success', "Booking #{$booking->booking_no} successfully delivered! Invoice #{$invoiceNo} generated, stock deducted, and shop ledger updated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Delivery failed: ' . $e->getMessage());
        }
    }

    public function cancelShopBooking(Request $request, $id)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $booking = DistributorShopBooking::where('distributor_id', $distributor->id)->findOrFail($id);

        if ($booking->status === 'delivered') {
            return back()->with('error', 'Cannot cancel a booking that has already been delivered.');
        }

        $booking->update([
            'status' => 'cancelled',
            'notes'  => $booking->notes . ' [Cancelled on ' . now()->toDateTimeString() . ']',
        ]);

        return back()->with('success', "Booking #{$booking->booking_no} has been cancelled.");
    }

    // =========================================================================
    // DISTRIBUTOR CLAIMS & REIMBURSEMENTS (Portal)
    // =========================================================================

    /**
     * Display distributor's submitted claims and their status.
     */
    public function claims(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $query = DistributorClaim::where('distributor_id', $distributor->id)
            ->with(['items.product', 'approver'])
            ->latest('id');

        if ($request->filled('claim_type')) {
            $query->where('claim_type', $request->claim_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('claim_date', [$request->start_date, $request->end_date]);
        }

        $claims = $query->paginate(20)->withQueryString();

        // Statistics
        $totalClaimsCount = DistributorClaim::where('distributor_id', $distributor->id)->count();
        $pendingCount = DistributorClaim::where('distributor_id', $distributor->id)->where('status', 'pending')->count();
        $pendingAmount = DistributorClaim::where('distributor_id', $distributor->id)->where('status', 'pending')->sum('total_amount');
        $approvedAmount = DistributorClaim::where('distributor_id', $distributor->id)->where('status', 'approved')->sum('approved_amount');

        // Products for itemized damage / scheme picker
        $products = Product::select('id', 'item_name', 'price', 'wholesale_price')->orderBy('item_name')->get();

        return view('distributor_panel.claims.index', compact(
            'distributor', 'claims', 'totalClaimsCount', 'pendingCount', 'pendingAmount', 'approvedAmount', 'products'
        ));
    }

    /**
     * Submit a new claim to Company / Head Office.
     */
    public function storeClaim(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $request->validate([
            'claim_type'      => 'required|string|in:trade_discount,damage_stock,freight,target_incentive,other',
            'claim_date'      => 'required|date',
            'total_amount'    => 'required|numeric|min:0.01',
            'settlement_type' => 'required|in:credit_note,stock_replacement',
            'attachment'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'description'     => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $uploadDir = public_path('uploads/claims');
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $fileName = 'claim_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $fileName);
                $attachmentPath = 'uploads/claims/' . $fileName;
            }

            $claimNo = DistributorClaim::generateClaimNo();

            $claim = DistributorClaim::create([
                'claim_no'        => $claimNo,
                'distributor_id'  => $distributor->id,
                'claim_type'      => $request->claim_type,
                'claim_date'      => $request->claim_date,
                'total_amount'    => (float) $request->total_amount,
                'approved_amount' => 0.00,
                'settlement_type' => $request->settlement_type,
                'status'          => 'pending',
                'attachment'      => $attachmentPath,
                'description'     => $request->description,
                'submitted_by'    => Auth::id(),
            ]);

            // Save line items if provided
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    if (!empty($item['product_id']) || !empty($item['qty'])) {
                        $qty = (float)($item['qty'] ?? 1);
                        $rate = (float)($item['unit_rate'] ?? 0);
                        DistributorClaimItem::create([
                            'distributor_claim_id' => $claim->id,
                            'product_id'           => !empty($item['product_id']) ? $item['product_id'] : null,
                            'variant_id'           => !empty($item['variant_id']) ? $item['variant_id'] : null,
                            'batch_no'             => $item['batch_no'] ?? null,
                            'qty'                  => $qty,
                            'unit_rate'            => $rate,
                            'subtotal'             => (float)($item['subtotal'] ?? ($qty * $rate)),
                            'reason'               => $item['reason'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
            return back()->with('success', "Claim #{$claimNo} of Rs. " . number_format($request->total_amount, 2) . " submitted successfully to Head Office for verification!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error submitting claim: ' . $e->getMessage());
        }
    }

}
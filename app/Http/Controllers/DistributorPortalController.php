<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\DistributorOrder;
use App\Models\DistributorOrderItem;
use App\Models\DistributorStock;
use App\Models\DistributorCustomer;
use App\Models\DistributorCustomerTier;
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
use App\Models\DistributorSalesReturn;
use App\Models\DistributorSalesReturnItem;
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

        $totalStockValue = (float) DistributorStock::where('distributor_id', $distributor->id)
            ->selectRaw('COALESCE(SUM(qty * COALESCE(avg_price, 0)), 0) as val')
            ->value('val');

        return view('distributor_panel.stock.index', compact('distributor', 'stocks', 'totalItems', 'totalQty', 'totalStockValue'));
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
     * Edit demand order (Allowed only if status is pending).
     */
    public function editOrder($id)
    {
        $distributor = $this->getDistributor();
        $order = DistributorOrder::with(['items.product.unit', 'items.variant'])
            ->where('distributor_id', $distributor->id)
            ->findOrFail($id);

        if ($order->status !== 'pending') {
            return redirect()->route('distributor.orders.show', $order->id)
                ->with('error', 'Only pending demand orders can be modified.');
        }

        $products = Product::with(['unit', 'variants'])->orderBy('item_name')->get();

        return view('distributor_panel.orders.edit', compact('distributor', 'order', 'products'));
    }

    /**
     * Update demand order (Allowed only if status is pending).
     */
    public function updateOrder(Request $request, $id)
    {
        $distributor = $this->getDistributor();
        $order = DistributorOrder::where('distributor_id', $distributor->id)->findOrFail($id);

        if ($order->status !== 'pending') {
            return redirect()->route('distributor.orders.show', $order->id)
                ->with('error', 'Only pending demand orders can be edited.');
        }

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
                    'distributor_order_id' => $order->id,
                    'product_id'           => $productId,
                    'variant_id'           => $variantId,
                    'requested_qty'        => $qty,
                    'approved_qty'         => 0,
                    'unit_price'           => $unitPrice,
                    'subtotal'             => $subtotal,
                ];
            }

            if (empty($itemsData)) {
                return back()->withInput()->with('error', 'Please enter at least one item quantity.');
            }

            // Delete old items and insert updated ones
            $order->items()->delete();
            foreach ($itemsData as $item) {
                DistributorOrderItem::create($item);
            }

            $order->update([
                'total_items'  => count($itemsData),
                'total_qty'    => $totalQty,
                'total_amount' => $totalAmount,
                'notes'        => $request->notes,
            ]);

            DB::commit();
            return redirect()->route('distributor.orders.index')->with('success', "Order #{$order->order_no} updated successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating demand: ' . $e->getMessage());
        }
    }

    /**
     * Delete demand order (Allowed only if status is pending).
     */
    public function destroyOrder($id)
    {
        $distributor = $this->getDistributor();
        $order = DistributorOrder::where('distributor_id', $distributor->id)->findOrFail($id);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending demand orders can be deleted. This order is already ' . $order->status . '.');
        }

        DB::beginTransaction();
        try {
            $orderNo = $order->order_no;
            $order->items()->delete();
            $order->delete();

            DB::commit();
            return redirect()->route('distributor.orders.index')->with('success', "Demand order #{$orderNo} has been deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting demand order: ' . $e->getMessage());
        }
    }

    /**
     * List of retail customers / shops belonging to this distributor.
     */
    public function myCustomers(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $query = DistributorCustomer::with('tier')->where('distributor_id', $distributor->id);

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
        $bookers = User::where('distributor_id', $distributor->id)->where('usertype', 'order_booker')->get();

        return view('distributor_panel.customers.index', compact('distributor', 'customers', 'totalCustomers', 'totalReceivable', 'bookers'));
    }

    /**
     * Store new retail customer.
     */
    public function storeCustomer(Request $request)
    {
        $distributor = $this->getDistributor();

        $request->validate([
            'booker_user_id'    => Auth::user()->usertype === 'order_booker' ? 'nullable' : 'required|exists:users,id',
            'shop_name'         => 'required|string|max:255',
            'owner_name'        => 'nullable|string|max:255',
            'phone'             => 'nullable|string|max:20',
            'area'              => 'nullable|string|max:100',
            'address'           => 'nullable|string',
            'opening_balance'   => 'nullable|numeric|min:0',
            'approved_discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $openingBalance = (float) ($request->opening_balance ?? 0);
        $approvedDiscount = (float) ($request->approved_discount ?? 0);

        $bookerUserId = $request->booker_user_id ?: (Auth::user()->usertype === 'order_booker' ? Auth::id() : null);

        DB::beginTransaction();
        try {
            $customer = DistributorCustomer::create([
                'distributor_id'    => $distributor->id,
                'booker_user_id'    => $bookerUserId,
                'shop_name'         => $request->shop_name,
                'owner_name'        => $request->owner_name,
                'phone'             => $request->phone,
                'area'              => $request->area,
                'address'           => $request->address,
                'approved_discount' => 0.00, // Only Company Head Office can assign/approve shop discount
                'opening_balance'   => $openingBalance,
                'current_balance'   => $openingBalance,
                'status'            => 'active',
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
     * Update retail customer details (including approved discount).
     */
    public function updateCustomer(Request $request, $id)
    {
        $distributor = $this->getDistributor();
        $customer = DistributorCustomer::where('distributor_id', $distributor->id)->findOrFail($id);

        $request->validate([
            'booker_user_id'    => 'required|exists:users,id',
            'shop_name'         => 'required|string|max:255',
            'owner_name'        => 'nullable|string|max:255',
            'phone'             => 'nullable|string|max:20',
            'area'              => 'nullable|string|max:100',
            'address'           => 'nullable|string',
            'approved_discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $customer->update([
            'booker_user_id'    => $request->has('booker_user_id') ? ($request->booker_user_id ?: null) : $customer->booker_user_id,
            'shop_name'         => $request->shop_name,
            'owner_name'        => $request->owner_name,
            'phone'             => $request->phone,
            'area'              => $request->area,
            'address'           => $request->address,
            'approved_discount' => (float) ($request->approved_discount ?? 0),
        ]);

        return back()->with('success', "Customer '{$customer->shop_name}' details updated successfully!");
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

        // Get order bookers registered under this distributor
        $orderBookers = User::where('usertype', 'order_booker')
            ->where('distributor_id', $distributor->id)
            ->orderBy('name')
            ->get();

        // Get products available in distributor's inventory
        $stocks = DistributorStock::with(['product.unit', 'product.brand', 'product.category_relation', 'variant'])
            ->where('distributor_id', $distributor->id)
            ->where('qty', '>', 0)
            ->get();

        // Build in-memory products catalog identical to company sale screen
        $productsCatalog = [];
        foreach ($stocks as $stk) {
            $p = $stk->product;
            if (!$p) continue;

            $v = $stk->variant;
            $price = (float)($v && $v->price > 0 ? $v->price : ($p->price ?? 0));
            $sizeLabel = $v ? ($v->size_label ?: $v->variant_name ?: '-') : '-';
            $fullName = $p->item_name . ($sizeLabel !== '-' ? ' (' . $sizeLabel . ')' : '');

            $productsCatalog[] = [
                'id'             => $p->id,
                'product_id'     => $p->id,
                'variant_id'     => $v ? $v->id : null,
                'item_name'      => $fullName,
                'base_name'      => $p->item_name,
                'size'           => $sizeLabel,
                'color'          => $p->color ?: '-',
                'unit_type'      => $p->unit_type ?: 'piece',
                'item_code'      => $p->item_code,
                'barcode'        => ($v ? $v->barcode_path : null) ?: $p->barcode_path ?: $p->item_code,
                'brand'          => $p->brand?->name,
                'price'          => $price,
                'original_price' => $price,
                'stock'          => (float)$stk->qty,
                'category_id'    => $p->category_id,
            ];
        }

        // Extract categories for quick panel
        $categoryIds = $stocks->pluck('product.category_id')->filter()->unique();
        $productIds = $stocks->pluck('product_id')->filter()->unique();
        $categories = \App\Models\Category::with(['products' => function($q) use ($distributor, $productIds) {
            $q->with(['brand', 'variants'])
              ->whereIn('id', $productIds);
        }])->whereIn('id', $categoryIds)->orderBy('name')->get();

        $invoiceNo = DistributorSale::generateInvoiceNo();
        
        // ONLY load distributor's own active accounts (Super Admin accounts are NEVER loaded)
        $bankAccounts = \App\Models\Account::where('status', 1)
            ->where('distributor_id', $distributor->id)
            ->orderBy('title')
            ->get();
        $accountHeads = \App\Models\AccountHead::where('status', 1)->get();

        return view('distributor_panel.sales.create', compact(
            'distributor',
            'customers',
            'orderBookers',
            'stocks',
            'productsCatalog',
            'invoiceNo',
            'categories',
            'bankAccounts',
            'accountHeads'
        ));
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
            'trade_offer'             => 'nullable|array',
            'trade_offer.*'           => 'nullable|numeric|min:0',
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
                // Distributor can adjust trade offer freely (increase, decrease, set to 0, or accept scheme)
                $tradeOffer = max(0, (float) ($request->trade_offer[$index] ?? 0));
                $price = (float) ($request->unit_price[$index] ?? $request->price[$index] ?? 0);
                $variantId = $request->variant_id[$index] ?? null;

                if ($qty <= 0 && $tradeOffer <= 0) continue;

                $totalPhysicalQty = $qty + $tradeOffer;

                // Check distributor stock
                $distStock = DistributorStock::where('distributor_id', $distributor->id)
                    ->where('product_id', $productId)
                    ->when($variantId, fn($q) => $q->where('variant_id', $variantId))
                    ->first();

                if (!$distStock || (float)$distStock->qty < $totalPhysicalQty) {
                    $prodName = Product::find($productId)->item_name ?? 'Item';
                    $avail = $distStock ? (float)$distStock->qty : 0;
                    $reqMsg = $tradeOffer > 0 ? "{$qty} + {$tradeOffer} T.O = {$totalPhysicalQty}" : "{$qty}";
                    throw new \Exception("Insufficient van stock for '{$prodName}'. Available: {$avail}, Requested: {$reqMsg}");
                }

                $itemSubtotal = $qty * $price;
                $subtotal += $itemSubtotal;

                $itemDisc = (float) ($request->item_disc[$index] ?? 0);
                $discType = $request->disc_type[$index] ?? 'percent';
                $discVal = (float) ($request->disc_val[$index] ?? 0);

                $itemsData[] = [
                    'product_id'         => $productId,
                    'variant_id'         => $variantId,
                    'qty'                => $qty,
                    'trade_offer'        => $tradeOffer,
                    'total_physical_qty' => $totalPhysicalQty,
                    'unit_price'         => $price,
                    'subtotal'           => $itemSubtotal,
                    'discount'           => $itemDisc,
                    'discount_type'      => $discType,
                    'discount_val'       => $discVal,
                    'dist_stock'         => $distStock,
                ];
            }

            if (empty($itemsData)) {
                throw new \Exception('No valid items in sale. Please add at least one product with quantity.');
            }

            // Calculate total line discount from items
            $totalLineDiscount = 0;
            foreach ($itemsData as $itemRow) {
                $totalLineDiscount += (float)($itemRow['discount'] ?? 0);
            }

            // Strict enforcement of Company Approved Discount (%) for the customer/shop
            // Distributor / Booker cannot give more or less discount than company approved percentage!
            $approvedPct = (float) $customer->effective_discount;
            $exactDiscount = round(($subtotal * $approvedPct) / 100, 2);
            $discount = $exactDiscount;

            // Recalculate line discounts to strictly match approved percentage
            foreach ($itemsData as &$itRow) {
                $lineGross = (float)($itRow['subtotal'] ?? 0);
                $itRow['discount'] = round(($lineGross * $approvedPct) / 100, 2);
                $itRow['discount_type'] = 'percent';
                $itRow['discount_val'] = $approvedPct;
            }
            unset($itRow);
            $grandTotal = max(0, $subtotal - $discount);

            $inputPaid = 0;
            if ($request->filled('paid_amount')) {
                $inputPaid = (float) $request->paid_amount;
            } elseif ($request->filled('cash') || $request->filled('card')) {
                $inputPaid = (float)($request->cash ?? 0) + (float)($request->card ?? 0);
            } elseif (!empty($request->payment_amount) && is_array($request->payment_amount)) {
                $inputPaid = array_sum(array_map('floatval', $request->payment_amount));
            }
            $paidAmount = min($grandTotal, $inputPaid);
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
            $bookerUserId = Auth::user()->usertype === 'order_booker' ? Auth::id() : ($request->booker_user_id ?: null);
            $sale = DistributorSale::create([
                'invoice_no'              => DistributorSale::generateInvoiceNo(),
                'distributor_id'          => $distributor->id,
                'booker_user_id'          => $bookerUserId,
                'distributor_customer_id' => $customer->id,
                'sale_date'               => $request->sale_date ?: now()->format('Y-m-d'),
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
                    'trade_offer'         => $row['trade_offer'] ?? 0.00,
                    'unit_price'          => $row['unit_price'],
                    'subtotal'            => $row['subtotal'],
                    'discount'            => $row['discount'] ?? 0.00,
                    'discount_type'       => $row['discount_type'] ?? 'percent',
                    'discount_val'        => $row['discount_val'] ?? 0.00,
                ]);

                $distStock = $row['dist_stock'];
                $deductQty = $row['total_physical_qty'] ?? ($row['qty'] + ($row['trade_offer'] ?? 0));
                $distStock->qty = max(0, (float)$distStock->qty - $deductQty);
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

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'     => true,
                    'message'     => "Sale invoice #{$sale->invoice_no} created successfully!",
                    'sale_id'     => $sale->id,
                    'invoice_no'  => $sale->invoice_no,
                    'receipt_url' => route('distributor.sales.receipt', $sale->id),
                    'invoice_url' => route('distributor.sales.receipt', $sale->id),
                    'invoice_a4_url' => route('distributor.sales.invoice_a4', $sale->id),
                ]);
            }

            return redirect()->route('distributor.sales.receipt', $sale->id)->with('success', "Sale invoice #{$sale->invoice_no} created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Printable invoice / receipt for the retailer.
     */
    public function saleReceipt($id)
    {
        $distributor = $this->getDistributor();
        $sale = DistributorSale::with(['customer', 'items.product.unit', 'items.variant', 'booker'])
            ->where('distributor_id', $distributor->id)
            ->findOrFail($id);

        return view('distributor_panel.sales.receipt', compact('distributor', 'sale'));
    }

    /**
     * Printable A4 invoice for distributor sale.
     */
    public function saleInvoiceA4($id)
    {
        $distributor = $this->getDistributor();
        $sale = DistributorSale::with(['customer', 'items.product.unit', 'items.variant', 'booker'])
            ->where('distributor_id', $distributor->id)
            ->findOrFail($id);

        return view('distributor_panel.sales.sale_invoice_a4', compact('distributor', 'sale'));
    }

    /**
     * History of sales made by distributor.
     */
    public function salesList(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $query = DistributorSale::with(['customer', 'items', 'booker', 'claim'])
            ->where('distributor_id', $distributor->id);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('invoice_no', 'like', "%{$s}%")
                  ->orWhereHas('customer', function($cq) use ($s) {
                      $cq->where('shop_name', 'like', "%{$s}%")
                         ->orWhere('owner_name', 'like', "%{$s}%")
                         ->orWhere('phone', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('customer_id')) {
            $query->where('distributor_customer_id', $request->customer_id);
        }

        if ($request->filled('payment_type') && $request->payment_type !== 'all') {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('month')) {
            $carbonMonth = \Carbon\Carbon::parse($request->month . '-01');
            $query->whereBetween('sale_date', [
                $carbonMonth->copy()->startOfMonth()->format('Y-m-d'),
                $carbonMonth->copy()->endOfMonth()->format('Y-m-d')
            ]);
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('sale_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }

        $filteredSaleIds = (clone $query)->pluck('id');
        $totalTradeOfferPcs = (float) \App\Models\DistributorSaleItem::whereIn('distributor_sale_id', $filteredSaleIds)->sum('trade_offer');

        // Summary stats across the filtered scope
        $stats = [
            'total_sales'        => (float) (clone $query)->sum('grand_total'),
            'total_paid'         => (float) (clone $query)->sum('paid_amount'),
            'total_due'          => (float) (clone $query)->sum('due_amount'),
            'total_invoices'     => (int) (clone $query)->count(),
            'total_discount'     => (float) (clone $query)->sum('discount'),
            'unclaimed_discount' => (float) (clone $query)->whereNull('claim_id')->where('discount', '>', 0)->sum('discount'),
            'total_trade_offer'  => $totalTradeOfferPcs,
        ];

        $sales = $query->latest('sale_date')->latest('id')->paginate(15);

        // Self-heal any legacy records where discount was not subtracted from grand_total / due_amount
        foreach ($sales as $s) {
            if ($s->items && $s->items->count() > 0) {
                $computedGross = 0;
                $computedDisc = 0;
                foreach ($s->items as $it) {
                    $computedGross += ((float)$it->qty * (float)$it->unit_price);
                    $computedDisc += (float)($it->discount ?? 0);
                }
                $trueNet = max(0, $computedGross - $computedDisc);
                if ($computedDisc > 0 && abs((float)$s->grand_total - $computedGross) < 0.05) {
                    $s->grand_total = $trueNet;
                    $s->discount = $computedDisc;
                    $s->due_amount = max(0, $trueNet - (float)$s->paid_amount);

                    \DB::table('distributor_sales')->where('id', $s->id)->update([
                        'discount'    => $computedDisc,
                        'grand_total' => $trueNet,
                        'due_amount'  => $s->due_amount,
                    ]);

                    \DB::table('distributor_customer_ledgers')
                        ->where('reference_no', $s->invoice_no)
                        ->where('type', 'sale')
                        ->update(['debit' => $trueNet]);

                    $cBal = \DB::table('distributor_customer_ledgers')
                        ->where('distributor_customer_id', $s->distributor_customer_id)
                        ->selectRaw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) as bal')
                        ->value('bal');
                    if ($cBal !== null) {
                        \DB::table('distributor_customers')
                            ->where('id', $s->distributor_customer_id)
                            ->update(['current_balance' => (float)$cBal]);
                    }
                }
            }
        }

        $customers = DistributorCustomer::where('distributor_id', $distributor->id)->get();

        return view('distributor_panel.sales.index', compact('distributor', 'sales', 'customers', 'stats'));
    }

    /**
     * List of Customer Sales Returns handled by distributor.
     */
    public function salesReturnsList(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $query = DistributorSalesReturn::with(['customer', 'items.product', 'sale'])
            ->where('distributor_id', $distributor->id);

        if ($request->customer_id) {
            $query->where('distributor_customer_id', $request->customer_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('return_date', [$request->start_date, $request->end_date]);
        }

        $returns = $query->latest('return_date')->latest('id')->paginate(15);
        $customers = DistributorCustomer::where('distributor_id', $distributor->id)->get();

        return view('distributor_panel.returns.index', compact('distributor', 'returns', 'customers'));
    }

    /**
     * Screen to create a customer sale return.
     */
    public function createSaleReturn(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $customers = DistributorCustomer::where('distributor_id', $distributor->id)
            ->where('status', 'active')
            ->orderBy('shop_name')
            ->get();

        $saleId = $request->get('sale_id');
        $selectedSale = null;
        if ($saleId) {
            $selectedSale = DistributorSale::with(['customer', 'items.product.unit', 'items.variant'])
                ->where('distributor_id', $distributor->id)
                ->find($saleId);
        }

        // Available stock products in distributor custody
        $stocks = DistributorStock::with(['product.unit', 'variant'])
            ->where('distributor_id', $distributor->id)
            ->get();

        $returnNo = DistributorSalesReturn::generateReturnNo();
        $accounts = Account::where('status', 1)
            ->where('distributor_id', $distributor->id)
            ->get();

        return view('distributor_panel.returns.create', compact('distributor', 'customers', 'selectedSale', 'stocks', 'returnNo', 'accounts'));
    }

    /**
     * Process & Store Customer Sale Return.
     */
    public function storeSaleReturn(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $request->validate([
            'distributor_customer_id' => 'required|exists:distributor_customers,id',
            'return_date'             => 'required|date',
            'product_id'              => 'required|array|min:1',
            'product_id.*'            => 'nullable',
            'return_qty'              => 'required|array|min:1',
            'return_qty.*'            => 'nullable|numeric|min:0',
            'unit_price'              => 'required|array',
            'refund_mode'             => 'required|in:credit,cash',
            'account_id'              => 'nullable',
            'refund_amount'           => 'nullable|numeric|min:0',
            'reason'                  => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $customer = DistributorCustomer::where('distributor_id', $distributor->id)
                ->findOrFail($request->distributor_customer_id);

            $sale = null;
            if ($request->filled('distributor_sale_id')) {
                $sale = DistributorSale::where('distributor_id', $distributor->id)->find($request->distributor_sale_id);
            }

            $totalReturnAmount = 0;
            $itemsData = [];

            foreach ($request->product_id as $index => $productId) {
                if (empty($productId)) continue;
                $qty = (float) ($request->return_qty[$index] ?? 0);
                if ($qty <= 0) continue;

                $price = (float) ($request->unit_price[$index] ?? 0);
                $variantId = $request->variant_id[$index] ?? null;
                $condition = $request->item_condition[$index] ?? 'restockable';
                $saleItemId = $request->sale_item_id[$index] ?? null;

                $subtotal = $qty * $price;
                $totalReturnAmount += $subtotal;

                $itemsData[] = [
                    'product_id'             => $productId,
                    'variant_id'             => $variantId,
                    'distributor_sale_item_id'=> $saleItemId,
                    'qty'                    => $qty,
                    'unit_price'             => $price,
                    'subtotal'               => $subtotal,
                    'item_condition'         => $condition,
                ];
            }

            if (empty($itemsData)) {
                throw new \Exception('Please enter at least one item with valid return quantity.');
            }

            $refundMode = $request->refund_mode;
            $refundAmount = 0;
            $creditAdjusted = 0;

            if ($refundMode === 'cash') {
                $refundAmount = min($totalReturnAmount, (float) ($request->refund_amount ?? $totalReturnAmount));
                $creditAdjusted = max(0, $totalReturnAmount - $refundAmount);
            } else {
                // credit adjustment directly reduces customer due
                $creditAdjusted = $totalReturnAmount;
                $refundAmount = 0;
            }

            $bookerUserId = $customer->booker_user_id ?: (Auth::user()->usertype === 'order_booker' ? Auth::id() : null);

            $salesReturn = DistributorSalesReturn::create([
                'return_no'               => DistributorSalesReturn::generateReturnNo(),
                'distributor_id'          => $distributor->id,
                'distributor_sale_id'     => $sale ? $sale->id : null,
                'distributor_customer_id' => $customer->id,
                'booker_user_id'          => $bookerUserId,
                'account_id'              => $request->account_id ?: null,
                'return_date'             => $request->return_date,
                'subtotal'                => $totalReturnAmount,
                'refund_amount'           => $refundAmount,
                'net_return_amount'       => $totalReturnAmount,
                'credit_adjusted'         => $creditAdjusted,
                'refund_mode'             => $refundMode,
                'reason'                  => $request->reason,
                'created_by'              => Auth::id(),
            ]);

            // Save items and increase distributor custody stock for restockable items
            foreach ($itemsData as $row) {
                $row['distributor_sales_return_id'] = $salesReturn->id;
                DistributorSalesReturnItem::create($row);

                if ($row['item_condition'] === 'restockable') {
                    $stock = DistributorStock::firstOrCreate([
                        'distributor_id' => $distributor->id,
                        'product_id'     => $row['product_id'],
                        'variant_id'     => $row['variant_id'],
                    ], [
                        'qty'       => 0,
                        'avg_price' => $row['unit_price'],
                    ]);

                    $stock->qty = (float)$stock->qty + $row['qty'];
                    $stock->save();
                }
            }

            // Customer Ledger Update:
            // Sale Return always CREDITS the customer's account (lowering their outstanding debt)
            $newCustBalance = (float)$customer->current_balance;
            if ($creditAdjusted > 0) {
                $newCustBalance = $newCustBalance - $creditAdjusted;

                DistributorCustomerLedger::create([
                    'distributor_customer_id' => $customer->id,
                    'distributor_id'          => $distributor->id,
                    'account_id'              => $request->account_id ?: null,
                    'date'                    => $request->return_date,
                    'type'                    => 'return',
                    'reference_no'            => $salesReturn->return_no,
                    'description'             => "Customer Return #{$salesReturn->return_no}" . ($sale ? " (Against Inv #{$sale->invoice_no})" : "") . " - Balance Deducted",
                    'debit'                   => 0,
                    'credit'                  => $creditAdjusted,
                    'balance'                 => $newCustBalance,
                ]);
            }

            // If cash refund was paid to customer directly:
            if ($refundAmount > 0) {
                // If paid in cash, record refund voucher note
                DistributorCustomerLedger::create([
                    'distributor_customer_id' => $customer->id,
                    'distributor_id'          => $distributor->id,
                    'account_id'              => $request->account_id ?: null,
                    'date'                    => $request->return_date,
                    'type'                    => 'payment_refund',
                    'reference_no'            => 'REF-' . $salesReturn->return_no,
                    'description'             => "Cash Refund of Rs. " . number_format($refundAmount, 2) . " paid to customer for Return #{$salesReturn->return_no}",
                    'debit'                   => 0,
                    'credit'                  => 0,
                    'balance'                 => $newCustBalance,
                ]);
            }

            $customer->update(['current_balance' => $newCustBalance]);

            DB::commit();
            return redirect()->route('distributor.sales.receipt_return', $salesReturn->id)
                ->with('success', "Sale return #{$salesReturn->return_no} processed successfully! Stock and customer ledger updated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error processing customer return: ' . $e->getMessage());
        }
    }

    /**
     * Show Return Receipt.
     */
    public function returnReceipt($id)
    {
        $distributor = $this->getDistributor();
        $return = DistributorSalesReturn::with(['customer', 'sale', 'items.product.unit', 'items.variant', 'account'])
            ->where('distributor_id', $distributor->id)
            ->findOrFail($id);

        return view('distributor_panel.returns.receipt', compact('distributor', 'return'));
    }

    /**
     * Retailer customer ledger view & payment collection.
     */
    public function customerLedger($id, Request $request)
    {
        $distributor = $this->getDistributor();
        $customer = DistributorCustomer::where('distributor_id', $distributor->id)->findOrFail($id);

        $query = DistributorCustomerLedger::with('account')
            ->where('distributor_customer_id', $customer->id)
            ->where('distributor_id', $distributor->id);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $entries = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        $totalDebit = $entries->sum('debit');
        $totalCredit = $entries->sum('credit');

        // Accounts owned by this distributor
        $accounts = Account::where('status', 1)
            ->where('distributor_id', $distributor->id)
            ->with('head')
            ->orderBy('title')
            ->get();

        if ($accounts->isEmpty()) {
            $cashHead = AccountHead::where('name', 'like', '%Cash%')->first();
            $headId = $cashHead ? $cashHead->id : (AccountHead::first()->id ?? 1);
            $defaultAcc = Account::create([
                'distributor_id'  => $distributor->id,
                'head_id'         => $headId,
                'account_code'    => 'D' . $distributor->id . '-CASH-' . rand(100, 999),
                'title'           => 'Cash in Hand',
                'type'            => 'Debit',
                'opening_balance' => 0,
                'status'          => 1,
            ]);
            $accounts = collect([$defaultAcc]);
        }

        return view('distributor_panel.customers.ledger', compact('distributor', 'customer', 'entries', 'totalDebit', 'totalCredit', 'accounts'));
    }

    /**
     * Display date-wise customer payments / collection report & management.
     */
    public function customerPaymentsList(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $startDate = $request->start_date ?: now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?: now()->format('Y-m-d');

        $query = DistributorCustomerLedger::with(['customer.booker', 'account', 'creator'])
            ->where('distributor_id', $distributor->id)
            ->where('type', 'payment_received');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        if ($request->filled('customer_id')) {
            $query->where('distributor_customer_id', $request->customer_id);
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('shop_name', 'like', "%{$search}%")
                         ->orWhere('owner_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Summary metrics
        $filteredTotal = (float) (clone $query)->sum('credit');
        $filteredCount = (int) (clone $query)->count();

        $todayTotal = (float) DistributorCustomerLedger::where('distributor_id', $distributor->id)
            ->where('type', 'payment_received')
            ->whereDate('date', now()->format('Y-m-d'))
            ->sum('credit');

        $monthTotal = (float) DistributorCustomerLedger::where('distributor_id', $distributor->id)
            ->where('type', 'payment_received')
            ->whereBetween('date', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')])
            ->sum('credit');

        $allTimeTotal = (float) DistributorCustomerLedger::where('distributor_id', $distributor->id)
            ->where('type', 'payment_received')
            ->sum('credit');

        $payments = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(25)->appends($request->all());

        // Dropdown data
        $customers = DistributorCustomer::where('distributor_id', $distributor->id)->orderBy('shop_name')->get();
        $accounts = Account::where('status', 1)->where('distributor_id', $distributor->id)->orderBy('title')->get();

        return view('distributor_panel.customers.payments_list', compact(
            'distributor',
            'payments',
            'filteredTotal',
            'filteredCount',
            'todayTotal',
            'monthTotal',
            'allTimeTotal',
            'customers',
            'accounts',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show/print receipt for a specific customer payment collection.
     */
    public function customerPaymentReceipt($id)
    {
        $distributor = $this->getDistributor();
        $payment = DistributorCustomerLedger::with(['customer.booker', 'account', 'creator'])
            ->where('distributor_id', $distributor->id)
            ->where('type', 'payment_received')
            ->findOrFail($id);

        return view('distributor_panel.customers.payment_receipt', compact('distributor', 'payment'));
    }

    /**
     * Delete/Reverse a customer payment entry.
     */
    public function destroyCustomerPayment($id)
    {
        $distributor = $this->getDistributor();
        $entry = DistributorCustomerLedger::where('distributor_id', $distributor->id)
            ->where('type', 'payment_received')
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            $customer = DistributorCustomer::where('distributor_id', $distributor->id)->findOrFail($entry->distributor_customer_id);
            $amount = (float) $entry->credit;

            // Reverse customer balance (add back amount)
            $customer->current_balance = (float)$customer->current_balance + $amount;
            $customer->save();

            // Reverse account balance if associated
            if ($entry->account_id) {
                $account = Account::where('distributor_id', $distributor->id)->find($entry->account_id);
                if ($account) {
                    $account->opening_balance = (float)$account->opening_balance - $amount;
                    $account->save();
                }
            }

            $entry->delete();

            DB::commit();
            return back()->with('success', "Payment receipt #{$entry->reference_no} reversed successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error reversing payment: ' . $e->getMessage());
        }
    }

    /**
     * Record cash or cheque collected from retail customer.
     */
    public function storeCustomerPayment(Request $request, $id = null)
    {
        $distributor = $this->getDistributor();
        $customerId = $id ?: ($request->distributor_customer_id ?: $request->customer_id);

        if (!$customerId) {
            return back()->with('error', 'Please select a valid customer.');
        }

        $customer = DistributorCustomer::where('distributor_id', $distributor->id)->findOrFail($customerId);

        $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'date'           => 'required|date',
            'account_id'     => 'required|exists:accounts,id',
            'payment_method' => 'nullable|string|max:50',
            'cheque_no'      => 'nullable|string|max:100',
            'cheque_bank'    => 'nullable|string|max:100',
            'cheque_date'    => 'nullable|date',
            'notes'          => 'nullable|string|max:1000',
        ]);

        // Security check: ensure account belongs strictly to this distributor
        $account = Account::where('distributor_id', $distributor->id)
            ->where('id', $request->account_id)
            ->firstOrFail();

        $amount = (float) $request->amount;

        DB::beginTransaction();
        try {
            $newBalance = (float) $customer->current_balance - $amount;

            $method = $request->payment_method ?: 'cash';
            $methodLabel = ucfirst($method);
            if (strtolower($method) === 'cheque' && $request->filled('cheque_no')) {
                $methodLabel = "Cheque (#{$request->cheque_no}" . ($request->filled('cheque_bank') ? " - {$request->cheque_bank}" : "") . ($request->filled('cheque_date') ? " dt: {$request->cheque_date}" : "") . ")";
            }

            $desc = "Payment collection via {$methodLabel} (Account: {$account->title})";
            if ($request->filled('notes')) {
                $desc .= ". Notes: " . $request->notes;
            }

            $lastRec = DistributorCustomerLedger::where('type', 'payment_received')->latest('id')->first();
            $nextRecId = $lastRec ? ($lastRec->id + 1) : 1;
            $recNo = 'REC-' . str_pad($nextRecId, 4, '0', STR_PAD_LEFT);

            $ledger = DistributorCustomerLedger::create([
                'distributor_customer_id' => $customer->id,
                'distributor_id'          => $distributor->id,
                'account_id'              => $account->id,
                'created_by'              => Auth::id(),
                'date'                    => $request->date,
                'type'                    => 'payment_received',
                'reference_no'            => $recNo,
                'description'             => $desc,
                'debit'                   => 0,
                'credit'                  => $amount,
                'balance'                 => $newBalance,
            ]);

            $customer->update(['current_balance' => $newBalance]);

            // Update Distributor Account Balance
            $account->opening_balance = (float)$account->opening_balance + $amount;
            $account->save();

            DB::commit();

            if ($request->filled('redirect_to_receipt') && $request->boolean('redirect_to_receipt')) {
                return redirect()->route('distributor.customer_payments.receipt', $ledger->id)
                    ->with('success', "Payment of Rs. " . number_format($amount, 2) . " received successfully! Receipt #{$recNo}");
            }

            return back()->with('success', "Payment of Rs. " . number_format($amount, 2) . " received and deposited into '{$account->title}'! Receipt #{$recNo}");
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
            'success'           => true,
            'id'                => $customer->id,
            'shop_name'         => $customer->shop_name,
            'owner_name'        => $customer->owner_name ?: 'N/A',
            'phone'             => $customer->phone ?: 'N/A',
            'area'              => $customer->area ?: 'N/A',
            'address'           => $customer->address ?: 'N/A',
            'current_balance'   => $balance,
            'approved_discount' => (float) ($customer->approved_discount ?? 0),
            'formatted_due'     => 'Rs ' . number_format(abs($balance), 2),
            'has_due'           => $balance > 0,
            'due_status'        => $balance > 0 ? 'Outstanding Balance Due' : ($balance < 0 ? 'Advance Balance Available' : 'Account Clear / Zero Balance'),
            'badge_class'       => $balance > 0 ? 'bg-danger' : ($balance < 0 ? 'bg-info' : 'bg-success'),
        ]);
    }

    public function storeShopBooking(Request $request)
    {
        $distributor = $this->getDistributor();

        $request->validate([
            'distributor_customer_id' => 'required|exists:distributor_customers,id',
            'payment_type'            => 'nullable|in:cash,credit',
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
                // Distributor can adjust trade offer freely for booking
                $tradeOffer = max(0, (float) ($request->trade_offer[$index] ?? 0));
                $price = (float) ($request->unit_price[$index] ?? 0);
                $variantId = $request->variant_id[$index] ?? null;

                if ($qty <= 0 && $tradeOffer <= 0) continue;

                $lineSubtotal = $qty * $price;
                $subtotal += $lineSubtotal;
                $totalQty += $qty;

                $itemsData[] = [
                    'product_id'  => $pId,
                    'variant_id'  => $variantId,
                    'qty'         => $qty,
                    'trade_offer' => $tradeOffer,
                    'unit_price'  => $price,
                    'subtotal'    => $lineSubtotal,
                ];
            }

            if (empty($itemsData)) {
                throw new \Exception('Please add at least one product with quantity to book order.');
            }

            $discount = (float) ($request->discount ?? 0);
            $approvedPct = (float) $customer->effective_discount;
            $exactDiscount = round(($subtotal * $approvedPct) / 100, 2);
            $discount = $exactDiscount; // Force exact company approved discount
            $maxAllowedDiscount = round(($subtotal * $approvedPct) / 100, 2);

            if ($discount > ($maxAllowedDiscount + 0.05)) {
                throw new \Exception("Discount exceeds the approved limit for this shop! This shop is approved for maximum {$approvedPct}% discount (Max: Rs. " . number_format($maxAllowedDiscount, 2) . "). You entered Rs. " . number_format($discount, 2) . ".");
            }

            $grandTotal = max(0, $subtotal - $discount);

            $booking = DistributorShopBooking::create([
                'booking_no'              => DistributorShopBooking::generateBookingNo(),
                'distributor_id'          => $distributor->id,
                'booker_user_id'          => Auth::id(),
                'distributor_customer_id' => $customer->id,
                'booking_date'            => $request->booking_date ?? now()->toDateString(),
                'payment_type'            => $request->payment_type ?? 'cash',
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
                    'booking_id'  => $booking->id,
                    'product_id'  => $it['product_id'],
                    'variant_id'  => $it['variant_id'],
                    'qty'         => $it['qty'],
                    'trade_offer' => $it['trade_offer'] ?? 0,
                    'unit_price'  => $it['unit_price'],
                    'subtotal'    => $it['subtotal'],
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

        if ($request->payment_type && $request->payment_type !== 'all') {
            $query->where('payment_type', $request->payment_type);
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

        if ($request->payment_type && $request->payment_type !== 'all') {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('booking_date', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $query->whereDate('booking_date', $request->start_date);
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

        // Check stock availability for each item (Purchased Qty + Free Trade Offer)
        $stockCheck = [];
        $canDeliver = true;
        foreach ($booking->items as $item) {
            $st = DistributorStock::where('distributor_id', $distributor->id)
                ->where('product_id', $item->product_id)
                ->when($item->variant_id, fn($q) => $q->where('variant_id', $item->variant_id))
                ->first();
            $avail = $st ? (float)$st->qty : 0;
            $neededQty = (float)$item->qty + (float)($item->trade_offer ?? 0);
            $stockCheck[$item->id] = [
                'available'   => $avail,
                'needed'      => $neededQty,
                'trade_offer' => (float)($item->trade_offer ?? 0),
                'is_ok'       => $avail >= $neededQty,
            ];
            if ($avail < $neededQty) {
                $canDeliver = false;
            }
        }

        return response()->json([
            'success'        => true,
            'booking'        => $booking,
            'formatted_date' => \Carbon\Carbon::parse($booking->booking_date)->format('d-M-Y'),
            'stock_check'    => $stockCheck,
            'can_deliver'    => $canDeliver,
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

            // 1. Verify and deduct stock for all items (Qty + Free Trade Offer)
            foreach ($booking->items as $item) {
                $distStock = DistributorStock::where('distributor_id', $distributor->id)
                    ->where('product_id', $item->product_id)
                    ->when($item->variant_id, fn($q) => $q->where('variant_id', $item->variant_id))
                    ->first();

                $neededQty = (float)$item->qty + (float)($item->trade_offer ?? 0);

                if (!$distStock || (float)$distStock->qty < $neededQty) {
                    $prodName = Product::find($item->product_id)->item_name ?? 'Item';
                    $avail = $distStock ? (float)$distStock->qty : 0;
                    throw new \Exception("Insufficient stock in your custody for '{$prodName}'. Available: {$avail}, Required (Qty + Trade Offer): {$neededQty}. Please replenish stock first.");
                }

                $distStock->qty = (float)$distStock->qty - $neededQty;
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
                'booker_user_id'          => $booking->booker_user_id,
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
                    'trade_offer'         => $item->trade_offer ?? 0,
                    'unit_price'          => $item->unit_price,
                    'subtotal'            => $item->subtotal,
                    'discount'            => $item->discount ?? 0,
                    'discount_type'       => $item->discount_type ?? 'percent',
                    'discount_val'        => $item->discount_val ?? 0,
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

    /**
     * Edit shop booking interface (for Distributor and Order Booker).
     */
    public function editShopBooking($id)
    {
        $distributor = $this->getDistributor();
        $user = Auth::user();

        $booking = DistributorShopBooking::with(['items.product.unit', 'items.variant', 'customer', 'booker'])
            ->where('distributor_id', $distributor->id)
            ->findOrFail($id);

        if ($user && $user->usertype === 'order_booker' && $booking->booker_user_id != $user->id) {
            return redirect()->route('distributor.booker.my_bookings')->with('error', 'You can only edit your own booked orders.');
        }

        if ($booking->status === 'delivered') {
            $dest = ($user && $user->usertype === 'order_booker') ? 'distributor.booker.my_bookings' : 'distributor.shop_bookings.index';
            return redirect()->route($dest)->with('error', 'Cannot edit a booking that has already been delivered and converted to a sale.');
        }

        if ($booking->status === 'cancelled') {
            $dest = ($user && $user->usertype === 'order_booker') ? 'distributor.booker.my_bookings' : 'distributor.shop_bookings.index';
            return redirect()->route($dest)->with('error', 'Cannot edit a cancelled booking.');
        }

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

        $isBookerUser = ($user && $user->usertype === 'order_booker');

        return view('distributor_panel.shop_bookings.edit', compact('distributor', 'booking', 'customers', 'products', 'stockMap', 'isBookerUser'));
    }

    /**
     * Update shop booking details.
     */
    public function updateShopBooking(Request $request, $id)
    {
        $distributor = $this->getDistributor();
        $user = Auth::user();

        $booking = DistributorShopBooking::with('items')
            ->where('distributor_id', $distributor->id)
            ->findOrFail($id);

        $destRoute = ($user && $user->usertype === 'order_booker') ? 'distributor.booker.my_bookings' : 'distributor.shop_bookings.index';

        if ($user && $user->usertype === 'order_booker' && $booking->booker_user_id != $user->id) {
            return redirect()->route('distributor.booker.my_bookings')->with('error', 'You can only edit your own booked orders.');
        }

        if ($booking->status === 'delivered') {
            return redirect()->route($destRoute)->with('error', 'Cannot edit a booking that has already been delivered.');
        }

        if ($booking->status === 'cancelled') {
            return redirect()->route($destRoute)->with('error', 'Cannot edit a cancelled booking.');
        }

        $request->validate([
            'distributor_customer_id' => 'required|exists:distributor_customers,id',
            'payment_type'            => 'nullable|in:cash,credit',
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
                $tradeOffer = (float) ($request->trade_offer[$index] ?? 0);
                $price = (float) ($request->unit_price[$index] ?? 0);
                $variantId = $request->variant_id[$index] ?? null;

                if ($qty <= 0 && $tradeOffer <= 0) continue;

                $lineSubtotal = $qty * $price;
                $subtotal += $lineSubtotal;
                $totalQty += $qty;

                $itemsData[] = [
                    'product_id'  => $pId,
                    'variant_id'  => $variantId,
                    'qty'         => $qty,
                    'trade_offer' => $tradeOffer,
                    'unit_price'  => $price,
                    'subtotal'    => $lineSubtotal,
                ];
            }

            if (empty($itemsData)) {
                throw new \Exception('Please add at least one product with quantity to update the booking.');
            }

            $discount = (float) ($request->discount ?? 0);
            $approvedPct = (float) ($customer->approved_discount ?? 0);
            $maxAllowedDiscount = round(($subtotal * $approvedPct) / 100, 2);

            if ($discount > ($maxAllowedDiscount + 0.05)) {
                throw new \Exception("Discount exceeds the approved limit for this shop! This shop is approved for maximum {$approvedPct}% discount (Max: Rs. " . number_format($maxAllowedDiscount, 2) . "). You entered Rs. " . number_format($discount, 2) . ".");
            }

            $grandTotal = max(0, $subtotal - $discount);

            $booking->update([
                'distributor_customer_id' => $customer->id,
                'booking_date'            => $request->booking_date ?? $booking->booking_date,
                'payment_type'            => $request->payment_type ?? 'cash',
                'total_items'             => count($itemsData),
                'total_qty'               => $totalQty,
                'subtotal'                => $subtotal,
                'discount'                => $discount,
                'grand_total'             => $grandTotal,
                'notes'                   => $request->notes,
            ]);

            // Sync items: delete old items and recreate updated items
            $booking->items()->delete();

            foreach ($itemsData as $it) {
                DistributorShopBookingItem::create([
                    'booking_id'  => $booking->id,
                    'product_id'  => $it['product_id'],
                    'variant_id'  => $it['variant_id'],
                    'qty'         => $it['qty'],
                    'trade_offer' => $it['trade_offer'] ?? 0,
                    'unit_price'  => $it['unit_price'],
                    'subtotal'    => $it['subtotal'],
                ]);
            }

            DB::commit();

            return redirect()->route($destRoute)->with('success', "Booking #{$booking->booking_no} has been updated successfully!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update booking: ' . $e->getMessage());
        }
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

        // Self-heal/sync discount on distributor_sales from items if needed
        $unhealedSales = DistributorSale::where('distributor_id', $distributor->id)
            ->where(function($q) {
                $q->whereNull('discount')->orWhere('discount', '<=', 0);
            })
            ->with('items')
            ->get();
        foreach ($unhealedSales as $us) {
            $sumDisc = (float) $us->items->sum('discount');
            if ($sumDisc > 0) {
                $us->update(['discount' => $sumDisc]);
            }
        }

        $query = DistributorClaim::where('distributor_id', $distributor->id)
            ->with(['sales.customer', 'approver'])
            ->latest('id');

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

        // Discount tracking
        $totalDiscountGiven = (float) DistributorSale::where('distributor_id', $distributor->id)->sum('discount');
        $unclaimedDiscount = (float) DistributorSale::where('distributor_id', $distributor->id)->whereNull('claim_id')->where('discount', '>', 0)->sum('discount');

        return view('distributor_panel.claims.index', compact(
            'distributor', 'claims', 'totalClaimsCount', 'pendingCount', 'pendingAmount', 'approvedAmount',
            'totalDiscountGiven', 'unclaimedDiscount'
        ));
    }

    /**
     * AJAX endpoint: Calculate unclaimed retail discounts for a date range.
     */
    public function calculateDiscountForClaim(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date',
        ]);

        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        if ($fromDate > $toDate) {
            $tmp = $fromDate;
            $fromDate = $toDate;
            $toDate = $tmp;
        }

        // Self-heal/sync discount from items if any
        $unhealed = DistributorSale::where('distributor_id', $distributor->id)
            ->whereBetween('sale_date', [$fromDate, $toDate])
            ->where(function($q) {
                $q->whereNull('discount')->orWhere('discount', '<=', 0);
            })
            ->with('items')
            ->get();
        foreach ($unhealed as $uh) {
            $sd = (float) $uh->items->sum('discount');
            if ($sd > 0) {
                $uh->update(['discount' => $sd]);
            }
        }

        $sales = DistributorSale::where('distributor_id', $distributor->id)
            ->whereBetween('sale_date', [$fromDate, $toDate])
            ->where('discount', '>', 0)
            ->whereNull('claim_id')
            ->with('customer:id,shop_name,owner_name,phone,approved_discount')
            ->orderBy('sale_date', 'asc')
            ->get();

        $totalDiscount = (float) $sales->sum('discount');
        $totalSubtotal = (float) $sales->sum('subtotal');

        $salesList = $sales->map(function ($s) {
            return [
                'id'          => $s->id,
                'invoice_no'  => $s->invoice_no,
                'sale_date'   => $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('d M Y') : '-',
                'shop_name'   => $s->customer->shop_name ?? 'Retail Shop',
                'owner_name'  => $s->customer->owner_name ?? '',
                'phone'       => $s->customer->phone ?? '',
                'approved_pct'=> (float)($s->customer->approved_discount ?? 0),
                'subtotal'    => (float) $s->subtotal,
                'discount'    => (float) $s->discount,
                'grand_total' => (float) $s->grand_total,
            ];
        });

        return response()->json([
            'status'         => true,
            'count'          => $sales->count(),
            'total_discount' => $totalDiscount,
            'total_subtotal' => $totalSubtotal,
            'sales'          => $salesList,
        ]);
    }

    /**
     * Submit a new retail discount claim to Company / Head Office.
     */
    public function storeClaim(Request $request)
    {
        $this->enforceBookerRestriction();
        $distributor = $this->getDistributor();

        $request->validate([
            'from_date'   => 'required|date',
            'to_date'     => 'required|date',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'description' => 'nullable|string',
        ]);

        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        if ($fromDate > $toDate) {
            $tmp = $fromDate;
            $fromDate = $toDate;
            $toDate = $tmp;
        }

        DB::beginTransaction();
        try {
            // Self-heal/sync discount from items if any
            $unhealed = DistributorSale::where('distributor_id', $distributor->id)
                ->whereBetween('sale_date', [$fromDate, $toDate])
                ->where(function($q) {
                    $q->whereNull('discount')->orWhere('discount', '<=', 0);
                })
                ->with('items')
                ->get();
            foreach ($unhealed as $uh) {
                $sd = (float) $uh->items->sum('discount');
                if ($sd > 0) {
                    $uh->update(['discount' => $sd]);
                }
            }

            $sales = DistributorSale::where('distributor_id', $distributor->id)
                ->whereBetween('sale_date', [$fromDate, $toDate])
                ->where('discount', '>', 0)
                ->whereNull('claim_id')
                ->get();

            if ($sales->isEmpty() || (float)$sales->sum('discount') <= 0) {
                return back()->with('error', "No unclaimed retail shop discounts found between {$fromDate} and {$toDate}.");
            }

            $totalDiscount = (float) $sales->sum('discount');

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
                'claim_type'      => 'trade_discount',
                'claim_date'      => now()->toDateString(),
                'from_date'       => $fromDate,
                'to_date'         => $toDate,
                'total_amount'    => $totalDiscount,
                'approved_amount' => 0.00,
                'settlement_type' => 'credit_note',
                'status'          => 'pending',
                'attachment'      => $attachmentPath,
                'description'     => $request->description ?: "Retail discount claim for {$sales->count()} shop bills from {$fromDate} to {$toDate}.",
                'submitted_by'    => Auth::id(),
            ]);

            // Link sales to this claim
            DistributorSale::whereIn('id', $sales->pluck('id'))->update(['claim_id' => $claim->id]);

            DB::commit();
            return back()->with('success', "Claim #{$claimNo} of Rs. " . number_format($totalDiscount, 2) . " for {$sales->count()} shop bills submitted successfully to Head Office for verification!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error submitting claim: ' . $e->getMessage());
        }
    }
}
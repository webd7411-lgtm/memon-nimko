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
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        // Check if distributor relation exists
        $distributor = Distributor::where('user_id', $user->id)->first();

        // Fallback for Super Admin testing portal
        $isAdmin = false;
        try {
            $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole(['Super Admin', 'super-admin'])) || ($user->usertype === 'admin');
        } catch (\Throwable $e) {
            $isAdmin = ($user->usertype === 'admin');
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
     * Distributor Portal Dashboard.
     */
    public function dashboard()
    {
        $distributor = $this->getDistributor();

        // 1. Company Ledger Hisab Kitab
        $companyLedgerEntries = DistributorLedger::where('distributor_id', $distributor->id)->get();
        $totalGoodsFromCompany = $companyLedgerEntries->sum('debit');
        $totalPaidToCompany = $companyLedgerEntries->sum('credit');
        $payableBalanceToCompany = (float) $distributor->current_balance;

        // 2. Retail Market & Recovery Hisab Kitab
        $totalStockUnits = DistributorStock::where('distributor_id', $distributor->id)->sum('qty');
        $totalCustomers = DistributorCustomer::where('distributor_id', $distributor->id)->count();
        $totalMarketDue = DistributorCustomer::where('distributor_id', $distributor->id)->sum('current_balance');

        $totalCustomerRecovery = DistributorCustomerLedger::where('distributor_id', $distributor->id)
            ->where('type', 'payment_received')
            ->sum('credit');

        // Recent recovery collections from retail shops
        $recentRecoveries = DistributorCustomerLedger::with('customer')
            ->where('distributor_id', $distributor->id)
            ->where('type', 'payment_received')
            ->latest('id')
            ->take(5)
            ->get();

        // 3. Sales
        $todaySales = DistributorSale::where('distributor_id', $distributor->id)
            ->whereDate('sale_date', now()->format('Y-m-d'))
            ->sum('grand_total');

        $monthSales = DistributorSale::where('distributor_id', $distributor->id)
            ->whereBetween('sale_date', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')])
            ->sum('grand_total');

        $pendingDemandsCount = DistributorOrder::where('distributor_id', $distributor->id)
            ->where('status', 'pending')
            ->count();

        $recentSales = DistributorSale::with('customer')
            ->where('distributor_id', $distributor->id)
            ->latest('id')
            ->take(5)
            ->get();

        $recentOrders = DistributorOrder::where('distributor_id', $distributor->id)
            ->latest('id')
            ->take(5)
            ->get();

        return view('distributor_panel.dashboard', compact(
            'distributor',
            'totalGoodsFromCompany',
            'totalPaidToCompany',
            'payableBalanceToCompany',
            'totalMarketDue',
            'totalCustomerRecovery',
            'todaySales',
            'monthSales',
            'totalStockUnits',
            'totalCustomers',
            'recentRecoveries',
            'recentSales',
            'recentOrders',
            'pendingDemandsCount'
        ));
    }

    /**
     * View stock in distributor custody.
     */
    public function myStock(Request $request)
    {
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
            return back()->with('success', "Customer '{$customer->shop_name}' added successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error adding customer: ' . $e->getMessage());
        }
    }

    /**
     * Dedicated POS / Sale Screen for Distributor to sell to Retailers.
     */
    public function createSale()
    {
        $distributor = $this->getDistributor();

        // Get customers belonging to distributor
        $customers = DistributorCustomer::where('distributor_id', $distributor->id)
            ->where('status', 'active')
            ->orderBy('shop_name')
            ->get();

        // Get products available in distributor's inventory
        $stocks = DistributorStock::with(['product.unit', 'variant'])
            ->where('distributor_id', $distributor->id)
            ->where('qty', '>', 0)
            ->get();

        return view('distributor_panel.sales.create', compact('distributor', 'customers', 'stocks'));
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
            'product_id.*'            => 'required|exists:products,id',
            'qty'                     => 'required|array|min:1',
            'qty.*'                   => 'required|numeric|min:0.01',
            'unit_price'              => 'required|array|min:1',
            'unit_price.*'            => 'required|numeric|min:0',
            'discount'                => 'nullable|numeric|min:0',
            'paid_amount'             => 'nullable|numeric|min:0',
            'payment_type'            => 'required|in:cash,credit,partial',
        ]);

        DB::beginTransaction();
        try {
            $customer = DistributorCustomer::where('distributor_id', $distributor->id)
                ->findOrFail($request->distributor_customer_id);

            $subtotal = 0;
            $itemsData = [];

            foreach ($request->product_id as $index => $productId) {
                $qty = (float) ($request->qty[$index] ?? 0);
                $price = (float) ($request->unit_price[$index] ?? 0);
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
                    throw new \Exception("Insufficient stock for '{$prodName}'. Available: {$avail}, Requested: {$qty}");
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
                throw new \Exception('No valid items in sale.');
            }

            $discount = (float) ($request->discount ?? 0);
            $grandTotal = max(0, $subtotal - $discount);
            $paidAmount = min($grandTotal, (float) ($request->paid_amount ?? 0));
            $dueAmount = max(0, $grandTotal - $paidAmount);

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
                'payment_type'            => $request->payment_type,
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
                    'description'             => "Cash payment received for Invoice #{$sale->invoice_no}",
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
}

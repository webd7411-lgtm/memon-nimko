<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\DistributorOrder;
use App\Models\DistributorOrderItem;
use App\Models\DistributorStock;
use App\Models\DistributorLedger;
use App\Models\DistributorPayment;
use App\Models\Account;
use App\Models\AccountHead;
use App\Models\DistributorSale;
use App\Models\DistributorCustomer;
use App\Models\DistributorCustomerLedger;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\Stock;
use App\Models\Product;
use App\Models\DistributorClaim;
use App\Models\DistributorClaimItem;
use App\Models\ExpenseVoucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DistributorAdminController extends Controller
{
    /**
     * Display a listing of all distributors.
     */
    public function index(Request $request)
    {
        $query = Distributor::with('user')->withCount(['customers', 'stocks', 'orders']);

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->type) {
            if ($request->type === 'order_booker' || $request->type === 'booker') {
                $query->where(function($q) {
                    $q->where('type', 'order_booker')
                      ->orWhere('name', 'like', '%booker%');
                });
            } elseif ($request->type === 'distributor') {
                $query->where(function($q) {
                    $q->where('type', 'distributor')
                      ->orWhereNull('type')
                      ->where('name', 'not like', '%booker%');
                });
            }
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $distributors = $query->latest()->paginate(15);

        $totalReceivable = Distributor::where('status', 'active')->sum('current_balance');
        $activeDistributorsCount = Distributor::where('status', 'active')->count();
        $pendingOrdersCount = DistributorOrder::where('status', 'pending')->count();

        return view('admin_panel.distributors.index', compact('distributors', 'totalReceivable', 'activeDistributorsCount', 'pendingOrdersCount'));
    }

    /**
     * Store a newly created distributor and their login account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'city'            => 'nullable|string|max:100',
            'area'            => 'nullable|string|max:100',
            'address'         => 'nullable|string',
            'opening_balance' => 'nullable|numeric|min:0',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create User Account for Distributor
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'usertype' => 'distributor',
            ]);
            // Ensure Distributor role exists and assign safely
            try {
                if (class_exists(\Spatie\Permission\Models\Role::class)) {
                    $role = \Spatie\Permission\Models\Role::firstOrCreate([
                        'name'       => 'Distributor',
                        'guard_name' => 'web',
                    ]);

                    if (class_exists(\Spatie\Permission\Models\Permission::class)) {
                        $portalPerm = \Spatie\Permission\Models\Permission::firstOrCreate([
                            'name'       => 'Distributor Portal',
                            'guard_name' => 'web',
                        ]);
                        if (!$role->hasPermissionTo($portalPerm)) {
                            $role->givePermissionTo($portalPerm);
                        }
                    }

                    if (method_exists($user, 'assignRole')) {
                        $user->assignRole($role);
                    }
                }
            } catch (\Throwable $roleEx) {
                \Illuminate\Support\Facades\Log::warning('Distributor role auto-assignment notice: ' . $roleEx->getMessage());
            }

            // 2. Create Distributor Record
            $openingBalance = (float) ($request->opening_balance ?? 0);
            $distributor = Distributor::create([
                'user_id'         => $user->id,
                'name'            => $request->name,
                'code'            => Distributor::generateCode(),
                'type'            => $request->type ?: 'distributor',
                'phone'           => $request->phone,
                'cnic'            => $request->cnic,
                'city'            => $request->city,
                'area'            => $request->area,
                'address'         => $request->address,
                'opening_balance' => $openingBalance,
                'current_balance' => $openingBalance,
                'status'          => 'active',
            ]);

            // 3. If Opening Balance > 0, post to ledger
            if ($openingBalance > 0) {
                DistributorLedger::create([
                    'distributor_id' => $distributor->id,
                    'date'           => now()->format('Y-m-d'),
                    'type'           => 'opening',
                    'reference_no'   => 'OP-' . $distributor->code,
                    'description'    => 'Opening Balance on Registration',
                    'debit'          => $openingBalance,
                    'credit'         => 0,
                    'balance'        => $openingBalance,
                    'created_by'     => Auth::id(),
                ]);
            }

            DB::commit();
            return redirect()->route('admin.distributors.index')->with('success', 'Distributor account created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating distributor: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified distributor.
     */
    public function update(Request $request, $id)
    {
        $distributor = Distributor::with('user')->findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20',
            'city'     => 'nullable|string|max:100',
            'area'     => 'nullable|string|max:100',
            'address'  => 'nullable|string',
            'status'   => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            $distributor->update([
                'name'    => $request->name,
                'type'    => $request->type ?: ($distributor->type ?: 'distributor'),
                'phone'   => $request->phone,
                'cnic'    => $request->cnic,
                'city'    => $request->city,
                'area'    => $request->area,
                'address' => $request->address,
                'status'  => $request->status,
            ]);

            if ($distributor->user) {
                $distributor->user->update([
                    'name' => $request->name,
                ]);

                if (!empty($request->password)) {
                    $distributor->user->update([
                        'password' => Hash::make($request->password),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.distributors.index')->with('success', 'Distributor updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Update error: ' . $e->getMessage());
        }
    }

    /**
     * View purchase orders / demand requests placed by distributors.
     */
    public function orders(Request $request)
    {
        $query = DistributorOrder::with(['distributor', 'user', 'approver', 'items.product']);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->distributor_id) {
            $query->where('distributor_id', $request->distributor_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }

        $orders = $query->latest()->paginate(15);
        $distributors = Distributor::where('status', 'active')->get();
        $pendingCount = DistributorOrder::where('status', 'pending')->count();

        return view('admin_panel.distributors.orders', compact('orders', 'distributors', 'pendingCount'));
    }

    /**
     * Show form for Super Admin to directly issue / sell stock to a distributor.
     */
    public function createDirectSale()
    {
        $distributors = Distributor::where('status', 'active')->get();
        $warehouses = Warehouse::all();
        $products = Product::with(['unit', 'variants'])->orderBy('item_name')->get();

        return view('admin_panel.distributors.direct_sale', compact('distributors', 'warehouses', 'products'));
    }

    /**
     * Store direct sale / stock issue to a distributor.
     */
    public function storeDirectSale(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'warehouse_id'   => 'nullable',
            'product_id'     => 'required|array|min:1',
            'product_id.*'   => 'required|exists:products,id',
            'quantity'       => 'required|array|min:1',
            'quantity.*'     => 'required|numeric|min:0.01',
            'unit_price'     => 'required|array|min:1',
            'unit_price.*'   => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $distributor = Distributor::findOrFail($request->distributor_id);
            $warehouseId = $request->warehouse_id ?: (Warehouse::first()->id ?? 'shop');
            $totalAmount = 0;
            $totalQty = 0;
            $itemsData = [];

            foreach ($request->product_id as $index => $productId) {
                $qty = (float) ($request->quantity[$index] ?? 0);
                $price = (float) ($request->unit_price[$index] ?? 0);
                $variantId = $request->variant_id[$index] ?? null;

                if ($qty <= 0) continue;

                $subtotal = $qty * $price;
                $totalQty += $qty;
                $totalAmount += $subtotal;

                // 1. Deduct from Company Warehouse
                if ($warehouseId !== 'shop') {
                    $whStock = WarehouseStock::firstOrCreate([
                        'warehouse_id' => $warehouseId,
                        'product_id'   => $productId,
                        'variant_id'   => $variantId,
                    ], ['quantity' => 0, 'price' => 0]);

                    $whStock->quantity = max(0, (float)$whStock->quantity - $qty);
                    $whStock->save();
                } else {
                    $shopStock = Stock::firstOrCreate([
                        'product_id'   => $productId,
                        'variant_id'   => $variantId,
                        'warehouse_id' => null,
                    ], ['qty' => 0]);
                    $shopStock->qty = max(0, (float)$shopStock->qty - $qty);
                    $shopStock->save();
                }

                // 2. Add to Distributor Custody Stock
                $distStock = DistributorStock::firstOrCreate([
                    'distributor_id' => $distributor->id,
                    'product_id'     => $productId,
                    'variant_id'     => $variantId,
                ], ['qty' => 0, 'avg_price' => $price]);

                $distStock->qty += $qty;
                $distStock->avg_price = $price;
                $distStock->save();

                $itemsData[] = [
                    'product_id'    => $productId,
                    'variant_id'    => $variantId,
                    'requested_qty' => $qty,
                    'approved_qty'  => $qty,
                    'unit_price'    => $price,
                    'subtotal'      => $subtotal,
                ];
            }

            if (empty($itemsData)) {
                return back()->withInput()->with('error', 'Please enter at least one product with quantity.');
            }

            // 3. Create Completed Distributor Order Record
            $order = DistributorOrder::create([
                'order_no'                => DistributorOrder::generateOrderNo(),
                'distributor_id'          => $distributor->id,
                'user_id'                 => Auth::id(),
                'order_date'              => $request->sale_date ?: now()->format('Y-m-d'),
                'total_items'             => count($itemsData),
                'total_qty'               => $totalQty,
                'total_amount'            => $totalAmount,
                'status'                  => 'delivered',
                'dispatched_warehouse_id' => ($warehouseId !== 'shop' ? $warehouseId : null),
                'approved_by'             => Auth::id(),
                'approved_at'             => now(),
                'notes'                   => 'Direct Sale from Head Office',
                'admin_remarks'           => $request->remarks,
            ]);

            foreach ($itemsData as $item) {
                $item['distributor_order_id'] = $order->id;
                DistributorOrderItem::create($item);
            }

            // 4. Update Distributor Ledger (Debit)
            $newBalance = (float) $distributor->current_balance + $totalAmount;
            DistributorLedger::create([
                'distributor_id' => $distributor->id,
                'date'           => $request->sale_date ?: now()->format('Y-m-d'),
                'type'           => 'dispatch',
                'reference_no'   => $order->order_no,
                'description'    => "Direct Sale / Stock Issue #{$order->order_no} (Items: " . count($itemsData) . ", Total Qty: {$totalQty})",
                'debit'          => $totalAmount,
                'credit'         => 0,
                'balance'        => $newBalance,
                'created_by'     => Auth::id(),
            ]);

            $distributor->update(['current_balance' => $newBalance]);

            DB::commit();
            return redirect()->route('admin.distributors.orders.show', $order->id)
                ->with('success', "Stock issued successfully to {$distributor->name}! Invoice #{$order->order_no} created and ledger updated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error issuing stock: ' . $e->getMessage());
        }
    }

    /**
     * Display register/history of all sales issued to distributors (similar to regular /sale).
     */
    public function salesList(Request $request)
    {
        $query = DistributorOrder::with(['distributor', 'user', 'items.product', 'items.variant'])
            ->whereIn('status', ['dispatched', 'delivered']);

        if ($request->distributor_id) {
            $query->where('distributor_id', $request->distributor_id);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_no', 'like', "%{$search}%")
                  ->orWhereHas('distributor', fn($d) => $d->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
            });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }

        $sales = $query->latest('id')->paginate(15);
        $distributors = Distributor::where('status', 'active')->get();

        $totalSalesAmount = DistributorOrder::whereIn('status', ['dispatched', 'delivered'])->sum('total_amount');
        $totalItemsDispatched = DistributorOrder::whereIn('status', ['dispatched', 'delivered'])->sum('total_qty');

        return view('admin_panel.distributors.sales_list', compact('sales', 'distributors', 'totalSalesAmount', 'totalItemsDispatched'));
    }

    /**
     * View/Print Invoice for a distributor sale.
     */
    public function saleInvoice($id)
    {
        $order = DistributorOrder::with(['distributor', 'user', 'items.product.unit', 'items.variant'])->findOrFail($id);
        return view('admin_panel.distributors.sale_invoice', compact('order'));
    }

    /**
     * Show demand request details for review and approval.
     */
    public function showOrder($id)
    {
        $order = DistributorOrder::with(['distributor', 'user', 'items.product.unit', 'items.variant'])->findOrFail($id);
        $warehouses = Warehouse::all();

        // Stock availability in main warehouse for each item
        $stockMap = [];
        foreach ($order->items as $item) {
            $stockQty = WarehouseStock::where('product_id', $item->product_id)
                ->when($item->variant_id, fn($q) => $q->where('variant_id', $item->variant_id))
                ->sum('quantity');

            // Also check shop/branch stock if warehouse stock is 0
            if ($stockQty <= 0) {
                $stockQty = Stock::where('product_id', $item->product_id)
                    ->when($item->variant_id, fn($q) => $q->where('variant_id', $item->variant_id))
                    ->sum('qty');
            }

            $stockMap[$item->id] = (float) $stockQty;
        }

        return view('admin_panel.distributors.order_detail', compact('order', 'warehouses', 'stockMap'));
    }

    /**
     * Approve and Dispatch goods to distributor.
     */
    public function dispatchOrder(Request $request, $id)
    {
        $order = DistributorOrder::with(['distributor', 'items.product'])->findOrFail($id);

        if ($order->status === 'dispatched' || $order->status === 'delivered') {
            return back()->with('error', 'This order has already been dispatched.');
        }

        $request->validate([
            'warehouse_id'     => 'required',
            'approved_qty'     => 'required|array',
            'approved_qty.*'   => 'nullable|numeric|min:0',
            'unit_price'       => 'required|array',
            'unit_price.*'     => 'nullable|numeric|min:0',
            'admin_remarks'    => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $totalQty = 0;
            $warehouseId = $request->warehouse_id;

            foreach ($order->items as $item) {
                $approvedQty = (float) ($request->approved_qty[$item->id] ?? 0);
                $unitPrice = (float) ($request->unit_price[$item->id] ?? $item->unit_price);
                $subtotal = $approvedQty * $unitPrice;

                $item->update([
                    'approved_qty' => $approvedQty,
                    'unit_price'   => $unitPrice,
                    'subtotal'     => $subtotal,
                ]);

                if ($approvedQty > 0) {
                    $totalQty += $approvedQty;
                    $totalAmount += $subtotal;

                    // 1. Deduct from Warehouse Stock if warehouse selected
                    if ($warehouseId !== 'shop') {
                        $whStock = WarehouseStock::firstOrCreate([
                            'warehouse_id' => $warehouseId,
                            'product_id'   => $item->product_id,
                            'variant_id'   => $item->variant_id,
                        ], ['quantity' => 0, 'price' => 0]);

                        $whStock->quantity = max(0, (float)$whStock->quantity - $approvedQty);
                        $whStock->save();
                    } else {
                        // Shop stock
                        $shopStock = Stock::firstOrCreate([
                            'product_id'   => $item->product_id,
                            'variant_id'   => $item->variant_id,
                            'warehouse_id' => null,
                        ], ['qty' => 0]);
                        $shopStock->qty = max(0, (float)$shopStock->qty - $approvedQty);
                        $shopStock->save();
                    }

                    // 2. Add to Distributor's Custody Stock
                    $distStock = DistributorStock::firstOrCreate([
                        'distributor_id' => $order->distributor_id,
                        'product_id'     => $item->product_id,
                        'variant_id'     => $item->variant_id,
                    ], ['qty' => 0, 'avg_price' => $unitPrice]);

                    $distStock->qty += $approvedQty;
                    $distStock->avg_price = $unitPrice;
                    $distStock->save();
                }
            }

            // 3. Update Order status
            $order->update([
                'status'                    => 'dispatched',
                'dispatched_warehouse_id'   => ($warehouseId !== 'shop' ? $warehouseId : null),
                'approved_by'               => Auth::id(),
                'approved_at'               => now(),
                'total_qty'                 => $totalQty,
                'total_amount'              => $totalAmount,
                'admin_remarks'             => $request->admin_remarks,
            ]);

            // 4. Update Distributor Ledger (Debit entry - Distributor owes company)
            if ($totalAmount > 0) {
                $distributor = $order->distributor;
                $newBalance = (float) $distributor->current_balance + $totalAmount;

                DistributorLedger::create([
                    'distributor_id' => $distributor->id,
                    'date'           => now()->format('Y-m-d'),
                    'type'           => 'dispatch',
                    'reference_no'   => $order->order_no,
                    'description'    => "Goods dispatched against Order #{$order->order_no} (Items: {$order->items->count()}, Total Qty: {$totalQty})",
                    'debit'          => $totalAmount,
                    'credit'         => 0,
                    'balance'        => $newBalance,
                    'created_by'     => Auth::id(),
                ]);

                $distributor->update(['current_balance' => $newBalance]);
            }

            DB::commit();
            return redirect()->route('admin.distributors.orders')->with('success', "Order #{$order->order_no} dispatched successfully! Distributor stock & ledger updated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Dispatch error: ' . $e->getMessage());
        }
    }

    /**
     * View stock in custody of all distributors.
     */
    public function stocks(Request $request)
    {
        $query = DistributorStock::with(['distributor', 'product.unit', 'variant'])->where('qty', '>', 0);

        if ($request->distributor_id) {
            $query->where('distributor_id', $request->distributor_id);
        }

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        $stocks = $query->latest('updated_at')->paginate(20);
        $distributors = Distributor::where('status', 'active')->get();
        $products = Product::select('id', 'item_name')->orderBy('item_name')->get();

        return view('admin_panel.distributors.stocks', compact('stocks', 'distributors', 'products'));
    }

    /**
     * View Company <-> Distributor financial statement / ledger.
     */
    public function ledger($id, Request $request)
    {
        $distributor = Distributor::with('user')->findOrFail($id);

        $query = DistributorLedger::where('distributor_id', $distributor->id);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $entries = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        $totalDebit = $entries->sum('debit');
        $totalCredit = $entries->sum('credit');

        return view('admin_panel.distributors.ledger', compact('distributor', 'entries', 'totalDebit', 'totalCredit'));
    }

    /**
     * Record payment received from distributor to company.
     */
    public function storePayment(Request $request, $id)
    {
        $distributor = Distributor::findOrFail($id);

        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'date'         => 'required|date',
            'payment_mode' => 'required|string',
            'reference_no' => 'nullable|string',
            'notes'        => 'nullable|string',
        ]);

        $amount = (float) $request->amount;

        DB::beginTransaction();
        try {
            $newBalance = (float) $distributor->current_balance - $amount;
            $paymentNo = DistributorPayment::generatePaymentNo();

            DistributorPayment::create([
                'payment_no'            => $paymentNo,
                'distributor_id'        => $distributor->id,
                'amount'                => $amount,
                'payment_date'          => $request->date,
                'payment_method'        => $request->payment_mode,
                'transaction_reference' => $request->reference_no,
                'notes'                 => $request->notes,
                'status'                => 'approved',
                'approved_by'           => Auth::id(),
                'approved_at'           => now(),
                'created_by'            => Auth::id(),
            ]);

            DistributorLedger::create([
                'distributor_id' => $distributor->id,
                'date'           => $request->date,
                'type'           => 'payment_received',
                'reference_no'   => $request->reference_no ?: $paymentNo,
                'description'    => "Payment received via {$request->payment_mode}. " . ($request->notes ? "Notes: {$request->notes}" : ''),
                'debit'          => 0,
                'credit'         => $amount,
                'balance'        => $newBalance,
                'created_by'     => Auth::id(),
            ]);

            $distributor->update(['current_balance' => $newBalance]);

            DB::commit();
            return back()->with('success', "Payment of Rs. " . number_format($amount, 2) . " recorded successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * Central audit view of secondary market sales made by distributors.
     */
    public function secondarySales(Request $request)
    {
        $query = DistributorSale::with(['distributor', 'customer', 'items.product']);

        if ($request->distributor_id) {
            $query->where('distributor_id', $request->distributor_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('sale_date', [$request->start_date, $request->end_date]);
        }

        $sales = $query->latest('sale_date')->latest('id')->paginate(20);
        $distributors = Distributor::where('status', 'active')->get();

        $totalMarketSales = DistributorSale::sum('grand_total');
        $totalMarketCollected = DistributorSale::sum('paid_amount');
        $totalMarketDue = DistributorSale::sum('due_amount');

        return view('admin_panel.distributors.secondary_sales', compact('sales', 'distributors', 'totalMarketSales', 'totalMarketCollected', 'totalMarketDue'));
    }

    /**
     * List all distributor payments / collections and approval requests.
     */
    public function payments(Request $request)
    {
        $query = DistributorPayment::with(['distributor', 'approver', 'creator', 'account.head'])
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('distributor_id')) {
            $query->where('distributor_id', $request->distributor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
        }

        $payments = $query->paginate(20)->withQueryString();
        $distributors = Distributor::orderBy('name')->get();

        // Fetch company Cash & Bank Accounts
        $companyAccounts = Account::where('status', 1)->with('head')->orderBy('head_id')->orderBy('title')->get();

        $totalApproved = DistributorPayment::where('status', 'approved')->sum('amount');
        $totalPending = DistributorPayment::where('status', 'pending')->sum('amount');
        $pendingPaymentsCount = DistributorPayment::where('status', 'pending')->count();
        $totalRejected = DistributorPayment::where('status', 'rejected')->sum('amount');

        return view('admin_panel.distributors.payments', compact(
            'payments',
            'distributors',
            'companyAccounts',
            'totalApproved',
            'totalPending',
            'pendingPaymentsCount',
            'totalRejected'
        ));
    }

    /**
     * Super Admin / Company approves a distributor payment.
     * Credits distributor ledger and decrements current balance.
     */
    public function approvePayment(Request $request, $id)
    {
        $payment = DistributorPayment::with(['distributor', 'account'])->findOrFail($id);

        if ($payment->status === 'approved') {
            return back()->with('info', "Payment #{$payment->payment_no} is already approved.");
        }

        DB::beginTransaction();
        try {
            $distributor = $payment->distributor;
            $newBalance = (float) $distributor->current_balance - (float) $payment->amount;

            // Target Account in Chart of Accounts
            $accountId = $request->account_id ?: $payment->account_id;
            $account = null;
            if ($accountId) {
                $account = Account::with('head')->find($accountId);
                if ($account) {
                    // Automatically add the payment to Company Account balance!
                    $account->opening_balance = (float)$account->opening_balance + (float)$payment->amount;
                    $account->save();
                }
            }

            $accountNote = $account ? " (Deposited into: {$account->title})" : '';

            // 1. Post to Distributor Ledger (Credit reduces amount owed to company)
            DistributorLedger::create([
                'distributor_id' => $distributor->id,
                'date'           => $payment->payment_date ? $payment->payment_date->format('Y-m-d') : now()->format('Y-m-d'),
                'type'           => 'payment_received',
                'reference_no'   => $payment->payment_no,
                'description'    => "Payment approved via " . ucwords(str_replace('_', ' ', $payment->payment_method)) . 
                                    ($payment->bank_name ? " ({$payment->bank_name})" : '') .
                                    $accountNote .
                                    ($payment->transaction_reference ? " Ref #{$payment->transaction_reference}" : '') .
                                    ($request->approval_notes ? ". Note: {$request->approval_notes}" : ''),
                'debit'          => 0,
                'credit'         => (float) $payment->amount,
                'balance'        => $newBalance,
                'created_by'     => Auth::id(),
            ]);

            // 2. Decrement Distributor Current Balance
            $distributor->update(['current_balance' => $newBalance]);

            // 3. Update Payment Record to Approved
            $payment->update([
                'status'      => 'approved',
                'account_id'  => $account ? $account->id : $payment->account_id,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'notes'       => $payment->notes . ($request->approval_notes ? " | Approval: {$request->approval_notes}" : ''),
            ]);

            DB::commit();
            return back()->with('success', "Payment #{$payment->payment_no} of Rs. " . number_format($payment->amount, 2) . " from '{$distributor->name}' approved" . ($account ? " & credited to '{$account->title}'" : "") . " successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error approving payment: ' . $e->getMessage());
        }
    }

    /**
     * Reject a distributor payment submission with reason.
     */
    public function rejectPayment(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $payment = DistributorPayment::findOrFail($id);

        if ($payment->status === 'approved') {
            return back()->with('error', "Cannot reject an already approved payment as it has already been credited to ledger.");
        }

        $payment->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
        ]);

        return back()->with('warning', "Payment #{$payment->payment_no} has been rejected.");
    }

    /**
     * Directly record and auto-approve a payment from any distributor to company.
     */
    public function storeAdminPayment(Request $request)
    {
        $request->validate([
            'distributor_id'        => 'required|exists:distributors,id',
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

        $distributor = Distributor::findOrFail($request->distributor_id);

        $accountId = $request->account_id ? (int)$request->account_id : null;
        $bankName = $request->bank_name;
        $accountNumber = $request->account_number;
        $paymentMethod = $request->payment_method ?: 'cash';

        $account = null;
        if ($accountId) {
            $account = Account::with('head')->find($accountId);
            if ($account) {
                $headName = strtolower($account->head->name ?? '');
                $accTitle = strtolower($account->title ?? '');
                if (!$request->filled('payment_method')) {
                    $paymentMethod = (str_contains($headName, 'cash') || str_contains($accTitle, 'cash')) ? 'cash' : 'bank_transfer';
                }
                if (empty($bankName)) {
                    $bankName = $account->title;
                }
                if (empty($accountNumber)) {
                    $accountNumber = $account->account_code;
                }
            }
        }

        $slipFileName = null;
        if ($request->hasFile('slip_image')) {
            $file = $request->file('slip_image');
            $slipFileName = 'slip_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/distributor_payments'), $slipFileName);
        }

        DB::beginTransaction();
        try {
            $amount = (float) $request->amount;
            $newBalance = (float) $distributor->current_balance - $amount;
            $paymentNo = DistributorPayment::generatePaymentNo();

            // Automatically credit funds to company Cash/Bank Account
            if ($account) {
                $account->opening_balance = (float)$account->opening_balance + $amount;
                $account->save();
            }

            // 1. Create Payment (auto-approved)
            $payment = DistributorPayment::create([
                'payment_no'            => $paymentNo,
                'distributor_id'        => $distributor->id,
                'amount'                => $amount,
                'payment_date'          => $request->payment_date,
                'payment_method'        => $paymentMethod,
                'account_id'            => $account ? $account->id : null,
                'bank_name'             => $bankName,
                'account_number'        => $accountNumber,
                'transaction_reference' => $request->transaction_reference,
                'slip_image'            => $slipFileName,
                'notes'                 => $request->notes,
                'status'                => 'approved',
                'approved_by'           => Auth::id(),
                'approved_at'           => now(),
                'created_by'            => Auth::id(),
            ]);

            // 2. Post to Ledger
            $accountNote = $account ? " (Deposited into: {$account->title})" : '';
            DistributorLedger::create([
                'distributor_id' => $distributor->id,
                'date'           => $request->payment_date,
                'type'           => 'payment_received',
                'reference_no'   => $paymentNo,
                'description'    => "Direct payment received via " . ucwords(str_replace('_', ' ', $paymentMethod)) .
                                    ($bankName ? " ({$bankName})" : '') .
                                    $accountNote .
                                    ($request->transaction_reference ? " Ref #{$request->transaction_reference}" : '') .
                                    ($request->notes ? ". Note: {$request->notes}" : ''),
                'debit'          => 0,
                'credit'         => $amount,
                'balance'        => $newBalance,
                'created_by'     => Auth::id(),
            ]);

            // 3. Update Current Balance
            $distributor->update(['current_balance' => $newBalance]);

            DB::commit();
            return back()->with('success', "Payment #{$paymentNo} of Rs. " . number_format($amount, 2) . " recorded and credited to {$distributor->name}'s ledger" . ($account ? " and '{$account->title}'" : "") . " successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    /**
     * Super Admin update payment details (even after approval).
     */
    public function updatePayment(Request $request, $id)
    {
        $payment = DistributorPayment::with(['distributor', 'account'])->findOrFail($id);

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

        $newAmount = (float) $request->amount;
        $oldAmount = (float) $payment->amount;
        $distributor = $payment->distributor;

        DB::beginTransaction();
        try {
            $accountId = $request->account_id ? (int)$request->account_id : null;
            $bankName = $request->bank_name;
            $accountNumber = $request->account_number;
            $paymentMethod = $request->payment_method ?: $payment->payment_method;

            $newAccount = $accountId ? Account::find($accountId) : null;
            if ($newAccount) {
                if (empty($bankName)) $bankName = $newAccount->title;
                if (empty($accountNumber)) $accountNumber = $newAccount->account_code;
            }

            // If already approved, adjust ledger, distributor balance, and company accounts
            if ($payment->status === 'approved') {
                $amountDiff = $newAmount - $oldAmount;

                // Adjust distributor balance
                if ($distributor && $amountDiff != 0) {
                    $distributor->current_balance -= $amountDiff;
                    $distributor->save();
                }

                // Adjust ledger entry
                $ledgerEntry = DistributorLedger::where('distributor_id', $payment->distributor_id)
                    ->where('reference_no', $payment->payment_no)
                    ->first();
                if ($ledgerEntry) {
                    $ledgerEntry->date = $request->payment_date;
                    $ledgerEntry->credit = $newAmount;
                    if ($amountDiff != 0) {
                        $ledgerEntry->balance -= $amountDiff;
                    }
                    $accountNote = $newAccount ? " (Deposited into: {$newAccount->title})" : '';
                    $ledgerEntry->description = "Payment approved via " . ucwords(str_replace('_', ' ', $paymentMethod)) .
                        ($bankName ? " ({$bankName})" : '') .
                        $accountNote .
                        ($request->transaction_reference ? " Ref #{$request->transaction_reference}" : '') .
                        ($request->notes ? ". Note: {$request->notes}" : '');
                    $ledgerEntry->save();
                }

                // Adjust company bank / cash account balance
                $oldAccountId = $payment->account_id;
                if ($oldAccountId == $accountId) {
                    if ($newAccount && $amountDiff != 0) {
                        $newAccount->opening_balance += $amountDiff;
                        $newAccount->save();
                    }
                } else {
                    if ($oldAccountId) {
                        $oldAcc = Account::find($oldAccountId);
                        if ($oldAcc) {
                            $oldAcc->opening_balance -= $oldAmount;
                            $oldAcc->save();
                        }
                    }
                    if ($newAccount) {
                        $newAccount->opening_balance += $newAmount;
                        $newAccount->save();
                    }
                }
            }

            // Slip image
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
                'amount'                => $newAmount,
                'payment_date'          => $request->payment_date,
                'payment_method'        => $paymentMethod,
                'account_id'            => $accountId,
                'bank_name'             => $bankName,
                'account_number'        => $accountNumber,
                'transaction_reference' => $request->transaction_reference,
                'slip_image'            => $slipFileName,
                'notes'                 => $request->notes,
            ]);

            DB::commit();
            return back()->with('success', "Payment #{$payment->payment_no} updated successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating payment: ' . $e->getMessage());
        }
    }

    /**
     * Super Admin delete payment.
     */
    public function destroyPayment($id)
    {
        $payment = DistributorPayment::with('distributor')->findOrFail($id);

        DB::beginTransaction();
        try {
            // If was approved, reverse ledger and account balance
            if ($payment->status === 'approved') {
                $distributor = $payment->distributor;
                if ($distributor) {
                    $distributor->current_balance += (float)$payment->amount;
                    $distributor->save();
                }

                // Delete ledger entry
                DistributorLedger::where('distributor_id', $payment->distributor_id)
                    ->where('reference_no', $payment->payment_no)
                    ->delete();

                // Reverse company account balance
                if ($payment->account_id) {
                    $account = Account::find($payment->account_id);
                    if ($account) {
                        $account->opening_balance -= (float)$payment->amount;
                        $account->save();
                    }
                }
            }

            if ($payment->slip_image && file_exists(public_path('uploads/distributor_payments/' . $payment->slip_image))) {
                @unlink(public_path('uploads/distributor_payments/' . $payment->slip_image));
            }

            $paymentNo = $payment->payment_no;
            $payment->delete();

            DB::commit();
            return back()->with('success', "Payment #{$paymentNo} has been deleted.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting payment: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of all distributor retail customers and their balances.
     */
    public function customers(Request $request)
    {
        $query = DistributorCustomer::with(['distributor'])
            ->withCount(['sales', 'ledgers']);

        if ($request->filled('distributor_id')) {
            $query->where('distributor_id', $request->distributor_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('shop_name', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('distributor', function($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%")
                         ->orWhere('city', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('balance_status')) {
            if ($request->balance_status === 'due') {
                $query->where('current_balance', '>', 0);
            } elseif ($request->balance_status === 'zero') {
                $query->where('current_balance', '<=', 0);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $customers = $query->latest('id')->paginate(15)->appends($request->all());

        $distributors = Distributor::where('status', 'active')->orderBy('name')->get();
        $accounts = Account::where('status', 1)->with('head')->orderBy('title')->get();
        $totalCustomers = DistributorCustomer::count();
        $totalMarketDue = DistributorCustomer::sum('current_balance');
        $customersWithDueCount = DistributorCustomer::where('current_balance', '>', 0)->count();

        return view('admin_panel.distributors.customers', compact(
            'customers',
            'distributors',
            'accounts',
            'totalCustomers',
            'totalMarketDue',
            'customersWithDueCount'
        ));
    }

    /**
     * Display the ledger statement of a distributor's customer.
     */
    public function customerLedger($id, Request $request)
    {
        $customer = DistributorCustomer::with('distributor')->findOrFail($id);
        $distributor = $customer->distributor;

        $query = DistributorCustomerLedger::where('distributor_customer_id', $customer->id);

        $openingBalanceBeforeFilter = 0;
        if ($request->filled('start_date')) {
            $prevDebit = DistributorCustomerLedger::where('distributor_customer_id', $customer->id)
                ->where('date', '<', $request->start_date)
                ->sum('debit');
            $prevCredit = DistributorCustomerLedger::where('distributor_customer_id', $customer->id)
                ->where('date', '<', $request->start_date)
                ->sum('credit');
            $openingBalanceBeforeFilter = (float)$prevDebit - (float)$prevCredit;

            if ($request->filled('end_date')) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            } else {
                $query->where('date', '>=', $request->start_date);
            }
        } elseif ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        $entries = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        $totalDebit = $entries->sum('debit');
        $totalCredit = $entries->sum('credit');

        // Pre-fetch related distributor sales to link invoice details if available
        $invoices = DistributorSale::where('distributor_customer_id', $customer->id)
            ->pluck('id', 'invoice_no')
            ->toArray();

        $accounts = Account::where('status', 1)->with('head')->orderBy('title')->get();

        return view('admin_panel.distributors.customer_ledger', compact(
            'customer',
            'distributor',
            'entries',
            'totalDebit',
            'totalCredit',
            'openingBalanceBeforeFilter',
            'invoices',
            'accounts'
        ));
    }

    /**
     * Store new customer registered under a distributor.
     */
    public function storeCustomer(Request $request)
    {
        $request->validate([
            'distributor_id'    => 'required|exists:distributors,id',
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

        DB::beginTransaction();
        try {
            $customer = DistributorCustomer::create([
                'distributor_id'    => $request->distributor_id,
                'shop_name'         => $request->shop_name,
                'owner_name'        => $request->owner_name,
                'phone'             => $request->phone,
                'area'              => $request->area,
                'address'           => $request->address,
                'approved_discount' => $approvedDiscount,
                'opening_balance'   => $openingBalance,
                'current_balance'   => $openingBalance,
                'status'            => 'active',
            ]);

            if ($openingBalance > 0) {
                DistributorCustomerLedger::create([
                    'distributor_customer_id' => $customer->id,
                    'distributor_id'          => $request->distributor_id,
                    'date'                    => now()->format('Y-m-d'),
                    'type'                    => 'opening',
                    'reference_no'            => 'OP-' . $customer->id,
                    'description'             => 'Opening Balance on Customer Registration',
                    'debit'                   => $openingBalance,
                    'credit'                  => 0,
                    'balance'                 => $openingBalance,
                ]);
            }

            DB::commit();
            return back()->with('success', "Retail customer '{$customer->shop_name}' created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating customer: ' . $e->getMessage());
        }
    }

    /**
     * Update existing retail customer details.
     */
    public function updateCustomer(Request $request, $id)
    {
        $customer = DistributorCustomer::findOrFail($id);

        $request->validate([
            'distributor_id'    => 'required|exists:distributors,id',
            'shop_name'         => 'required|string|max:255',
            'owner_name'        => 'nullable|string|max:255',
            'phone'             => 'nullable|string|max:20',
            'area'              => 'nullable|string|max:100',
            'address'           => 'nullable|string',
            'approved_discount' => 'nullable|numeric|min:0|max:100',
            'status'            => 'nullable|string|in:active,inactive',
        ]);

        $customer->update([
            'distributor_id'    => $request->distributor_id,
            'shop_name'         => $request->shop_name,
            'owner_name'        => $request->owner_name,
            'phone'             => $request->phone,
            'area'              => $request->area,
            'address'           => $request->address,
            'approved_discount' => (float) ($request->approved_discount ?? 0),
            'status'            => $request->status ?? 'active',
        ]);

        return back()->with('success', "Customer '{$customer->shop_name}' details updated successfully!");
    }

    /**
     * Delete customer if no sales or ledger history exists.
     */
    public function destroyCustomer($id)
    {
        $customer = DistributorCustomer::findOrFail($id);

        if ($customer->sales()->count() > 0 || $customer->ledgers()->where('type', '!=', 'opening')->count() > 0) {
            return back()->with('error', "Cannot delete customer '{$customer->shop_name}' because invoices or payment ledgers are associated with it.");
        }

        $customer->ledgers()->delete();
        $customer->delete();

        return back()->with('success', "Customer '{$customer->shop_name}' deleted successfully.");
    }

    /**
     * Store payment collection from retail customer on behalf of distributor.
     */
    public function storeCustomerPayment(Request $request, $id)
    {
        $customer = DistributorCustomer::with('distributor')->findOrFail($id);

        $request->validate([
            'amount'                => 'required|numeric|min:0.01',
            'date'                  => 'required|date',
            'account_id'            => 'nullable|exists:accounts,id',
            'payment_method'        => 'nullable|string|max:50',
            'transaction_reference' => 'nullable|string|max:100',
            'slip_image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'adjust_distributor'    => 'nullable',
            'notes'                 => 'nullable|string|max:1000',
        ]);

        $amount = (float) $request->amount;
        $accountId = $request->account_id ? (int)$request->account_id : null;
        $paymentMethod = $request->payment_method ?: 'cash';
        $shouldAdjustDistributor = $request->has('adjust_distributor') ? $request->boolean('adjust_distributor') : true;

        $account = null;
        $bankName = null;
        $accountNumber = null;
        if ($accountId) {
            $account = Account::with('head')->find($accountId);
            if ($account) {
                $bankName = $account->title;
                $accountNumber = $account->account_code;
                if (!$request->filled('payment_method')) {
                    $headName = strtolower($account->head->name ?? '');
                    $accTitle = strtolower($account->title ?? '');
                    $paymentMethod = (str_contains($headName, 'cash') || str_contains($accTitle, 'cash')) ? 'cash' : 'bank_transfer';
                }
            }
        }

        $slipFileName = null;
        if ($request->hasFile('slip_image')) {
            $file = $request->file('slip_image');
            $slipFileName = 'cust_slip_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/distributor_payments'), $slipFileName);
        }

        $lastRec = DistributorCustomerLedger::where('type', 'payment_received')->latest('id')->first();
        $nextRecId = $lastRec ? ($lastRec->id + 1) : 1;
        $receiptNo = 'REC-' . str_pad($nextRecId, 4, '0', STR_PAD_LEFT);
        $methodLabel = ucwords(str_replace('_', ' ', $paymentMethod));

        DB::beginTransaction();
        try {
            // 1. Credit retail customer's outstanding balance
            $newCustomerBalance = (float) $customer->current_balance - $amount;
            
            $customerDesc = "Direct payment received via {$methodLabel}";
            if ($account) {
                $customerDesc .= " (Deposited into: {$account->title})";
            }
            if ($request->transaction_reference) {
                $customerDesc .= " Ref #{$request->transaction_reference}";
            }
            if ($request->notes) {
                $customerDesc .= ". Note: {$request->notes}";
            }

            DistributorCustomerLedger::create([
                'distributor_customer_id' => $customer->id,
                'distributor_id'          => $customer->distributor_id,
                'date'                    => $request->date,
                'type'                    => 'payment_received',
                'reference_no'            => $request->transaction_reference ?: $receiptNo,
                'description'             => $customerDesc,
                'debit'                   => 0,
                'credit'                  => $amount,
                'balance'                 => $newCustomerBalance,
            ]);

            $customer->update(['current_balance' => $newCustomerBalance]);

            // 2. Deposit into Company Bank / Cash Account
            if ($account) {
                $account->opening_balance = (float)$account->opening_balance + $amount;
                $account->save();
            }

            // 3. Automatically credit Distributor's Company Ledger
            $distributorUpdatedMsg = '';
            if ($customer->distributor) {
                $distributor = $customer->distributor;
                $newDistBalance = (float) $distributor->current_balance - $amount;
                $distPaymentNo = DistributorPayment::generatePaymentNo();

                // Create approved DistributorPayment record
                DistributorPayment::create([
                    'payment_no'            => $distPaymentNo,
                    'distributor_id'        => $distributor->id,
                    'amount'                => $amount,
                    'payment_date'          => $request->date,
                    'payment_method'        => $paymentMethod,
                    'account_id'            => $account ? $account->id : null,
                    'bank_name'             => $bankName,
                    'account_number'        => $accountNumber,
                    'transaction_reference' => $request->transaction_reference ?: $receiptNo,
                    'slip_image'            => $slipFileName,
                    'notes'                 => "Direct recovery from retail shop: {$customer->shop_name}" . ($request->notes ? ". Note: {$request->notes}" : ''),
                    'status'                => 'approved',
                    'approved_by'           => Auth::id(),
                    'approved_at'           => now(),
                    'created_by'            => Auth::id(),
                ]);

                // Create DistributorLedger entry
                $distDesc = "Direct recovery from customer '{$customer->shop_name}' via {$methodLabel}";
                if ($account) {
                    $distDesc .= " (Deposited into: {$account->title})";
                }
                if ($request->transaction_reference) {
                    $distDesc .= " Ref #{$request->transaction_reference}";
                }

                DistributorLedger::create([
                    'distributor_id' => $distributor->id,
                    'date'           => $request->date,
                    'type'           => 'payment_received',
                    'reference_no'   => $distPaymentNo,
                    'description'    => $distDesc,
                    'debit'          => 0,
                    'credit'         => $amount,
                    'balance'        => $newDistBalance,
                    'created_by'     => Auth::id(),
                ]);

                $distributor->update(['current_balance' => $newDistBalance]);
                $distributorUpdatedMsg = " & credited to '{$distributor->name}' company ledger";
            }

            DB::commit();

            $bankMsg = $account ? " deposited into '{$account->title}'" : "";
            return back()->with('success', "Payment of Rs. " . number_format($amount, 2) . " received from '{$customer->shop_name}'{$bankMsg}{$distributorUpdatedMsg} successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // DISTRIBUTOR CLAIMS & REIMBURSEMENTS (Trade Discounts, Damage, Freight)
    // =========================================================================

    /**
     * Display listing of all distributor claims with statistics and filter options.
     */
    public function claims(Request $request)
    {
        $distributors = Distributor::where('status', 'active')->orderBy('name')->get();
        $expenseHeads = AccountHead::where('status', 1)->orderBy('name')->get();
        $expenseAccounts = Account::where('status', 1)->orderBy('title')->get();
        $products = Product::select('id', 'item_name', 'price', 'wholesale_price')->orderBy('item_name')->get();

        $query = DistributorClaim::with(['distributor', 'items.product', 'expenseHead', 'expenseAccount', 'approver', 'submitter'])
            ->latest('id');

        if ($request->filled('distributor_id')) {
            $query->where('distributor_id', $request->distributor_id);
        }
        if ($request->filled('claim_type')) {
            $query->where('claim_type', $request->claim_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('claim_date', [$request->start_date, $request->end_date]);
        }

        $claims = $query->paginate(25)->withQueryString();

        // Statistics
        $totalClaimsCount = DistributorClaim::count();
        $pendingClaimsCount = DistributorClaim::where('status', 'pending')->count();
        $pendingAmount = DistributorClaim::where('status', 'pending')->sum('total_amount');
        $approvedAmount = DistributorClaim::where('status', 'approved')->sum('approved_amount');

        return view('admin_panel.distributors.claims', compact(
            'claims', 'distributors', 'expenseHeads', 'expenseAccounts', 'products',
            'totalClaimsCount', 'pendingClaimsCount', 'pendingAmount', 'approvedAmount'
        ));
    }

    /**
     * Record a new claim directly from Admin Panel on behalf of a distributor.
     */
    public function storeClaim(Request $request)
    {
        $request->validate([
            'distributor_id'  => 'required|exists:distributors,id',
            'claim_type'      => 'required|string',
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
                'distributor_id'  => $request->distributor_id,
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

            // Save items if any
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
            return back()->with('success', "Claim #{$claimNo} filed successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving claim: ' . $e->getMessage());
        }
    }

    /**
     * Approve and settle a distributor claim.
     * Credits Distributor Ledger & Books Company Expense.
     */
    public function approveClaim(Request $request, $id)
    {
        $claim = DistributorClaim::with(['distributor', 'items'])->findOrFail($id);

        if ($claim->status === 'approved') {
            return back()->with('info', "Claim #{$claim->claim_no} is already approved.");
        }

        $request->validate([
            'approved_amount'    => 'required|numeric|min:0.01',
            'settlement_type'    => 'required|in:credit_note,stock_replacement',
            'expense_account_id' => 'nullable|exists:accounts,id',
            'admin_notes'        => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $distributor = $claim->distributor;
            $approvedAmount = (float) $request->approved_amount;
            $settlementType = $request->settlement_type;

            // Target Expense Account
            $expenseAccount = null;
            if ($request->expense_account_id) {
                $expenseAccount = Account::with('head')->find($request->expense_account_id);
            }

            // 1. Record Company Expense Voucher
            $voucher = null;
            if ($approvedAmount > 0 && class_exists(ExpenseVoucher::class)) {
                try {
                    $evid = method_exists(ExpenseVoucher::class, 'generateInvoiceNo') 
                        ? ExpenseVoucher::generateInvoiceNo() 
                        : 'EV-' . date('Ymd') . '-' . rand(1000, 9999);

                    $voucher = ExpenseVoucher::create([
                        'evid'         => $evid,
                        'user_id'      => Auth::id(),
                        'branch_id'    => function_exists('active_branch_id') ? active_branch_id() : null,
                        'entry_date'   => now(),
                        'date'         => $claim->claim_date ? $claim->claim_date->format('Y-m-d') : now()->format('Y-m-d'),
                        'type'         => $expenseAccount ? $expenseAccount->head_id : 'Claim Expense',
                        'party_id'     => $expenseAccount ? $expenseAccount->id : null,
                        'remarks'      => ["Claim #{$claim->claim_no} ({$claim->type_label}) approved for {$distributor->name}. Settlement: " . ucfirst(str_replace('_', ' ', $settlementType))],
                        'amount'       => [(string)$approvedAmount],
                        'total_amount' => $approvedAmount,
                    ]);

                    if ($expenseAccount) {
                        $expenseAccount->opening_balance = (float)$expenseAccount->opening_balance + $approvedAmount;
                        $expenseAccount->save();
                    }
                } catch (\Throwable $ex) {
                    \Illuminate\Support\Facades\Log::warning('Claim expense voucher creation notice: ' . $ex->getMessage());
                }
            }

            // 2. Settlement Method
            if ($settlementType === 'credit_note') {
                // Post Credit entry to Distributor Ledger (Reduces their payable balance)
                $newBalance = (float) $distributor->current_balance - $approvedAmount;

                $desc = "Claim #{$claim->claim_no} Approved ({$claim->type_label}). Settled via Credit Note.";
                if ($expenseAccount) {
                    $desc .= " Expense booked under: {$expenseAccount->title}";
                }
                if ($request->admin_notes) {
                    $desc .= " | Note: {$request->admin_notes}";
                }

                DistributorLedger::create([
                    'distributor_id' => $distributor->id,
                    'date'           => now()->format('Y-m-d'),
                    'type'           => 'claim_adjustment',
                    'reference_no'   => $claim->claim_no,
                    'description'    => $desc,
                    'debit'          => 0,
                    'credit'         => $approvedAmount,
                    'balance'        => $newBalance,
                    'created_by'     => Auth::id(),
                ]);

                $distributor->update(['current_balance' => $newBalance]);
            } elseif ($settlementType === 'stock_replacement') {
                // Settle via Free Replacement stock in Distributor custody
                if ($claim->items && $claim->items->count() > 0) {
                    foreach ($claim->items as $item) {
                        if ($item->product_id && $item->qty > 0) {
                            $distStock = DistributorStock::firstOrCreate([
                                'distributor_id' => $distributor->id,
                                'product_id'     => $item->product_id,
                                'variant_id'     => $item->variant_id,
                            ], [
                                'qty'       => 0,
                                'avg_price' => $item->unit_rate ?: 0,
                            ]);
                            $distStock->qty += (float)$item->qty;
                            $distStock->save();
                        }
                    }
                }
            }

            // 3. Mark Claim as Approved
            $claim->update([
                'status'             => 'approved',
                'approved_amount'    => $approvedAmount,
                'settlement_type'    => $settlementType,
                'expense_head_id'    => $expenseAccount ? $expenseAccount->head_id : null,
                'expense_account_id' => $expenseAccount ? $expenseAccount->id : null,
                'expense_voucher_id' => $voucher ? $voucher->id : null,
                'admin_notes'        => $request->admin_notes,
                'approved_by'        => Auth::id(),
                'approved_at'        => now(),
            ]);

            DB::commit();

            $settleDesc = ($settlementType === 'credit_note')
                ? "Rs. " . number_format($approvedAmount, 2) . " credited to '{$distributor->name}' ledger & recorded in Company Expense"
                : "approved as Free Replacement Stock issued to custody";

            return back()->with('success', "Claim #{$claim->claim_no} successfully {$settleDesc}!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error approving claim: ' . $e->getMessage());
        }
    }

    /**
     * Reject a distributor claim with reason.
     */
    public function rejectClaim(Request $request, $id)
    {
        $claim = DistributorClaim::findOrFail($id);

        if ($claim->status === 'approved') {
            return back()->with('error', 'Cannot reject an already approved claim.');
        }

        $claim->update([
            'status'      => 'rejected',
            'admin_notes' => $request->admin_notes ?: 'Claim rejected by administrator.',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', "Claim #{$claim->claim_no} marked as rejected.");
    }

    /**
     * Delete an unapproved claim.
     */
    public function destroyClaim($id)
    {
        $claim = DistributorClaim::findOrFail($id);

        if ($claim->status === 'approved') {
            return back()->with('error', 'Approved claims cannot be deleted directly to maintain ledger integrity.');
        }

        if ($claim->attachment && file_exists(public_path($claim->attachment))) {
            @unlink(public_path($claim->attachment));
        }

        $claim->items()->delete();
        $claim->delete();

        return back()->with('success', "Claim deleted successfully.");
    }
}

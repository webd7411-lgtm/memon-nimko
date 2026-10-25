<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\DistributorOrder;
use App\Models\DistributorOrderItem;
use App\Models\DistributorStock;
use App\Models\DistributorLedger;
use App\Models\DistributorPayment;
use App\Models\DistributorSale;
use App\Models\DistributorCustomer;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\Stock;
use App\Models\Product;
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
        $query = DistributorPayment::with(['distributor', 'approver', 'creator'])
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

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
        }

        $payments = $query->paginate(20)->withQueryString();
        $distributors = Distributor::orderBy('name')->get();

        $totalApproved = DistributorPayment::where('status', 'approved')->sum('amount');
        $totalPending = DistributorPayment::where('status', 'pending')->sum('amount');
        $pendingPaymentsCount = DistributorPayment::where('status', 'pending')->count();
        $totalRejected = DistributorPayment::where('status', 'rejected')->sum('amount');

        return view('admin_panel.distributors.payments', compact(
            'payments',
            'distributors',
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
        $payment = DistributorPayment::with('distributor')->findOrFail($id);

        if ($payment->status === 'approved') {
            return back()->with('info', "Payment #{$payment->payment_no} is already approved.");
        }

        DB::beginTransaction();
        try {
            $distributor = $payment->distributor;
            $newBalance = (float) $distributor->current_balance - (float) $payment->amount;

            // 1. Post to Distributor Ledger (Credit reduces amount owed to company)
            DistributorLedger::create([
                'distributor_id' => $distributor->id,
                'date'           => $payment->payment_date ? $payment->payment_date->format('Y-m-d') : now()->format('Y-m-d'),
                'type'           => 'payment_received',
                'reference_no'   => $payment->payment_no,
                'description'    => "Payment approved via " . ucwords(str_replace('_', ' ', $payment->payment_method)) . 
                                    ($payment->bank_name ? " ({$payment->bank_name})" : '') .
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
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'notes'       => $payment->notes . ($request->approval_notes ? " | Approval: {$request->approval_notes}" : ''),
            ]);

            DB::commit();
            return back()->with('success', "Payment #{$payment->payment_no} of Rs. " . number_format($payment->amount, 2) . " from '{$distributor->name}' approved and credited to ledger successfully!");
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
            'payment_method'        => 'required|string|max:50',
            'bank_name'             => 'nullable|string|max:100',
            'account_number'        => 'nullable|string|max:100',
            'transaction_reference' => 'nullable|string|max:100',
            'slip_image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'notes'                 => 'nullable|string|max:1000',
        ]);

        $distributor = Distributor::findOrFail($request->distributor_id);

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

            // 1. Create Payment (auto-approved)
            $payment = DistributorPayment::create([
                'payment_no'            => $paymentNo,
                'distributor_id'        => $distributor->id,
                'amount'                => $amount,
                'payment_date'          => $request->payment_date,
                'payment_method'        => $request->payment_method,
                'bank_name'             => $request->bank_name,
                'account_number'        => $request->account_number,
                'transaction_reference' => $request->transaction_reference,
                'slip_image'            => $slipFileName,
                'notes'                 => $request->notes,
                'status'                => 'approved',
                'approved_by'           => Auth::id(),
                'approved_at'           => now(),
                'created_by'            => Auth::id(),
            ]);

            // 2. Post to Ledger
            DistributorLedger::create([
                'distributor_id' => $distributor->id,
                'date'           => $request->payment_date,
                'type'           => 'payment_received',
                'reference_no'   => $paymentNo,
                'description'    => "Direct payment received via " . ucwords(str_replace('_', ' ', $request->payment_method)) .
                                    ($request->bank_name ? " ({$request->bank_name})" : '') .
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
            return back()->with('success', "Payment #{$paymentNo} of Rs. " . number_format($amount, 2) . " recorded and credited to {$distributor->name}'s ledger successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }
}

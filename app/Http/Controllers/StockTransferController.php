<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockTransfer;
use App\Models\WarehouseStock;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use function active_branch_id;
use function is_all_branches;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'toBranch']);

        if (!is_all_branches() && \Illuminate\Support\Facades\Schema::hasColumn('stock_transfers', 'branch_id')) {
            $query->where(function ($q) {
                $q->where('branch_id', active_branch_id())
                  ->orWhere('to_branch_id', active_branch_id());
            });
        }

        $transfers = $query->latest()
            ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                $q->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            })
            ->get()
            ->map(function ($transfer) {
                $productIds = is_array($transfer->product_id) ? $transfer->product_id : (json_decode($transfer->product_id, true) ?: []);
                $variantIds = is_array($transfer->variant_id) ? $transfer->variant_id : (json_decode($transfer->variant_id, true) ?: []);
                $quantities = is_array($transfer->quantity) ? $transfer->quantity : (json_decode($transfer->quantity, true) ?: []);

                $items = [];
                foreach ($productIds as $i => $pid) {
                    $vid = $variantIds[$i] ?? null;
                    $qty = (float) ($quantities[$i] ?? 0);
                    
                    $product = Product::with('unit')->find($pid);
                    $variant = $vid ? ProductVariant::find($vid) : null;
                    
                    if ($product) {
                        $unitStr = $product->unit->name ?? ($product->unit_type ? strtoupper($product->unit_type) : 'Pc');
                        $items[] = [
                            'name' => $product->item_name . ($variant ? ' (' . ($variant->size_label ?: $variant->variant_name) . ')' : ''),
                            'qty'  => $qty,
                            'unit' => $unitStr,
                        ];
                    }
                }

                $transfer->items = collect($items);
                return $transfer;
            });

        return view(
            'admin_panel.warehouses.stock_transfers.index',
            compact('transfers')
        );
    }

    public function create()
    {
        $branchId = active_branch_id();

        $warehouses = Warehouse::all();
        $branches = \App\Models\Branch::all();
        $products = Product::with('variants')->get();

        return view('admin_panel.warehouses.stock_transfers.create', compact('warehouses', 'branches', 'products', 'branchId'));
    }

    public function store(Request $request)
    {
        try {
            $productIds = $request->product_id;
            $variantIds = $request->variant_id;
            $quantities = $request->quantity;

            $request->validate([
                'transfer_to'  => 'required|in:shop,warehouse,branch',
                'to_branch_id' => 'required_if:transfer_to,branch',
                'product_id'   => 'required|array|min:1',
                'product_id.*' => 'required|integer|exists:products,id',
                'quantity'     => 'required|array',
                'quantity.*'   => 'required|numeric|min:0.01',
            ]);

            $fromWarehouse = $request->from_warehouse_id;
            $transferTo    = $request->transfer_to;
            $toWarehouse   = $request->to_warehouse_id;
            $toBranchId    = $request->to_branch_id;
            $remarks       = $request->remarks;

            $status = ($transferTo === 'branch' && $toBranchId) ? 'pending' : 'completed';

            DB::beginTransaction();

            foreach ($productIds as $index => $productId) {
                if (empty($productId) || empty($quantities[$index])) {
                    continue;
                }

                $qty = (float) $quantities[$index];
                $variantId = $variantIds[$index] ?? null;
                if ($variantId === 'null' || empty($variantId)) $variantId = null;

                if ($qty <= 0) {
                    continue;
                }

                $prodObj = Product::find($productId);
                $isKgItem = $prodObj && ($prodObj->unit_type === 'kg');
                $stockQtyToApply = $isKgItem ? ($qty * 1000) : $qty;

                // ---------- SENDER DEDUCTION ----------
                if ($fromWarehouse !== 'Shop') {
                    $sourceStock = WarehouseStock::firstOrCreate(
                        [
                            'warehouse_id' => $fromWarehouse,
                            'product_id'   => $productId,
                            'variant_id'   => $variantId,
                            'branch_id'    => active_branch_id()
                        ],
                        ['quantity' => 0, 'price' => 0]
                    );

                    $sourceStock->quantity -= $stockQtyToApply;
                    $sourceStock->save();
                } else {
                    $sourceStock = Stock::firstOrCreate(
                        [
                            'product_id' => $productId,
                            'variant_id' => $variantId,
                            'branch_id'  => active_branch_id()
                        ],
                        ['qty' => 0]
                    );

                    $sourceStock->qty -= $stockQtyToApply;
                    $sourceStock->save();
                }

                // ---------- DESTINATION (Immediate credit for Warehouse/Shop, Pending for Branch) ----------
                if ($transferTo === 'warehouse' && $toWarehouse) {
                    $destStock = WarehouseStock::firstOrCreate(
                        [
                            'warehouse_id' => $toWarehouse,
                            'product_id'   => $productId,
                            'variant_id'   => $variantId,
                            'branch_id'    => active_branch_id()
                        ],
                        ['quantity' => 0, 'price' => $sourceStock->price ?? 0]
                    );

                    $destStock->quantity += $stockQtyToApply;
                    $destStock->save();
                } elseif ($transferTo === 'shop') {
                    $shopStock = Stock::firstOrCreate(
                        [
                            'product_id' => $productId,
                            'variant_id' => $variantId,
                            'branch_id'  => active_branch_id()
                        ],
                        ['qty' => 0]
                    );

                    $shopStock->qty += $stockQtyToApply;
                    $shopStock->save();
                }
                // Note: Branch transfers remain PENDING until accepted by receiving branch.
            }

            $transfer = StockTransfer::create([
                'branch_id'         => active_branch_id(),
                'from_warehouse_id' => $fromWarehouse === 'Shop' ? null : $fromWarehouse,
                'transfer_to'       => $transferTo,
                'to_warehouse_id'   => $transferTo === 'warehouse' ? $toWarehouse : null,
                'to_branch_id'      => $transferTo === 'branch' ? $toBranchId : null,
                'shop_name'         => $transferTo === 'shop' ? ($request->shop_name ?? 'Shop') : null,
                'product_id'        => json_encode(array_values(array_filter($productIds ?? []))),
                'variant_id'        => json_encode(array_values($variantIds ?? [])),
                'quantity'          => json_encode(array_values(array_filter($quantities ?? []))),
                'remarks'           => $remarks,
                'status'            => $status,
                'admin_notified'    => 0,
                'created_at'        => $request->transfer_date ? \Carbon\Carbon::parse($request->transfer_date . ' ' . now()->format('H:i:s')) : now(),
                'updated_at'        => now(),
            ]);

            DB::commit();

            $msg = ($status === 'pending')
                ? 'Stock transfer dispatched! Dispatched items will be added to target branch inventory upon acceptance.'
                : 'Stock transferred successfully.';

            return redirect()
                ->route('stock_transfers.receipt', $transfer->id)
                ->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function accept($id)
    {
        $transfer = StockTransfer::findOrFail($id);

        if ($transfer->status !== 'pending') {
            return redirect()->back()->with('error', 'This transfer has already been processed.');
        }

        if (!is_all_branches() && $transfer->to_branch_id && $transfer->to_branch_id != active_branch_id()) {
            return redirect()->back()->with('error', 'Unauthorized! Only the receiving branch can accept this transfer.');
        }

        $productIds = is_array($transfer->product_id) ? $transfer->product_id : (json_decode($transfer->product_id, true) ?: []);
        $variantIds = is_array($transfer->variant_id) ? $transfer->variant_id : (json_decode($transfer->variant_id, true) ?: []);
        $quantities = is_array($transfer->quantity) ? $transfer->quantity : (json_decode($transfer->quantity, true) ?: []);

        DB::beginTransaction();
        try {
            $destBranchId = $transfer->to_branch_id ?? active_branch_id();

            foreach ($productIds as $index => $productId) {
                if (empty($productId) || empty($quantities[$index])) {
                    continue;
                }

                $qty = (float) $quantities[$index];
                $variantId = $variantIds[$index] ?? null;
                if ($variantId === 'null' || empty($variantId)) $variantId = null;

                $prodObj = Product::find($productId);
                $isKgItem = $prodObj && ($prodObj->unit_type === 'kg');
                $stockQtyToApply = $isKgItem ? ($qty * 1000) : $qty;

                $branchStock = Stock::firstOrCreate(
                    [
                        'product_id' => $productId,
                        'variant_id' => $variantId,
                        'branch_id'  => $destBranchId
                    ],
                    ['qty' => 0]
                );

                $branchStock->qty += $stockQtyToApply;
                $branchStock->save();
            }

            $transfer->status = 'completed';
            $transfer->save();

            DB::commit();
            return redirect()->route('stock_transfers.index')->with('success', 'Stock transfer accepted! Items have been added to your branch stock.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to accept transfer: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        $transfer = StockTransfer::findOrFail($id);

        if ($transfer->status !== 'pending') {
            return redirect()->back()->with('error', 'This transfer has already been processed.');
        }

        if (!is_all_branches() && $transfer->to_branch_id && $transfer->to_branch_id != active_branch_id() && $transfer->branch_id != active_branch_id()) {
            return redirect()->back()->with('error', 'Unauthorized!');
        }

        $productIds = is_array($transfer->product_id) ? $transfer->product_id : (json_decode($transfer->product_id, true) ?: []);
        $variantIds = is_array($transfer->variant_id) ? $transfer->variant_id : (json_decode($transfer->variant_id, true) ?: []);
        $quantities = is_array($transfer->quantity) ? $transfer->quantity : (json_decode($transfer->quantity, true) ?: []);

        DB::beginTransaction();
        try {
            // Revert source stock (refund back to sender)
            foreach ($productIds as $index => $productId) {
                if (empty($productId) || empty($quantities[$index])) {
                    continue;
                }

                $qty = (float) $quantities[$index];
                $variantId = $variantIds[$index] ?? null;
                if ($variantId === 'null' || empty($variantId)) $variantId = null;

                $prodObj = Product::find($productId);
                $isKgItem = $prodObj && ($prodObj->unit_type === 'kg');
                $stockQtyToApply = $isKgItem ? ($qty * 1000) : $qty;

                if ($transfer->from_warehouse_id) {
                    $sourceStock = WarehouseStock::firstOrCreate(
                        [
                            'warehouse_id' => $transfer->from_warehouse_id,
                            'product_id'   => $productId,
                            'variant_id'   => $variantId,
                            'branch_id'    => $transfer->branch_id
                        ],
                        ['quantity' => 0]
                    );
                    $sourceStock->quantity += $stockQtyToApply;
                    $sourceStock->save();
                } else {
                    $sourceStock = Stock::firstOrCreate(
                        [
                            'product_id' => $productId,
                            'variant_id' => $variantId,
                            'branch_id'  => $transfer->branch_id
                        ],
                        ['qty' => 0]
                    );
                    $sourceStock->qty += $stockQtyToApply;
                    $sourceStock->save();
                }
            }

            $transfer->status = 'rejected';
            $transfer->save();

            DB::commit();
            return redirect()->route('stock_transfers.index')->with('success', 'Stock transfer rejected and returned to sender.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to reject transfer: ' . $e->getMessage());
        }
    }

    public function checkNewTransfers()
    {
        if (!Auth::check()) {
            return response()->json([]);
        }

        $activeBranch = active_branch_id();

        $query = StockTransfer::with(['fromWarehouse', 'fromBranch', 'toWarehouse', 'toBranch'])
            ->where('admin_notified', 0)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc');

        if (!is_all_branches()) {
            $query->where('to_branch_id', $activeBranch);
        }

        $newTransfers = $query->get();
        return response()->json($newTransfers);
    }

    public function markTransfersNotified(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error'], 401);
        }

        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            StockTransfer::whereIn('id', $ids)->update(['admin_notified' => 1]);
        }
        return response()->json(['status' => 'success']);
    }

    public function getStockQuantity(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');
        $productId   = $request->input('product_id');
        $variantId   = $request->input('variant_id');

        if ($variantId === 'null' || empty($variantId)) {
            $variantId = null;
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['quantity' => 0, 'unit' => '']);
        }

        $unitName = 'Pc';
        if ($product->unit_type === 'kg') {
            $unitName = 'KG';
        } elseif ($product->unit) {
            $unitName = $product->unit->name ?? 'Pc';
        }

        $totalQty = 0;

        if ($warehouseId === 'Shop' || empty($warehouseId)) {
            $activeBranch = active_branch_id();
            $query = Stock::where('product_id', $productId)
                ->where('branch_id', $activeBranch)
                ->whereNull('warehouse_id');

            if (!empty($variantId)) {
                $variantStock = (clone $query)->where('variant_id', $variantId)->sum('qty');
                if ($variantStock > 0) {
                    $totalQty = $variantStock;
                } else {
                    $totalQty = $query->sum('qty');
                }
            } else {
                $totalQty = $query->sum('qty');
            }
        } else {
            $query = WarehouseStock::where('product_id', $productId)
                ->where('warehouse_id', $warehouseId);

            if (!empty($variantId)) {
                $variantStock = (clone $query)->where('variant_id', $variantId)->sum('quantity');
                if ($variantStock > 0) {
                    $totalQty = $variantStock;
                } else {
                    $totalQty = $query->sum('quantity');
                }
            } else {
                $totalQty = $query->sum('quantity');
            }
        }

        if ($product->unit_type === 'kg') {
            $displayQty = round($totalQty / 1000, 3);
        } else {
            $displayQty = round($totalQty, 2);
        }

        return response()->json([
            'quantity' => $displayQty,
            'unit'     => $unitName
        ]);
    }

    public function receipt($id = null)
    {
        if (!$id) {
            $transfer = StockTransfer::with(['fromWarehouse', 'fromBranch', 'toWarehouse', 'toBranch'])->latest()->firstOrFail();
        } else {
            $transfer = StockTransfer::with(['fromWarehouse', 'fromBranch', 'toWarehouse', 'toBranch'])->findOrFail($id);
        }

        $productIds = is_array($transfer->product_id) ? $transfer->product_id : (json_decode($transfer->product_id, true) ?: []);
        $variantIds = is_array($transfer->variant_id) ? $transfer->variant_id : (json_decode($transfer->variant_id, true) ?: []);
        $quantities = is_array($transfer->quantity) ? $transfer->quantity : (json_decode($transfer->quantity, true) ?: []);

        $products = collect();

        foreach ($productIds as $index => $productId) {
            if (empty($productId)) continue;

            $product = Product::find($productId);
            if (!$product) continue;

            $variantId = $variantIds[$index] ?? null;
            if ($variantId === 'null' || empty($variantId)) $variantId = null;

            $variantName = '';
            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                if ($variant) {
                    $variantName = $variant->name ?? '';
                }
            }

            $unitName = 'Pc';
            if ($product->unit_type === 'kg') {
                $unitName = 'KG';
            } elseif ($product->unit) {
                $unitName = $product->unit->name ?? 'Pc';
            }

            $product->transfer_qty = (float) ($quantities[$index] ?? 0);
            $product->unit = $unitName;
            $product->variant_name = $variantName;

            $products->push($product);
        }

        $unitTotals = [];
        foreach ($products as $p) {
            $u = $p->unit ?: 'Pc';
            if (!isset($unitTotals[$u])) {
                $unitTotals[$u] = 0;
            }
            $unitTotals[$u] += (float) $p->transfer_qty;
        }

        return view('admin_panel.warehouses.stock_transfers.receipt', compact('transfer', 'products', 'unitTotals'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\RawMaterialPurchase;
use App\Models\RawMaterialPurchaseItem;
use App\Models\RawMaterialStock;
use App\Models\Vendor;
use App\Models\VendorLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function active_branch_id;
use function is_all_branches;

class RawMaterialController extends Controller
{
    // ==================== RAW MATERIALS CRUD ====================
    public function index(Request $request)
    {
        $branchId = is_all_branches() ? null : active_branch_id();

        // Date and Time filters (Default: Today from 00:00 to 23:59)
        $startDate = $request->input('start_date', date('Y-m-d'));
        $startTime = $request->input('start_time', '00:00');
        $endDate   = $request->input('end_date', date('Y-m-d'));
        $endTime   = $request->input('end_time', '23:59');

        $sTime = strlen($startTime) == 5 ? $startTime . ':00' : $startTime;
        $eTime = strlen($endTime) == 5 ? $endTime . ':59' : $endTime;
        $startDT = $startDate . ' ' . $sTime;
        $endDT   = $endDate . ' ' . $eTime;

        $materialsQuery = RawMaterial::with(['stocks' => function($q) use ($branchId) {
            if ($branchId) $q->where('branch_id', $branchId);
        }, 'stocks.warehouse']);

        $materials = $materialsQuery->orderBy('name')->get();
        $materialIds = $materials->pluck('id')->toArray();

        // 1. Current Live Branch Stock from DB (consumption units)
        $liveStockQuery = RawMaterialStock::whereIn('raw_material_id', $materialIds)
            ->where(function($q) {
                $q->whereNull('warehouse_id')->orWhere('warehouse_id', 0);
            });
        if ($branchId) {
            $liveStockQuery->where('branch_id', $branchId);
        }
        $liveStocks = $liveStockQuery->select('raw_material_id', DB::raw('SUM(qty) as total_qty'))
            ->groupBy('raw_material_id')
            ->pluck('total_qty', 'raw_material_id')
            ->toArray();

        // 2. Purchases AFTER endDT (for backward balance calculation)
        $purchAfterQuery = DB::table('raw_material_purchase_items as rmpi')
            ->join('raw_material_purchases as rmp', 'rmp.id', '=', 'rmpi.purchase_id')
            ->whereIn('rmpi.raw_material_id', $materialIds)
            ->where(function($q) {
                $q->whereNull('rmp.warehouse_id')->orWhere('rmp.warehouse_id', 0);
            })
            ->where(DB::raw("COALESCE(rmp.created_at, CONCAT(rmp.date, ' 00:00:00'))"), '>', $endDT);
        if ($branchId) {
            $purchAfterQuery->where('rmp.branch_id', $branchId);
        }
        $purchAfter = $purchAfterQuery->select('rmpi.raw_material_id', DB::raw('SUM(rmpi.qty) as total_qty'))
            ->groupBy('rmpi.raw_material_id')
            ->pluck('total_qty', 'rmpi.raw_material_id')
            ->toArray();

        // 3. Usages AFTER endDT (for backward balance calculation)
        $usageAfterQuery = DB::table('production_raw_material_usage as prmu')
            ->join('production_entries as pe', 'pe.id', '=', 'prmu.production_entry_id')
            ->whereIn('prmu.raw_material_id', $materialIds)
            ->where(function($q) {
                $q->whereNull('pe.warehouse_id')->orWhere('pe.warehouse_id', 0);
            })
            ->where(DB::raw("COALESCE(prmu.created_at, CONCAT(pe.production_date, ' 00:00:00'))"), '>', $endDT);
        if ($branchId) {
            $usageAfterQuery->where(function($q) use ($branchId) {
                $q->where('prmu.branch_id', $branchId)->orWhere('pe.branch_id', $branchId);
            });
        }
        $usageAfter = $usageAfterQuery->select('prmu.raw_material_id', DB::raw('SUM(prmu.qty_used) as total_qty'))
            ->groupBy('prmu.raw_material_id')
            ->pluck('total_qty', 'prmu.raw_material_id')
            ->toArray();

        // 4. Purchases IN PERIOD [startDT, endDT]
        $purchPeriodQuery = DB::table('raw_material_purchase_items as rmpi')
            ->join('raw_material_purchases as rmp', 'rmp.id', '=', 'rmpi.purchase_id')
            ->whereIn('rmpi.raw_material_id', $materialIds)
            ->where(function($q) {
                $q->whereNull('rmp.warehouse_id')->orWhere('rmp.warehouse_id', 0);
            })
            ->whereBetween(DB::raw("COALESCE(rmp.created_at, CONCAT(rmp.date, ' 00:00:00'))"), [$startDT, $endDT]);
        if ($branchId) {
            $purchPeriodQuery->where('rmp.branch_id', $branchId);
        }
        $purchPeriod = $purchPeriodQuery->select('rmpi.raw_material_id', DB::raw('SUM(rmpi.qty) as total_qty'))
            ->groupBy('rmpi.raw_material_id')
            ->pluck('total_qty', 'rmpi.raw_material_id')
            ->toArray();

        // 5. Usages IN PERIOD [startDT, endDT]
        $usagePeriodQuery = DB::table('production_raw_material_usage as prmu')
            ->join('production_entries as pe', 'pe.id', '=', 'prmu.production_entry_id')
            ->whereIn('prmu.raw_material_id', $materialIds)
            ->where(function($q) {
                $q->whereNull('pe.warehouse_id')->orWhere('pe.warehouse_id', 0);
            })
            ->whereBetween(DB::raw("COALESCE(prmu.created_at, CONCAT(pe.production_date, ' 00:00:00'))"), [$startDT, $endDT]);
        if ($branchId) {
            $usagePeriodQuery->where(function($q) use ($branchId) {
                $q->where('prmu.branch_id', $branchId)->orWhere('pe.branch_id', $branchId);
            });
        }
        $usagePeriod = $usagePeriodQuery->select('prmu.raw_material_id', DB::raw('SUM(prmu.qty_used) as total_qty'))
            ->groupBy('prmu.raw_material_id')
            ->pluck('total_qty', 'prmu.raw_material_id')
            ->toArray();

        // Attach calculated stock metrics to each material
        foreach ($materials as $m) {
            $factor = (float)($m->conversion_factor ?? 1);
            if ($factor <= 0) $factor = 1;

            $sNow = (float)($liveStocks[$m->id] ?? 0); // live stock in consumption unit
            $pAfterPur = (float)($purchAfter[$m->id] ?? 0); // purchase unit
            $pAfter = $pAfterPur * $factor; // consumption unit
            $uAfter = (float)($usageAfter[$m->id] ?? 0); // consumption unit

            $sEnd = $sNow - $pAfter + $uAfter; // closing / current stock of the period

            $pPeriodPur = (float)($purchPeriod[$m->id] ?? 0); // purchase unit
            $pPeriod = $pPeriodPur * $factor; // consumption unit
            $uPeriod = (float)($usagePeriod[$m->id] ?? 0); // consumption unit

            $sStart = $sEnd - $pPeriod + $uPeriod; // opening stock of the period

            $m->calc_opening_stock_consumption = $sStart;
            $m->calc_opening_stock_purchase = $sStart / $factor;

            $m->calc_purchase_qty_consumption = $pPeriod;
            $m->calc_purchase_qty_purchase = $pPeriodPur;

            $m->calc_used_qty_consumption = $uPeriod;
            $m->calc_used_qty_purchase = $uPeriod / $factor;

            $m->calc_current_stock_consumption = $sEnd;
            $m->calc_current_stock_purchase = $sEnd / $factor;

            $alertInConsumption = (float)($m->alert_qty ?? 0) * $factor;
            $m->calc_status = $sEnd > $alertInConsumption ? 'In Stock' : ($sEnd > 0 ? 'Low Stock' : 'Out of Stock');
            $m->has_usage = ($uPeriod > 0);
        }

        $purchasesQuery = RawMaterialPurchase::with(['items.rawMaterial', 'creator', 'warehouse', 'branch']);
        if (!is_all_branches()) {
            $purchasesQuery->where('branch_id', active_branch_id());
        }
        $purchases = $purchasesQuery->orderBy('date', 'desc')->orderBy('id', 'desc')->take(50)->get();

        $vendors = \App\Models\Vendor::orderBy('name')->get(['id', 'name']);
        $warehouses = \App\Models\Warehouse::orderBy('warehouse_name')->get(['id', 'warehouse_name']);

        $productsWithBom = \App\Models\Product::has('bom')->with(['bom.rawMaterial'])->orderBy('item_name')->get();

        // Warehouse stocks for raw materials (strictly warehouse inventory)
        $warehouseStocks = RawMaterialStock::with(['rawMaterial', 'warehouse'])
            ->whereNotNull('warehouse_id')
            ->where('warehouse_id', '>', 0)
            ->orderBy('warehouse_id')
            ->get();

        return view('admin_panel.raw_material.index', compact(
            'materials', 'purchases', 'vendors', 'warehouses', 'productsWithBom', 'warehouseStocks',
            'startDate', 'startTime', 'endDate', 'endTime'
        ));
    }

    public function storeRawMaterial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'urdu_name' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
            'alert_qty' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        if ($request->edit_id) {
            $material = RawMaterial::find($request->edit_id);
            if (!$material) {
                return response()->json(['error' => 'Raw Material not found!']);
            }
            $msg = ['success' => 'Raw Material Updated Successfully', 'reload' => true];
        } else {
            $material = new RawMaterial();
            $msg = ['success' => 'Raw Material Created Successfully', 'reload' => true];
        }

        $material->name = $request->name;
        $material->urdu_name = $request->urdu_name;
        $material->unit = strtolower(trim($request->unit));
        $material->consumption_unit = strtolower(trim($request->consumption_unit ?? $request->unit));
        $material->conversion_factor = (float)($request->conversion_factor ?? 1) > 0 ? (float)$request->conversion_factor : 1;
        $material->alert_qty = (float)($request->alert_qty ?? 0);
        $material->notes = $request->notes;
        $material->save();

        // Save initial / updated purchase price
        if ($request->has('initial_price')) {
            $price = (float)$request->initial_price;
            $latestItem = RawMaterialPurchaseItem::where('raw_material_id', $material->id)->latest('id')->first();
            if ($latestItem) {
                $latestItem->price_per_unit = $price;
                $latestItem->total = $latestItem->qty * $price;
                $latestItem->save();
            } elseif ($price > 0) {
                $purchase = RawMaterialPurchase::create([
                    'date' => date('Y-m-d'),
                    'invoice_no' => 'INIT-RM-' . $material->id,
                    'vendor_name' => 'Initial Cost Setup',
                    'total_cost' => 0,
                    'notes' => 'Auto-generated for initial raw material cost setup',
                    'created_by' => Auth::id(),
                    'branch_id' => is_all_branches() ? null : active_branch_id(),
                ]);
                RawMaterialPurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'raw_material_id' => $material->id,
                    'qty' => 0,
                    'price_per_unit' => $price,
                    'total' => 0,
                    'branch_id' => is_all_branches() ? null : active_branch_id(),
                ]);
            }
        }

        // Ensure stock record exists with opening stock (converted to consumption units)
        $branchId = is_all_branches() ? null : active_branch_id();
        $openingQty = (float)($request->opening_stock ?? 0);
        $factor = (float)($material->conversion_factor ?? 1);
        $openingQtyInConsumptionUnit = $openingQty * ($factor > 0 ? $factor : 1);
        $stockRecord = RawMaterialStock::firstOrCreate(
            [
                'raw_material_id' => $material->id,
                'branch_id' => $branchId,
                'warehouse_id' => null,
            ],
            ['qty' => $openingQtyInConsumptionUnit]
        );
        // If opening stock provided, update it
        if ($request->has('opening_stock') && $request->opening_stock !== null && $request->opening_stock !== '') {
            $stockRecord->qty = $openingQtyInConsumptionUnit;
            $stockRecord->branch_id = $branchId;
            $stockRecord->save();
        }

        return response()->json($msg);
    }

    public function deleteRawMaterial($id)
    {
        try {
            $material = RawMaterial::find($id);
            if ($material) {
                // Delete associated stocks
                RawMaterialStock::where('raw_material_id', $id)->delete();
                // Delete associated purchase items
                RawMaterialPurchaseItem::where('raw_material_id', $id)->delete();
                // Delete product BOM entries using this material
                DB::table('product_raw_material_bom')->where('raw_material_id', $id)->delete();
                
                $material->delete();
                return response()->json(['success' => 'Raw Material Deleted Successfully', 'reload' => route('raw-materials.index')]);
            }
            return response()->json(['error' => 'Raw Material Not Found']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Delete failed: ' . $e->getMessage()]);
        }
    }

    // ==================== RAW MATERIAL PURCHASES ====================
    public function storePurchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'vendor_name' => 'required|string|max:255',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'raw_material_id' => 'required|array',
            'raw_material_id.*' => 'required|exists:raw_materials,id',
            'qty' => 'required|array',
            'qty.*' => 'required|numeric|min:0.01',
            'price_per_unit' => 'required|array',
            'price_per_unit.*' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {
            $branchId = is_all_branches() ? null : active_branch_id();
            DB::beginTransaction();

            $totalCost = 0;
            $itemsData = [];

            foreach ($request->raw_material_id as $i => $rmId) {
                $qty = (float)($request->qty[$i] ?? 0);
                $price = (float)($request->price_per_unit[$i] ?? 0);
                $total = $qty * $price;
                $totalCost += $total;

                $itemsData[] = [
                    'raw_material_id' => $rmId,
                    'qty' => $qty,
                    'price_per_unit' => $price,
                    'total' => $total,
                    'branch_id' => $branchId,
                ];
            }

            // Find vendor
            $vendor = null;
            if ($request->filled('vendor_id')) {
                $vendor = Vendor::find($request->vendor_id);
            } elseif ($request->filled('vendor_name')) {
                $vendor = Vendor::where('name', $request->vendor_name)->first();
            }

            if ($request->filled('edit_id')) {
                $purchase = RawMaterialPurchase::with('items')->findOrFail($request->edit_id);

                if (!is_all_branches() && $purchase->branch_id && $purchase->branch_id != active_branch_id()) {
                    return response()->json(['error' => 'Unauthorized!']);
                }

                // 1. Reverse old stock
                foreach ($purchase->items as $item) {
                    $rawMat = RawMaterial::find($item->raw_material_id);
                    $factor = ($rawMat && (float)$rawMat->conversion_factor > 0) ? (float)$rawMat->conversion_factor : 1;
                    $deductQty = $item->qty * $factor;

                    if ($purchase->warehouse_id) {
                        $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
                            ->where('warehouse_id', $purchase->warehouse_id)
                            ->first();
                    } else {
                        $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
                            ->whereNull('warehouse_id');
                        if (!is_all_branches()) {
                            $stock->where('branch_id', active_branch_id());
                        }
                        $stock = $stock->first();
                    }
                    if ($stock) {
                        $stock->qty = max(0, $stock->qty - $deductQty);
                        $stock->save();
                    }
                }

                // 2. Reverse old vendor ledger
                $oldVendorId = $purchase->vendor_id;
                if (!$oldVendorId && $purchase->vendor_name) {
                    $v = Vendor::where('name', $purchase->vendor_name)->first();
                    $oldVendorId = $v ? $v->id : null;
                }
                if ($oldVendorId) {
                    $ledger = VendorLedger::where('vendor_id', $oldVendorId)->first();
                    if ($ledger) {
                        $ledger->previous_balance = $ledger->closing_balance;
                        $ledger->closing_balance -= (float)$purchase->total_cost;
                        $ledger->save();
                    }
                }

                // 3. Remove old items
                $purchase->items()->delete();

                // 4. Update purchase
                $purchase->date = $request->date;
                $purchase->vendor_id = $vendor ? $vendor->id : null;
                $purchase->vendor_name = $vendor ? $vendor->name : $request->vendor_name;
                $purchase->warehouse_id = $request->warehouse_id ?: null;
                $purchase->total_cost = $totalCost;
                $purchase->notes = $request->notes;
                $purchase->save();

                $successMsg = 'Raw Material Purchase Updated Successfully';
            } else {
                $purchase = RawMaterialPurchase::create([
                    'date' => $request->date,
                    'invoice_no' => 'RMP-' . date('Ymd-His'),
                    'vendor_id' => $vendor ? $vendor->id : null,
                    'vendor_name' => $vendor ? $vendor->name : $request->vendor_name,
                    'warehouse_id' => $request->warehouse_id ?: null,
                    'total_cost' => $totalCost,
                    'notes' => $request->notes,
                    'created_by' => Auth::id(),
                    'branch_id' => $branchId,
                ]);
                $successMsg = 'Raw Material Purchase Saved Successfully';
            }

            // 🔹 Update Vendor Ledger closing balance
            if ($vendor) {
                $ledger = VendorLedger::firstOrNew(['vendor_id' => $vendor->id]);
                if (!$ledger->exists) {
                    $ledger->admin_or_user_id = Auth::id();
                    $ledger->opening_balance = $vendor->opening_balance ?? 0;
                    $ledger->closing_balance = $vendor->opening_balance ?? 0;
                    $ledger->previous_balance = 0;
                }
                $ledger->previous_balance = $ledger->closing_balance;
                $ledger->closing_balance += $totalCost;
                $ledger->save();
            }

            foreach ($itemsData as $item) {
                $rmId = $item['raw_material_id'];
                $item['purchase_id'] = $purchase->id;
                RawMaterialPurchaseItem::create($item);

                // Update stock in consumption/recipe units
                $rawMat = RawMaterial::find($rmId);
                $factor = ($rawMat && (float)$rawMat->conversion_factor > 0) ? (float)$rawMat->conversion_factor : 1;
                $addQty = $item['qty'] * $factor;
                $warehouseId = $request->warehouse_id ?: null;

                if ($warehouseId) {
                    $stock = RawMaterialStock::firstOrCreate(
                        [
                            'raw_material_id' => $rmId,
                            'warehouse_id' => $warehouseId,
                        ],
                        [
                            'branch_id' => $branchId,
                            'qty' => 0
                        ]
                    );
                } else {
                    $stock = RawMaterialStock::firstOrCreate(
                        [
                            'raw_material_id' => $rmId,
                            'warehouse_id' => null,
                            'branch_id' => $branchId,
                        ],
                        ['qty' => 0]
                    );
                }
                $stock->qty += $addQty;
                $stock->save();
            }

            DB::commit();
            return response()->json(['success' => $successMsg, 'reload' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function deletePurchase($id)
    {
        try {
            DB::beginTransaction();
            $purchase = RawMaterialPurchase::with('items')->find($id);
            if (!$purchase) {
                return response()->json(['error' => 'Purchase not found!']);
            }

            if (!is_all_branches() && $purchase->branch_id && $purchase->branch_id != active_branch_id()) {
                return response()->json(['error' => 'Unauthorized!']);
            }

            // Reverse stock
            foreach ($purchase->items as $item) {
                $rawMat = RawMaterial::find($item->raw_material_id);
                $factor = ($rawMat && (float)$rawMat->conversion_factor > 0) ? (float)$rawMat->conversion_factor : 1;
                $deductQty = $item->qty * $factor;

                if ($purchase->warehouse_id) {
                    $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
                        ->where('warehouse_id', $purchase->warehouse_id)
                        ->first();
                } else {
                    $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
                        ->whereNull('warehouse_id');
                    if (!is_all_branches()) {
                        $stock->where('branch_id', active_branch_id());
                    }
                    $stock = $stock->first();
                }
                if ($stock) {
                    $stock->qty = max(0, $stock->qty - $deductQty);
                    $stock->save();
                }
            }

            // Reverse vendor ledger balance
            $vendorId = $purchase->vendor_id;
            if (!$vendorId && $purchase->vendor_name) {
                $v = Vendor::where('name', $purchase->vendor_name)->first();
                $vendorId = $v ? $v->id : null;
            }
            if ($vendorId) {
                $ledger = VendorLedger::where('vendor_id', $vendorId)->first();
                if ($ledger) {
                    $ledger->previous_balance = $ledger->closing_balance;
                    $ledger->closing_balance -= (float)$purchase->total_cost;
                    $ledger->save();
                }
            }

            $purchase->delete();
            DB::commit();
            return response()->json(['success' => 'Purchase Deleted Successfully & Stock Reversed', 'reload' => route('raw-materials.index')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function printPurchase($id)
    {
        $purchase = RawMaterialPurchase::with(['items.rawMaterial', 'creator', 'warehouse', 'branch'])->findOrFail($id);
        return view('admin_panel.raw_material.purchase_print', compact('purchase'));
    }

    // ==================== STOCK AJAX ====================
    // ==================== USAGE DETAIL AJAX ====================
    public function getUsageDetail($id, Request $request)
    {
        $material = RawMaterial::findOrFail($id);
        $branchId = is_all_branches() ? null : active_branch_id();

        $startDate = $request->input('start_date', date('Y-m-d'));
        $startTime = $request->input('start_time', '00:00');
        $endDate   = $request->input('end_date', date('Y-m-d'));
        $endTime   = $request->input('end_time', '23:59');

        $sTime = strlen($startTime) == 5 ? $startTime . ':00' : $startTime;
        $eTime = strlen($endTime) == 5 ? $endTime . ':59' : $endTime;
        $startDT = $startDate . ' ' . $sTime;
        $endDT   = $endDate . ' ' . $eTime;

        $factor = (float)($material->conversion_factor ?? 1);
        if ($factor <= 0) $factor = 1;

        // Fetch usage records for this raw material
        $usageQuery = DB::table('production_raw_material_usage as prmu')
            ->join('production_entries as pe', 'pe.id', '=', 'prmu.production_entry_id')
            ->leftJoin('users as u', 'u.id', '=', 'pe.created_by')
            ->where('prmu.raw_material_id', $id)
            ->where(function($q) {
                $q->whereNull('pe.warehouse_id')->orWhere('pe.warehouse_id', 0);
            })
            ->whereBetween(DB::raw("COALESCE(prmu.created_at, CONCAT(pe.production_date, ' 00:00:00'))"), [$startDT, $endDT]);

        if ($branchId) {
            $usageQuery->where(function($q) use ($branchId) {
                $q->where('prmu.branch_id', $branchId)->orWhere('pe.branch_id', $branchId);
            });
        }

        $usages = $usageQuery->select(
            'prmu.id as usage_id',
            'prmu.production_entry_id',
            'prmu.qty_used',
            'prmu.cost_per_unit',
            'prmu.total_cost',
            'prmu.created_at as usage_created_at',
            'pe.entry_no',
            'pe.production_date',
            'pe.notes as entry_notes',
            'u.name as creator_name'
        )->orderBy('prmu.created_at', 'desc')->get();

        $entryIds = $usages->pluck('production_entry_id')->unique()->toArray();

        // Fetch products produced in these entries
        $entryItemsGrouped = [];
        if (!empty($entryIds)) {
            $items = DB::table('production_entry_items as pei')
                ->join('products as p', 'p.id', '=', 'pei.product_id')
                ->leftJoin('product_variants as pv', 'pv.id', '=', 'pei.variant_id')
                ->whereIn('pei.production_entry_id', $entryIds)
                ->select(
                    'pei.id as item_id',
                    'pei.production_entry_id',
                    'pei.product_id',
                    'pei.variant_id',
                    'pei.unit',
                    'pei.qty_entered',
                    'pei.qty_stock',
                    'p.item_name',
                    'p.item_code',
                    'pv.variant_name',
                    'pv.size_label'
                )->get();

            $entryItemsGrouped = $items->groupBy('production_entry_id');
        }

        // Fetch BOM for this raw material
        $allProductIds = !empty($entryItemsGrouped) ? collect($entryItemsGrouped)->flatten(1)->pluck('product_id')->unique()->toArray() : [];
        $boms = DB::table('product_raw_material_bom')
            ->where('raw_material_id', $id)
            ->whereIn('product_id', $allProductIds)
            ->get();

        $bomLookup = [];
        foreach ($boms as $b) {
            $vKey = $b->variant_id ? ($b->product_id . '_' . $b->variant_id) : ($b->product_id . '_0');
            $bomLookup[$vKey] = (float)$b->qty_per_unit;
            if (!isset($bomLookup[$b->product_id . '_base'])) {
                $bomLookup[$b->product_id . '_base'] = (float)$b->qty_per_unit;
            }
        }

        $records = [];
        $totalUsedConsumption = 0;

        foreach ($usages as $u) {
            $qtyUsed = (float)$u->qty_used;
            $totalUsedConsumption += $qtyUsed;
            $prodItems = $entryItemsGrouped[$u->production_entry_id] ?? collect();

            $productsData = [];
            $totalWeightedExpected = 0;
            $itemsExpected = [];

            foreach ($prodItems as $pi) {
                $vKey = $pi->variant_id ? ($pi->product_id . '_' . $pi->variant_id) : ($pi->product_id . '_0');
                $bomQty = $bomLookup[$vKey] ?? ($bomLookup[$pi->product_id . '_base'] ?? 0);
                $expected = ((float)$pi->qty_entered) * ($bomQty > 0 ? $bomQty : 1);
                $itemsExpected[] = [
                    'item' => $pi,
                    'bom_qty' => $bomQty,
                    'expected' => $expected
                ];
                $totalWeightedExpected += $expected;
            }

            if (count($itemsExpected) === 1) {
                $pi = $itemsExpected[0]['item'];
                $varName = trim(($pi->size_label ? $pi->size_label : '') . ' ' . ($pi->variant_name ? $pi->variant_name : ''));
                $productsData[] = [
                    'product_name' => $pi->item_name . ($varName ? ' (' . $varName . ')' : ''),
                    'product_code' => $pi->item_code,
                    'produced_qty' => (float)$pi->qty_entered . ' ' . ($pi->unit ?? 'Pcs'),
                    'rm_qty_used' => number_format($qtyUsed, 2) . ' ' . ($material->consumption_unit ?? $material->unit),
                    'rm_qty_used_purchase' => number_format($qtyUsed / $factor, 2) . ' ' . $material->unit,
                    'recipe_per_unit' => $itemsExpected[0]['bom_qty'] > 0 ? (number_format($itemsExpected[0]['bom_qty'], 2) . ' ' . ($material->consumption_unit ?? $material->unit)) : '-',
                ];
            } elseif (count($itemsExpected) > 1) {
                foreach ($itemsExpected as $ie) {
                    $pi = $ie['item'];
                    $share = $totalWeightedExpected > 0 ? ($ie['expected'] / $totalWeightedExpected) : (1 / count($itemsExpected));
                    $allocatedUsage = $qtyUsed * $share;
                    $varName = trim(($pi->size_label ? $pi->size_label : '') . ' ' . ($pi->variant_name ? $pi->variant_name : ''));

                    $productsData[] = [
                        'product_name' => $pi->item_name . ($varName ? ' (' . $varName . ')' : ''),
                        'product_code' => $pi->item_code,
                        'produced_qty' => (float)$pi->qty_entered . ' ' . ($pi->unit ?? 'Pcs'),
                        'rm_qty_used' => number_format($allocatedUsage, 2) . ' ' . ($material->consumption_unit ?? $material->unit),
                        'rm_qty_used_purchase' => number_format($allocatedUsage / $factor, 2) . ' ' . $material->unit,
                        'recipe_per_unit' => $ie['bom_qty'] > 0 ? (number_format($ie['bom_qty'], 2) . ' ' . ($material->consumption_unit ?? $material->unit)) : '-',
                    ];
                }
            } else {
                $productsData[] = [
                    'product_name' => 'General Kitchen / Batch Usage',
                    'product_code' => '-',
                    'produced_qty' => '-',
                    'rm_qty_used' => number_format($qtyUsed, 2) . ' ' . ($material->consumption_unit ?? $material->unit),
                    'rm_qty_used_purchase' => number_format($qtyUsed / $factor, 2) . ' ' . $material->unit,
                    'recipe_per_unit' => '-',
                ];
            }

            $records[] = [
                'entry_id' => $u->production_entry_id,
                'entry_no' => $u->entry_no,
                'date' => $u->production_date ? date('d-M-Y', strtotime($u->production_date)) : '-',
                'datetime' => $u->usage_created_at ? date('d-M-Y h:i A', strtotime($u->usage_created_at)) : '-',
                'creator' => $u->creator_name ?? '-',
                'notes' => $u->entry_notes,
                'batch_rm_qty' => number_format($qtyUsed, 2) . ' ' . ($material->consumption_unit ?? $material->unit),
                'batch_rm_qty_purchase' => number_format($qtyUsed / $factor, 2) . ' ' . $material->unit,
                'products' => $productsData,
            ];
        }

        return response()->json([
            'success' => true,
            'material' => [
                'id' => $material->id,
                'name' => $material->name,
                'unit' => $material->unit,
                'consumption_unit' => $material->consumption_unit ?? $material->unit,
                'conversion_factor' => $factor,
            ],
            'filter' => [
                'start' => $startDT,
                'end' => $endDT,
                'formatted' => date('d-M-Y h:i A', strtotime($startDT)) . ' to ' . date('d-M-Y h:i A', strtotime($endDT)),
            ],
            'total_used' => $totalUsedConsumption,
            'total_used_consumption_formatted' => number_format($totalUsedConsumption, 2) . ' ' . ($material->consumption_unit ?? $material->unit),
            'total_used_purchase_formatted' => number_format($totalUsedConsumption / $factor, 2) . ' ' . $material->unit,
            'records' => $records,
        ]);
    }
}

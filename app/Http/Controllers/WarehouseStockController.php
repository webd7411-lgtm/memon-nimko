<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WarehouseStock;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class WarehouseStockController extends Controller
{
    public function index(Request $request)
    {
        $branchId  = active_branch_id();
        $type      = $request->stock_type ?? 'all';
        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $warehouseFilter = $request->warehouse_id;

        /* ======================================================
       1️⃣ PRODUCTS
    ====================================================== */
        $products = Product::with('unit', 'brand')->get();

        // Helper to convert stocks rows into KG / PC units
        $calcStockTotal = function($rows, $isKg) {
            $total = 0;
            foreach ($rows as $st) {
                $qty = (float)$st->qty;
                if ($isKg) {
                    if (!empty($st->variant_id)) {
                        $variant = \App\Models\ProductVariant::find($st->variant_id);
                        if ($variant && floatval($variant->size_value) > 0) {
                            $factor = ($variant->size_unit === 'kg') ? floatval($variant->size_value) : (floatval($variant->size_value) / 1000);
                            $total += ($qty * $factor);
                        } else {
                            $total += $qty;
                        }
                    } else {
                        // Loose product in stocks table is in grams
                        $total += ($qty / 1000);
                    }
                } else {
                    $total += $qty;
                }
            }
            return $total;
        };

        /* ======================================================
       2️⃣ SHOP STOCK — from stocks table (where warehouse_id is null)
    ====================================================== */
        $shopStocksQuery = DB::table('stocks')
            ->where('branch_id', $branchId)
            ->whereNull('warehouse_id');

        $shopStockRowsByProduct = $shopStocksQuery->get()->groupBy('product_id');

        $warehouses = Warehouse::all()->keyBy('id');
        $currentBranch = \App\Models\Branch::find($branchId);
        $currentBranchName = $currentBranch ? $currentBranch->name : 'Shop';

        /* ======================================================
       3️⃣ WAREHOUSE STOCK
    ====================================================== */
        $warehouseQtyByProduct = [];
        $warehouseByProduct    = [];

        // A. From warehouse_stocks table
        $warehouseQuery = WarehouseStock::with('warehouse')
            ->where('branch_id', $branchId);

        if ($warehouseFilter) {
            $warehouseQuery->where('warehouse_id', $warehouseFilter);
        }

        $warehouseRows = $warehouseQuery->get();

        foreach ($warehouseRows as $ws) {
            $pid = $ws->product_id;
            $qty = (float) $ws->quantity;
            $warehouseQtyByProduct[$pid] = ($warehouseQtyByProduct[$pid] ?? 0) + $qty;
            if ($qty > 0 && !isset($warehouseByProduct[$pid])) {
                $warehouseByProduct[$pid] = $ws->warehouse;
            }
        }

        // B. From stocks table (where warehouse_id is not null)
        $stocksWhQuery = DB::table('stocks')
            ->where('branch_id', $branchId)
            ->whereNotNull('warehouse_id');

        if ($warehouseFilter) {
            $stocksWhQuery->where('warehouse_id', $warehouseFilter);
        }

        $stocksWhRowsByProduct = $stocksWhQuery->get()->groupBy('product_id');

        foreach ($stocksWhRowsByProduct as $pid => $whRows) {
            foreach ($whRows as $sw) {
                if ($sw->qty > 0 && !isset($warehouseByProduct[$pid])) {
                    $warehouseByProduct[$pid] = $warehouses->get($sw->warehouse_id);
                }
            }
        }

        /* ======================================================
       4️⃣ FINAL STOCK COLLECTION
    ====================================================== */
        $stocks = collect();

        foreach ($products as $product) {

            $pid = $product->id;

            // ✅ Unit
            $unitType  = strtolower($product->unit_type ?? 'piece');
            $unitName  = strtolower($product->unit->name ?? '');
            $isKg      = ($unitType === 'kg' || $unitType === 'kilogram' || $unitName === 'kg');
            $unitLabel = $isKg ? 'KG' : ($unitType === 'pound' ? 'LB' : ($product->unit->name ?? 'PC'));

            // Shop stock
            $productShopRows = $shopStockRowsByProduct->get($pid) ?? collect();
            $shopQty = round($calcStockTotal($productShopRows, $isKg), 3);

            // Warehouse stock
            $productWhStocksRows = $stocksWhRowsByProduct->get($pid) ?? collect();
            $whFromStocks = $calcStockTotal($productWhStocksRows, $isKg);
            $whFromTable  = (float)($warehouseQtyByProduct[$pid] ?? 0);
            if ($isKg && $whFromTable > 0) {
                $whFromTable = $whFromTable / 1000;
            }
            $warehouseQty = round($whFromStocks + $whFromTable, 3);

            // 🔴 FILTER LOGIC
            if ($type === 'shop'      && $shopQty == 0) continue;
            if ($type === 'warehouse' && $warehouseQty == 0) continue;
            if ($type === 'all'       && $shopQty == 0 && $warehouseQty == 0) continue;

            $row = new WarehouseStock();
            $row->product_id      = $pid;
            $row->product         = $product;
            $row->unit_label      = $unitLabel;
            $row->warehouse       = $warehouseByProduct[$pid] ?? null;
            $row->warehouse_id    = isset($warehouseByProduct[$pid]) ? $warehouseByProduct[$pid]->id : null;
            $row->shop_stock      = $shopQty;
            $row->warehouse_stock = $warehouseQty;
            $row->total_stock     = $shopQty + $warehouseQty;
            $row->quantity        = $warehouseQty;

            if ($warehouseQty > 0 && $shopQty > 0) {
                $row->remarks = 'Shop & Warehouse';
            } elseif ($warehouseQty > 0 && $shopQty == 0) {
                $row->remarks = 'Warehouse Only';
            } elseif ($shopQty > 0 && $warehouseQty == 0) {
                $row->remarks = 'Shop Only';
            } else {
                $row->remarks = '—';
            }

            $row->created_at      = now();

            $stocks->push($row);
        }

        return view('admin_panel.warehouses.warehouse_stocks.index',
            compact('stocks', 'currentBranchName')
        );
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        $products = Product::all();
        return view('admin_panel.warehouses.warehouse_stocks.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer|min:0'
        ]);

        WarehouseStock::create($request->all());
        return redirect()->route('warehouse_stocks.index')->with('success', 'Stock added successfully.');
    }

    public function edit(WarehouseStock $warehouseStock)
    {
        $warehouses = Warehouse::all();
        $products = Product::all();
        return view('admin_panel.warehouses.warehouse_stocks.edit', compact('warehouseStock', 'warehouses', 'products'));
    }

    public function update(Request $request, WarehouseStock $warehouseStock)
    {
        $request->validate([
            'warehouse_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer|min:0'
        ]);

        $warehouseStock->update($request->all());
        return redirect()->route('warehouse_stocks.index')->with('success', 'Stock updated successfully.');
    }

    public function destroy(WarehouseStock $warehouseStock)
    {
        $warehouseStock->delete();
        return redirect()->route('warehouse_stocks.index')->with('success', 'Stock deleted successfully.');
    }
}

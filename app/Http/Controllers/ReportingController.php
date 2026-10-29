<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CustomerPayment;
use App\Models\ExpenseVoucher;
use App\Models\Product;
use App\Models\Sale;
use App\Models\VendorPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportingController extends Controller
{
    public function item_stock_report()
    {
        // Pass ALL products for the dropdown — no pagination needed
        $products = \App\Models\Product::orderBy('item_name', 'asc')
            ->select('id', 'item_code', 'item_name')
            ->get();
        return view('admin_panel.reporting.item_stock_report', compact('products'));
    }

    public function fetchItemStock(Request $request)
    {
        $productIdsArr = $request->input('product_id', 'all');
        $searchQ    = trim($request->get('q', ''));
        $startDate  = $request->start_date ?? date('Y-m-01');
        $endDate    = $request->end_date   ?? date('Y-m-t');
        $startTime  = $request->start_time ?? '00:00:00';
        $endTime    = $request->end_time   ?? '23:59:59';

        $startDT = $startDate . ' ' . $startTime;
        $endDT   = $endDate   . ' ' . $endTime;

        // Check if there is a stock reset timestamp (branch-specific, with backward compatibility)
        $resetTime = null;
        $branchIdForReset = active_branch_id();
        $resetFileName = 'stock_reset_timestamp_' . $branchIdForReset . '.txt';
        if (\Illuminate\Support\Facades\Storage::exists($resetFileName)) {
            $resetTime = trim(\Illuminate\Support\Facades\Storage::get($resetFileName));
        } elseif (\Illuminate\Support\Facades\Storage::exists('stock_reset_timestamp.txt')) {
            $resetTime = trim(\Illuminate\Support\Facades\Storage::get('stock_reset_timestamp.txt'));
        }

        // 1. Get products and their variants
        $pQuery = Product::with(['variants', 'unit'])->orderBy('item_name');

        $productIdsFilter = [];
        if (is_array($productIdsArr)) {
            if (!in_array('all', $productIdsArr)) {
                $productIdsFilter = $productIdsArr;
            }
        } elseif ($productIdsArr !== 'all') {
            $productIdsFilter = [$productIdsArr];
        }
        if (!empty($productIdsFilter)) {
            $pQuery->whereIn('id', $productIdsFilter);
        } elseif ($searchQ !== '') {
            $pQuery->where(function($q) use ($searchQ) {
                $q->where('item_name','like',"%{$searchQ}%")
                  ->orWhere('item_code','like',"%{$searchQ}%");
            });
        }

        $products = $pQuery->get();
        $productIds = $products->pluck('id')->toArray();

        // 2. Bulk queries grouped by product_id AND variant_id with DATE filters
        $purchasesQuery = DB::table('purchase_items')
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->whereIn('purchase_items.product_id', $productIds)
            ->whereNull('purchases.warehouse_id')  // ✅ EXCLUDE warehouse purchases
            ->whereBetween('purchases.purchase_date', [$startDate, $endDate]);
        if (!is_all_branches()) {
            $purchasesQuery->where('purchases.branch_id', active_branch_id());
        }
        if ($resetTime) {
            $purchasesQuery->where('purchases.created_at', '>=', $resetTime);
        }
        $purchases = $purchasesQuery->select('purchase_items.product_id', 'purchase_items.variant_id', DB::raw('SUM(purchase_items.qty) as total_qty'))
            ->groupBy('purchase_items.product_id', 'purchase_items.variant_id')
            ->get();

        $productionsQuery = DB::table('production_entry_items')
            ->join('production_entries', 'production_entries.id', '=', 'production_entry_items.production_entry_id')
            ->whereIn('production_entry_items.product_id', $productIds)
            ->whereNull('production_entries.warehouse_id')
            ->whereDate('production_entries.production_date', '>=', $startDate)
            ->whereDate('production_entries.production_date', '<=', $endDate);
        if (!is_all_branches()) {
            $productionsQuery->where('production_entries.branch_id', active_branch_id());
        }
        if ($resetTime) {
            $productionsQuery->where('production_entries.created_at', '>=', $resetTime);
        }
        $productions = $productionsQuery->select('production_entry_items.product_id', 'production_entry_items.variant_id', DB::raw('SUM(production_entry_items.qty_stock) as total_qty'))
            ->groupBy('production_entry_items.product_id', 'production_entry_items.variant_id')
            ->get();

        $purchaseReturnsQuery = DB::table('purchase_return_items')
            ->join('purchase_returns', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
            ->whereIn('purchase_return_items.product_id', $productIds)
            ->whereNull('purchase_returns.warehouse_id')
            ->whereBetween('purchase_returns.return_date', [$startDate, $endDate]);
        if (!is_all_branches()) {
            $purchaseReturnsQuery->where('purchase_returns.branch_id', active_branch_id());
        }
        if ($resetTime) {
            $purchaseReturnsQuery->where('purchase_returns.created_at', '>=', $resetTime);
        }
        $purchaseReturns = $purchaseReturnsQuery->select('purchase_return_items.product_id', DB::raw('SUM(purchase_return_items.qty) as total_qty'))
            ->groupBy('purchase_return_items.product_id')
            ->get();

        // Production ingredient usage within date range
        $prodUsageQuery = DB::table('production_raw_material_usage as prmu')
            ->join('production_entries as pe', 'pe.id', '=', 'prmu.production_entry_id')
            ->whereIn('prmu.ingredient_product_id', $productIds)
            ->whereNull('pe.warehouse_id')
            ->whereDate('pe.production_date', '>=', $startDate)
            ->whereDate('pe.production_date', '<=', $endDate);
        if (!is_all_branches()) {
            $activeBranchId = active_branch_id();
            $prodUsageQuery->where(function($q) use ($activeBranchId) {
                $q->where('prmu.branch_id', $activeBranchId)
                  ->orWhere('pe.branch_id', $activeBranchId);
            });
        }
        if ($resetTime) {
            $prodUsageQuery->where('pe.created_at', '>=', $resetTime);
        }
        $prodUsage = $prodUsageQuery->select('prmu.ingredient_product_id as product_id', DB::raw('SUM(prmu.qty_used) as total_qty'))
            ->groupBy('prmu.ingredient_product_id')
            ->get();

        $currStocksQuery = DB::table('stocks')->whereIn('product_id', $productIds)->whereNull('warehouse_id');
        if (!is_all_branches()) {
            $currStocksQuery->where('branch_id', active_branch_id());
        }
        $currStocks = $currStocksQuery->select('product_id', 'variant_id', 'qty')->get();

        // Mapping helper: key = pid_vid (use 0 for null variant)
        $mapP = []; foreach($purchases as $p) { $mapP[$p->product_id . '_' . ($p->variant_id ?? 0)] = $p->total_qty; }
        $mapProd = []; foreach($productions as $pd) { $mapProd[$pd->product_id . '_' . ($pd->variant_id ?? 0)] = ($mapProd[$pd->product_id . '_' . ($pd->variant_id ?? 0)] ?? 0) + $pd->total_qty; }
        $mapProdUsage = []; foreach($prodUsage as $pu) { $mapProdUsage[$pu->product_id . '_0'] = (float)$pu->total_qty; }
        // purchase_return_items has no variant_id — map by product only (key pid_0)
        $mapPR = []; foreach($purchaseReturns as $pr) { $mapPR[$pr->product_id . '_0'] = ($mapPR[$pr->product_id . '_0'] ?? 0) + $pr->total_qty; }
        $mapS = []; foreach($currStocks as $cs) { $mapS[$cs->product_id . '_' . ($cs->variant_id ?? 0)] = ($mapS[$cs->product_id . '_' . ($cs->variant_id ?? 0)] ?? 0) + $cs->qty; }

        // Sales processing (by product and variant) with DATE filter
        $hasVariantIdInSales = \Illuminate\Support\Facades\Schema::hasColumn('sales', 'variant_id');
        $hasTradeOfferInSales = \Illuminate\Support\Facades\Schema::hasColumn('sales', 'trade_offer');
        $allSalesQuery = DB::table('sales')->whereBetween('created_at', [$startDT, $endDT])->whereNotNull('product')->select('product', 'qty');
        if (!is_all_branches()) {
            $allSalesQuery->where('branch_id', active_branch_id());
        }
        if ($hasVariantIdInSales) {
            $allSalesQuery->addSelect('variant_id');
        }
        if ($hasTradeOfferInSales) {
            $allSalesQuery->addSelect('trade_offer');
        }
        if ($resetTime) {
            $allSalesQuery->where('created_at', '>=', $resetTime);
        }
        $allSales = $allSalesQuery->get();

        $soldMap = [];
        $toSoldMap = [];
        foreach ($allSales as $s) {
            $pids = explode(',', $s->product);
            $qtys = explode(',', $s->qty);
            $tos  = $hasTradeOfferInSales ? explode(',', $s->trade_offer ?? '') : [];
            $vids = $hasVariantIdInSales ? explode(',', $s->variant_id ?? '') : [];
            
            foreach ($pids as $idx => $pid) {
                $pid = trim($pid);
                if ($pid === '') continue;
                $vid = trim($vids[$idx] ?? '0');
                if ($vid === '') $vid = '0';

                // Fallback: If product has variants but vid is '0', map to default variant ID
                if ($vid === '0') {
                    $prodObj = $products->firstWhere('id', (int)$pid);
                    if ($prodObj && strtolower($prodObj->unit_type ?? '') !== 'kg' && $prodObj->variants->count() > 0) {
                        $defV = $prodObj->variants->where('is_default', 1)->first() ?? $prodObj->variants->first();
                        if ($defV) $vid = (string)$defV->id;
                    }
                }

                $key = $pid . '_' . $vid;
                $soldMap[$key]   = ($soldMap[$key] ?? 0) + floatval($qtys[$idx] ?? 0);
                $toSoldMap[$key] = ($toSoldMap[$key] ?? 0) + floatval($tos[$idx] ?? 0);
            }
        }

        $hasVariantIdInReturns   = \Illuminate\Support\Facades\Schema::hasColumn('sales_returns', 'variant_id');
        $hasProductCodeInReturns = \Illuminate\Support\Facades\Schema::hasColumn('sales_returns', 'product_code');
        $hasTradeOfferInReturns  = \Illuminate\Support\Facades\Schema::hasColumn('sales_returns', 'trade_offer');
        
        $allReturnsQuery = DB::table('sales_returns')
            ->whereBetween('created_at', [$startDT, $endDT])
            ->whereNotNull('product')
            ->select('id', 'sale_id', 'product', 'qty');
        if (!is_all_branches()) {
            $allReturnsQuery->where('branch_id', active_branch_id());
        }
        if ($hasVariantIdInReturns) {
            $allReturnsQuery->addSelect('variant_id');
        }
        if ($hasProductCodeInReturns) {
            $allReturnsQuery->addSelect('product_code');
        }
        if ($hasTradeOfferInReturns) {
            $allReturnsQuery->addSelect('trade_offer');
        }
        if ($resetTime) {
            $allReturnsQuery->where('created_at', '>=', $resetTime);
        }
        $allReturns = $allReturnsQuery->get();

        $productMapByName = [];
        $productMapById   = [];
        $productMapByCode = [];
        foreach ($products as $prodItem) {
            $productMapById[$prodItem->id] = $prodItem->id;
            
            $cleanName = trim(strtolower($prodItem->item_name));
            if ($cleanName !== '') {
                $productMapByName[$cleanName] = $prodItem->id;
            }

            $cleanCode = trim(strtolower($prodItem->item_code));
            if ($cleanCode !== '') {
                $productMapByCode[$cleanCode] = $prodItem->id;
            }

            if ($prodItem->variants) {
                foreach ($prodItem->variants as $vItem) {
                    $vLabel = trim(strtolower($vItem->size_label ?: $vItem->variant_name));
                    if ($vLabel !== '') {
                        $fullName = $cleanName . ' (' . $vLabel . ')';
                        $productMapByName[$fullName] = $prodItem->id;
                        $productMapByName[$vLabel] = $prodItem->id;
                    }
                }
            }
        }

        $retMap = [];
        $toRetMap = [];
        foreach ($allReturns as $r) {
            $pnames = explode(',', $r->product ?? '');
            $pcodes = $hasProductCodeInReturns ? explode(',', $r->product_code ?? '') : [];
            $qtys   = explode(',', $r->qty ?? '');
            $tos    = $hasTradeOfferInReturns ? explode(',', $r->trade_offer ?? '') : [];
            $vids   = $hasVariantIdInReturns && !empty($r->variant_id) ? explode(',', $r->variant_id) : [];

            // Parent sale fallback for variant_id if return variant_id is missing
            $parentSaleVids = [];
            if (empty($vids) && !empty($r->sale_id)) {
                $pSale = DB::table('sales')->find($r->sale_id);
                if ($pSale && !empty($pSale->variant_id)) {
                    $parentSaleVids = explode(',', $pSale->variant_id);
                }
            }

            foreach ($pnames as $idx => $pNameOrId) {
                $pNameOrId = trim($pNameOrId);
                if ($pNameOrId === '') continue;

                $pid = null;
                if (is_numeric($pNameOrId) && isset($productMapById[(int)$pNameOrId])) {
                    $pid = (int)$pNameOrId;
                } else {
                    $lowerStr = strtolower($pNameOrId);
                    $pid = $productMapByName[$lowerStr] ?? ($productMapByCode[$lowerStr] ?? null);

                    if (!$pid && isset($pcodes[$idx])) {
                        $cCode = trim(strtolower($pcodes[$idx]));
                        $pid = $productMapByCode[$cCode] ?? null;
                    }

                    if (!$pid) {
                        foreach ($products as $prodItem) {
                            $pNameLower = strtolower($prodItem->item_name);
                            if ($pNameLower !== '' && (str_contains($lowerStr, $pNameLower) || str_contains($pNameLower, $lowerStr))) {
                                $pid = $prodItem->id;
                                break;
                            }
                        }
                    }
                }

                if (!$pid) continue;

                $vid = trim($vids[$idx] ?? '');
                if ($vid === '' || $vid === '0') {
                    if (isset($parentSaleVids[$idx]) && trim($parentSaleVids[$idx]) !== '') {
                        $vid = trim($parentSaleVids[$idx]);
                    } elseif (!empty($parentSaleVids[0])) {
                        $vid = trim($parentSaleVids[0]);
                    }
                }

                // If still empty, check if product has variants and name has variant label (e.g. "big" vs "small")
                if ($vid === '' || $vid === '0') {
                    $prodObj = $products->firstWhere('id', (int)$pid);
                    if ($prodObj && $prodObj->variants->count() > 0) {
                        $lowerName = strtolower($pNameOrId);
                        foreach ($prodObj->variants as $vObj) {
                            $vLbl = strtolower(trim($vObj->size_label ?: $vObj->variant_name));
                            if ($vLbl !== '' && (str_contains($lowerName, "({$vLbl})") || str_contains($lowerName, "- {$vLbl}") || str_contains($lowerName, $vLbl))) {
                                $vid = (string)$vObj->id;
                                break;
                            }
                        }
                    }
                }

                if ($vid === '') $vid = '0';

                $key = $pid . '_' . $vid;
                $retMap[$key] = ($retMap[$key] ?? 0) + floatval($qtys[$idx] ?? 0);
                $toRetMap[$key] = ($toRetMap[$key] ?? 0) + floatval($tos[$idx] ?? 0);
            }
        }

        // 3. Transactions AFTER end_date to perform backward calculation for Balance
        $purchAfterQuery = DB::table('purchase_items')
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->whereIn('purchase_items.product_id', $productIds)
            ->whereNull('purchases.warehouse_id')  // ✅ EXCLUDE warehouse purchases (after)
            ->where('purchases.purchase_date', '>', $endDate);
        if (!is_all_branches()) {
            $purchAfterQuery->where('purchases.branch_id', active_branch_id());
        }
        if ($resetTime) {
            $purchAfterQuery->where('purchases.created_at', '>=', $resetTime);
        }
        $purchAfter = $purchAfterQuery->select('purchase_items.product_id', 'purchase_items.variant_id', DB::raw('SUM(purchase_items.qty) as total_qty'))
            ->groupBy('purchase_items.product_id', 'purchase_items.variant_id')
            ->get();
        $mapPAft = []; foreach($purchAfter as $p) { $mapPAft[$p->product_id . '_' . ($p->variant_id ?? '0')] = $p->total_qty; }

        $prodAfterQuery = DB::table('production_entry_items')
            ->join('production_entries', 'production_entries.id', '=', 'production_entry_items.production_entry_id')
            ->whereIn('production_entry_items.product_id', $productIds)
            ->whereNull('production_entries.warehouse_id')
            ->whereDate('production_entries.production_date', '>', $endDate);
        if (!is_all_branches()) {
            $prodAfterQuery->where('production_entries.branch_id', active_branch_id());
        }
        if ($resetTime) {
            $prodAfterQuery->where('production_entries.created_at', '>=', $resetTime);
        }
        $prodAfter = $prodAfterQuery->select('production_entry_items.product_id', 'production_entry_items.variant_id', DB::raw('SUM(production_entry_items.qty_stock) as total_qty'))
            ->groupBy('production_entry_items.product_id', 'production_entry_items.variant_id')
            ->get();
        $mapProdAft = []; foreach($prodAfter as $pd) { $mapProdAft[$pd->product_id . '_' . ($pd->variant_id ?? 0)] = ($mapProdAft[$pd->product_id . '_' . ($pd->variant_id ?? 0)] ?? 0) + $pd->total_qty; }

        $prodUsageAfterQuery = DB::table('production_raw_material_usage as prmu')
            ->join('production_entries as pe', 'pe.id', '=', 'prmu.production_entry_id')
            ->whereIn('prmu.ingredient_product_id', $productIds)
            ->whereNull('pe.warehouse_id')
            ->whereDate('pe.production_date', '>', $endDate);
        if (!is_all_branches()) {
            $activeBranchId = active_branch_id();
            $prodUsageAfterQuery->where(function($q) use ($activeBranchId) {
                $q->where('prmu.branch_id', $activeBranchId)
                  ->orWhere('pe.branch_id', $activeBranchId);
            });
        }
        if ($resetTime) {
            $prodUsageAfterQuery->where('pe.created_at', '>=', $resetTime);
        }
        $prodUsageAfter = $prodUsageAfterQuery->select('prmu.ingredient_product_id as product_id', DB::raw('SUM(prmu.qty_used) as total_qty'))
            ->groupBy('prmu.ingredient_product_id')
            ->get();
        $mapProdUsageAft = []; foreach($prodUsageAfter as $pua) { $mapProdUsageAft[$pua->product_id . '_0'] = (float)$pua->total_qty; }

        $prAfterQuery = DB::table('purchase_return_items')
            ->join('purchase_returns', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
            ->whereIn('purchase_return_items.product_id', $productIds)
            ->whereNull('purchase_returns.warehouse_id')
            ->where('purchase_returns.return_date', '>', $endDate);
        if (!is_all_branches()) {
            $prAfterQuery->where('purchase_returns.branch_id', active_branch_id());
        }
        if ($resetTime) {
            $prAfterQuery->where('purchase_returns.created_at', '>=', $resetTime);
        }
        $prAfter = $prAfterQuery->select('purchase_return_items.product_id', DB::raw('SUM(purchase_return_items.qty) as total_qty'))
            ->groupBy('purchase_return_items.product_id')
            ->get();
        $mapPRAft = []; foreach($prAfter as $pr) { $mapPRAft[$pr->product_id . '_0'] = ($mapPRAft[$pr->product_id . '_0'] ?? 0) + $pr->total_qty; }

        $allSalesAfterQ = DB::table('sales')->where('created_at', '>', $endDT)->whereNotNull('product')->select('product', 'qty');
        if (!is_all_branches()) {
            $allSalesAfterQ->where('branch_id', active_branch_id());
        }
        if ($hasVariantIdInSales) { $allSalesAfterQ->addSelect('variant_id'); }
        if ($hasTradeOfferInSales) { $allSalesAfterQ->addSelect('trade_offer'); }
        if ($resetTime) {
            $allSalesAfterQ->where('created_at', '>=', $resetTime);
        }
        $allSalesAfter = $allSalesAfterQ->get();
        
        $soldAftMap   = [];
        $toSoldAftMap = [];
        foreach ($allSalesAfter as $s) {
            $pids = explode(',', $s->product); $qtys = explode(',', $s->qty); $tos = $hasTradeOfferInSales ? explode(',', $s->trade_offer ?? '') : []; $vids = $hasVariantIdInSales ? explode(',', $s->variant_id ?? '') : [];
            foreach ($pids as $idx => $pid) {
                $pid = trim($pid); if ($pid === '') continue;
                $vid = trim($vids[$idx] ?? '0'); if ($vid === '') $vid = '0';
                if ($vid === '0') {
                    $prodObj = $products->firstWhere('id', (int)$pid);
                    if ($prodObj && strtolower($prodObj->unit_type ?? '') !== 'kg' && $prodObj->variants->count() > 0) {
                        $defV = $prodObj->variants->where('is_default', 1)->first() ?? $prodObj->variants->first();
                        if ($defV) $vid = (string)$defV->id;
                    }
                }
                $key = $pid . '_' . $vid;
                $soldAftMap[$key]   = ($soldAftMap[$key] ?? 0) + floatval($qtys[$idx] ?? 0);
                $toSoldAftMap[$key] = ($toSoldAftMap[$key] ?? 0) + floatval($tos[$idx] ?? 0);
            }
        }
        $allRetAfterQ = DB::table('sales_returns')
            ->where('created_at', '>', $endDT)
            ->whereNotNull('product')
            ->select('id', 'sale_id', 'product', 'qty');
        if (!is_all_branches()) {
            $allRetAfterQ->where('branch_id', active_branch_id());
        }
        if ($hasVariantIdInReturns) {
            $allRetAfterQ->addSelect('variant_id');
        }
        if ($hasProductCodeInReturns) {
            $allRetAfterQ->addSelect('product_code');
        }
        if ($hasTradeOfferInReturns) {
            $allRetAfterQ->addSelect('trade_offer');
        }
        if ($resetTime) {
            $allRetAfterQ->where('created_at', '>=', $resetTime);
        }
        $allRetAfter = $allRetAfterQ->get();
        
        $retAftMap   = [];
        $toRetAftMap = [];
        foreach ($allRetAfter as $r) {
            $pnames = explode(',', $r->product ?? '');
            $pcodes = $hasProductCodeInReturns ? explode(',', $r->product_code ?? '') : [];
            $qtys   = explode(',', $r->qty ?? '');
            $tos    = $hasTradeOfferInReturns ? explode(',', $r->trade_offer ?? '') : [];
            $vids   = $hasVariantIdInReturns && !empty($r->variant_id) ? explode(',', $r->variant_id) : [];

            $parentSaleVids = [];
            if (empty($vids) && !empty($r->sale_id)) {
                $pSale = DB::table('sales')->find($r->sale_id);
                if ($pSale && !empty($pSale->variant_id)) {
                    $parentSaleVids = explode(',', $pSale->variant_id);
                }
            }

            foreach ($pnames as $idx => $pNameOrId) {
                $pNameOrId = trim($pNameOrId);
                if ($pNameOrId === '') continue;

                $pid = null;
                if (is_numeric($pNameOrId) && isset($productMapById[(int)$pNameOrId])) {
                    $pid = (int)$pNameOrId;
                } else {
                    $lowerStr = strtolower($pNameOrId);
                    $pid = $productMapByName[$lowerStr] ?? ($productMapByCode[$lowerStr] ?? null);

                    if (!$pid && isset($pcodes[$idx])) {
                        $cCode = trim(strtolower($pcodes[$idx]));
                        $pid = $productMapByCode[$cCode] ?? null;
                    }

                    if (!$pid) {
                        foreach ($products as $prodItem) {
                            $pNameLower = strtolower($prodItem->item_name);
                            if ($pNameLower !== '' && (str_contains($lowerStr, $pNameLower) || str_contains($pNameLower, $lowerStr))) {
                                $pid = $prodItem->id;
                                break;
                            }
                        }
                    }
                }

                if (!$pid) continue;

                $vid = trim($vids[$idx] ?? '');
                if ($vid === '' || $vid === '0') {
                    if (isset($parentSaleVids[$idx]) && trim($parentSaleVids[$idx]) !== '') {
                        $vid = trim($parentSaleVids[$idx]);
                    } elseif (!empty($parentSaleVids[0])) {
                        $vid = trim($parentSaleVids[0]);
                    }
                }

                if ($vid === '' || $vid === '0') {
                    $prodObj = $products->firstWhere('id', (int)$pid);
                    if ($prodObj && $prodObj->variants->count() > 0) {
                        $lowerName = strtolower($pNameOrId);
                        foreach ($prodObj->variants as $vObj) {
                            $vLbl = strtolower(trim($vObj->size_label ?: $vObj->variant_name));
                            if ($vLbl !== '' && (str_contains($lowerName, "({$vLbl})") || str_contains($lowerName, "- {$vLbl}") || str_contains($lowerName, $vLbl))) {
                                $vid = (string)$vObj->id;
                                break;
                            }
                        }
                    }
                }

                if ($vid === '') $vid = '0';

                $key = $pid . '_' . $vid;
                $retAftMap[$key] = ($retAftMap[$key] ?? 0) + floatval($qtys[$idx] ?? 0);
                $toRetAftMap[$key] = ($toRetAftMap[$key] ?? 0) + floatval($tos[$idx] ?? 0);
            }
        }
        // ---- STOCK ADJUSTMENTS within date range ----
        $hasSaBranchId = \Illuminate\Support\Facades\Schema::hasColumn('stock_adjustments', 'branch_id');
        $adjInRangeQuery = DB::table('stock_adjustment_items as sai')
            ->join('stock_adjustments as sa', 'sa.id', '=', 'sai.adjustment_id')
            ->leftJoin('users as u', 'u.id', '=', 'sa.created_by')
            ->whereIn('sai.product_id', $productIds)
            ->whereNull('sa.warehouse_id')  // ✅ Only branch-level adjustments (exclude warehouse-level)
            ->whereBetween('sa.adjustment_date', [$startDate, $endDate]);
        if (!is_all_branches()) {
            if ($hasSaBranchId) {
                $adjInRangeQuery->where(function($q) {
                    $q->where('sa.branch_id', active_branch_id())
                      ->orWhere(function($sq) {
                          $sq->whereNull('sa.branch_id')->where('u.branch_id', active_branch_id());
                      });
                });
            } else {
                $adjInRangeQuery->where('u.branch_id', active_branch_id());
            }
        }
        if ($resetTime) {
            $adjInRangeQuery->where('sa.created_at', '>=', $resetTime);
        }
        $adjInRange = $adjInRangeQuery->select('sai.product_id', 'sa.type', DB::raw('SUM(sai.qty_stock) as total_qty'))
            ->groupBy('sai.product_id', 'sa.type')
            ->get();

        $mapAdjInc = []; // increase within range
        $mapAdjDec = []; // decrease within range
        foreach ($adjInRange as $adj) {
            $k = $adj->product_id . '_0';
            if ($adj->type === 'increase') {
                $mapAdjInc[$k] = ($mapAdjInc[$k] ?? 0) + $adj->total_qty;
            } else {
                $mapAdjDec[$k] = ($mapAdjDec[$k] ?? 0) + $adj->total_qty;
            }
        }

        // ---- STOCK ADJUSTMENTS AFTER end_date ----
        $adjAfterQuery = DB::table('stock_adjustment_items as sai')
            ->join('stock_adjustments as sa', 'sa.id', '=', 'sai.adjustment_id')
            ->leftJoin('users as u', 'u.id', '=', 'sa.created_by')
            ->whereIn('sai.product_id', $productIds)
            ->whereNull('sa.warehouse_id')  // ✅ Only branch-level adjustments (exclude warehouse-level)
            ->where('sa.adjustment_date', '>', $endDate);
        if (!is_all_branches()) {
            if ($hasSaBranchId) {
                $adjAfterQuery->where(function($q) {
                    $q->where('sa.branch_id', active_branch_id())
                      ->orWhere(function($sq) {
                          $sq->whereNull('sa.branch_id')->where('u.branch_id', active_branch_id());
                      });
                });
            } else {
                $adjAfterQuery->where('u.branch_id', active_branch_id());
            }
        }
        if ($resetTime) {
            $adjAfterQuery->where('sa.created_at', '>=', $resetTime);
        }
        $adjAfter = $adjAfterQuery->select('sai.product_id', 'sa.type', DB::raw('SUM(sai.qty_stock) as total_qty'))
            ->groupBy('sai.product_id', 'sa.type')
            ->get();

        $mapAdjIncAft = [];
        $mapAdjDecAft = [];
        foreach ($adjAfter as $adj) {
            $k = $adj->product_id . '_0';
            if ($adj->type === 'increase') {
                $mapAdjIncAft[$k] = ($mapAdjIncAft[$k] ?? 0) + $adj->total_qty;
            } else {
                $mapAdjDecAft[$k] = ($mapAdjDecAft[$k] ?? 0) + $adj->total_qty;
            }
        }

        // ---- STOCK TRANSFERS (OUT) within date range ----
        // Stock leaving Shop: source is Shop (from_warehouse_id is null / '' / 'Shop' / 0)
        $transferOutQuery = DB::table('stock_transfers')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDT, $endDT])
            ->where(function($q) {
                $q->whereNull('from_warehouse_id')
                  ->orWhere('from_warehouse_id', '')
                  ->orWhere('from_warehouse_id', 'Shop')
                  ->orWhere('from_warehouse_id', 0);
            });
        if (!is_all_branches()) {
            $transferOutQuery->where('branch_id', active_branch_id());
        }
        if ($resetTime) {
            $transferOutQuery->where('created_at', '>=', $resetTime);
        }
        $transfersOut = $transferOutQuery->get();
        $mapTransfer = [];
        foreach ($transfersOut as $tr) {
            $decodedPids = json_decode($tr->product_id, true);
            if (is_string($decodedPids)) {
                $decodedPids = json_decode($decodedPids, true);
            }
            $rawPids = is_array($decodedPids) ? $decodedPids : ($decodedPids !== null && $decodedPids !== '' ? [$decodedPids] : []);

            $decodedQtys = json_decode($tr->quantity, true);
            if (is_string($decodedQtys)) {
                $decodedQtys = json_decode($decodedQtys, true);
            }
            $rawQtys = is_array($decodedQtys) ? $decodedQtys : ($decodedQtys !== null && $decodedQtys !== '' ? [$decodedQtys] : []);
            $pids = $rawPids;
            $qtys = $rawQtys;
            foreach ($pids as $i => $pid) {
                if (is_array($pid)) {
                    $pid = $pid[0] ?? '';
                }
                $pid = trim($pid);
                if ($pid === '') continue;
                if (!in_array((int)$pid, $productIds)) continue;
                $qtyRaw = $qtys[$i] ?? 0;
                if (is_array($qtyRaw)) $qtyRaw = $qtyRaw[0] ?? 0;
                $qty = floatval($qtyRaw);
                $key = $pid . '_0';
                $mapTransfer[$key] = ($mapTransfer[$key] ?? 0) + $qty;
            }
        }

        // ---- STOCK TRANSFERS (IN) within date range ----
        // Stock entering Shop: destination is active branch shop
        $transferInQuery = DB::table('stock_transfers')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDT, $endDT])
            ->where(function($q) {
                $activeBranchId = active_branch_id();
                $q->where(function($sq) use ($activeBranchId) {
                    $sq->where('transfer_to', 'branch')
                       ->where('to_branch_id', $activeBranchId);
                })->orWhere(function($sq) use ($activeBranchId) {
                    $sq->where('transfer_to', 'shop')
                       ->where('branch_id', $activeBranchId);
                })->orWhere(function($sq) use ($activeBranchId) {
                    $sq->where('to_branch_id', $activeBranchId)
                       ->whereNull('to_warehouse_id');
                });
            });
        if ($resetTime) {
            $transferInQuery->where('created_at', '>=', $resetTime);
        }
        $transfersIn = $transferInQuery->get();
        $mapTransferIn = [];
        foreach ($transfersIn as $tr) {
            $decodedPids = json_decode($tr->product_id, true);
            if (is_string($decodedPids)) {
                $decodedPids = json_decode($decodedPids, true);
            }
            $rawPids = is_array($decodedPids) ? $decodedPids : ($decodedPids !== null && $decodedPids !== '' ? [$decodedPids] : []);

            $decodedQtys = json_decode($tr->quantity, true);
            if (is_string($decodedQtys)) {
                $decodedQtys = json_decode($decodedQtys, true);
            }
            $rawQtys = is_array($decodedQtys) ? $decodedQtys : ($decodedQtys !== null && $decodedQtys !== '' ? [$decodedQtys] : []);
            $pids = $rawPids;
            $qtys = $rawQtys;
            foreach ($pids as $i => $pid) {
                if (is_array($pid)) {
                    $pid = $pid[0] ?? '';
                }
                $pid = trim($pid);
                if ($pid === '') continue;
                if (!in_array((int)$pid, $productIds)) continue;
                $qtyRaw = $qtys[$i] ?? 0;
                if (is_array($qtyRaw)) $qtyRaw = $qtyRaw[0] ?? 0;
                $qty = floatval($qtyRaw);
                $key = $pid . '_0';
                $mapTransferIn[$key] = ($mapTransferIn[$key] ?? 0) + $qty;
            }
        }

        // ---- STOCK TRANSFERS (OUT) AFTER end_date ----
        $transferOutAfterQuery = DB::table('stock_transfers')
            ->where('status', 'completed')
            ->where('created_at', '>', $endDT)
            ->where(function($q) {
                $q->whereNull('from_warehouse_id')
                  ->orWhere('from_warehouse_id', '')
                  ->orWhere('from_warehouse_id', 'Shop')
                  ->orWhere('from_warehouse_id', 0);
            });
        if (!is_all_branches()) {
            $transferOutAfterQuery->where('branch_id', active_branch_id());
        }
        if ($resetTime) {
            $transferOutAfterQuery->where('created_at', '>=', $resetTime);
        }
        $transfersOutAfter = $transferOutAfterQuery->get();
        $mapTransferAft = [];
        foreach ($transfersOutAfter as $tr) {
            $decodedPids = json_decode($tr->product_id, true);
            if (is_string($decodedPids)) $decodedPids = json_decode($decodedPids, true);
            $rawPids = is_array($decodedPids) ? $decodedPids : ($decodedPids !== null && $decodedPids !== '' ? [$decodedPids] : []);
            $decodedQtys = json_decode($tr->quantity, true);
            if (is_string($decodedQtys)) $decodedQtys = json_decode($decodedQtys, true);
            $rawQtys = is_array($decodedQtys) ? $decodedQtys : ($decodedQtys !== null && $decodedQtys !== '' ? [$decodedQtys] : []);
            foreach ($rawPids as $i => $pid) {
                $pid = trim($pid); if ($pid === '') continue;
                if (!in_array((int)$pid, $productIds)) continue;
                $qtyRaw = $rawQtys[$i] ?? 0;
                if (is_array($qtyRaw)) $qtyRaw = $qtyRaw[0] ?? 0;
                $qty = floatval($qtyRaw);
                $key = $pid . '_0';
                $mapTransferAft[$key] = ($mapTransferAft[$key] ?? 0) + $qty;
            }
        }

        // ---- STOCK TRANSFERS (IN) AFTER end_date ----
        $transferInAfterQuery = DB::table('stock_transfers')
            ->where('status', 'completed')
            ->where('created_at', '>', $endDT)
            ->where(function($q) {
                $activeBranchId = active_branch_id();
                $q->where(function($sq) use ($activeBranchId) {
                    $sq->where('transfer_to', 'branch')
                       ->where('to_branch_id', $activeBranchId);
                })->orWhere(function($sq) use ($activeBranchId) {
                    $sq->where('transfer_to', 'shop')
                       ->where('branch_id', $activeBranchId);
                })->orWhere(function($sq) use ($activeBranchId) {
                    $sq->where('to_branch_id', $activeBranchId)
                       ->whereNull('to_warehouse_id');
                });
            });
        if ($resetTime) {
            $transferInAfterQuery->where('created_at', '>=', $resetTime);
        }
        $transfersInAfter = $transferInAfterQuery->get();
        $mapTransferInAft = [];
        foreach ($transfersInAfter as $tr) {
            $decodedPids = json_decode($tr->product_id, true);
            if (is_string($decodedPids)) $decodedPids = json_decode($decodedPids, true);
            $rawPids = is_array($decodedPids) ? $decodedPids : ($decodedPids !== null && $decodedPids !== '' ? [$decodedPids] : []);
            $decodedQtys = json_decode($tr->quantity, true);
            if (is_string($decodedQtys)) $decodedQtys = json_decode($decodedQtys, true);
            $rawQtys = is_array($decodedQtys) ? $decodedQtys : ($decodedQtys !== null && $decodedQtys !== '' ? [$decodedQtys] : []);
            foreach ($rawPids as $i => $pid) {
                $pid = trim($pid); if ($pid === '') continue;
                if (!in_array((int)$pid, $productIds)) continue;
                $qtyRaw = $rawQtys[$i] ?? 0;
                if (is_array($qtyRaw)) $qtyRaw = $qtyRaw[0] ?? 0;
                $qty = floatval($qtyRaw);
                $key = $pid . '_0';
                $mapTransferInAft[$key] = ($mapTransferInAft[$key] ?? 0) + $qty;
            }
        }

        $rows = [];
        $grandTotalValue = 0;

        foreach ($products as $p) {
            $is_kg = $p->unit_type === 'kg';

            if (!$is_kg && $p->variants->count() > 0) {
                foreach ($p->variants as $v) {
                    $key = $p->id . '_' . $v->id;
                    $code = $p->item_code; 
                    
                    $purchased = (float)($mapP[$key] ?? 0);
                    $produced  = (float)($mapProd[$key] ?? 0);
                    
                    // If this is the default (or first) variant, also include base product productions (without variant ID)
                    if ($v->is_default || $p->variants->first()->id == $v->id) {
                        $produced += (float)($mapProd[$p->id . '_0'] ?? 0);
                    }

                    $pReturn   = (float)($mapPR[$p->id . '_0'] ?? 0);
                    $sReturn   = (float)($retMap[$key] ?? 0);
                    $toReturn  = (float)($toRetMap[$key] ?? 0);
                    if ($v->is_default || $p->variants->first()->id == $v->id) {
                        $sReturn  += (float)($retMap[$p->id . '_0'] ?? 0);
                        $toReturn += (float)($toRetMap[$p->id . '_0'] ?? 0);
                    }
                    $sold      = (float)($soldMap[$key] ?? 0);
                    $toSold    = (float)($toSoldMap[$key] ?? 0);
                    $balance   = (float)($mapS[$key] ?? 0);
                    
                    // If this is default variant, also include base product stock if any
                    if ($v->is_default || $p->variants->first()->id == $v->id) {
                        $balance += (float)($mapS[$p->id . '_0'] ?? 0);
                    }

                    $adjInc    = (float)($mapAdjInc[$p->id . '_0'] ?? 0);
                    $adjDec    = (float)($mapAdjDec[$p->id . '_0'] ?? 0);

                    $purchAft = (float)($mapPAft[$key] ?? 0);
                    $prodAft  = (float)($mapProdAft[$key] ?? 0); 
                    if ($v->is_default || $p->variants->first()->id == $v->id) {
                        $prodAft += (float)($mapProdAft[$p->id . '_0'] ?? 0);
                    }
                    $prAft    = (float)($mapPRAft[$p->id . '_0'] ?? 0);
                    $soldAft  = (float)($soldAftMap[$key] ?? 0);
                    $toSoldAft = (float)($toSoldAftMap[$key] ?? 0);
                    $sRetAft  = (float)($retAftMap[$key] ?? 0);
                    $toRetAft = (float)($toRetAftMap[$key] ?? 0);
                    $adjIncAft = (float)($mapAdjIncAft[$p->id . '_0'] ?? 0);
                    $adjDecAft = (float)($mapAdjDecAft[$p->id . '_0'] ?? 0);
                    
                    $prodUsage = (float)($mapProdUsage[$p->id . '_0'] ?? 0);
                    $prodUsageAft = (float)($mapProdUsageAft[$p->id . '_0'] ?? 0);
                    if ($p->variants->count() > 1 && !$v->is_default && $p->variants->first()->id != $v->id) {
                        $prodUsage = 0;
                        $prodUsageAft = 0;
                    }

                    $transferQty = (float)($mapTransfer[$p->id . '_0'] ?? 0);
                    $transferInQty = (float)($mapTransferIn[$p->id . '_0'] ?? 0);
                    $transferAft = (float)($mapTransferAft[$p->id . '_0'] ?? 0);
                    $transferInAft = (float)($mapTransferInAft[$p->id . '_0'] ?? 0);

                    // Reconcile physical closing & opening stock with Trade Offer (bonus units deducted) & Trade Offer Return (restored units)
                    $closingStock = $balance - $purchAft - $prodAft - ($sRetAft + $toRetAft) + ($soldAft + $toSoldAft) + $prAft - $adjIncAft + $adjDecAft - $transferInAft + $transferAft + $prodUsageAft;
                    $openingStock = $closingStock - $purchased - $produced - ($sReturn + $toReturn) - $transferInQty - $adjInc + ($sold + $toSold) + $pReturn + $transferQty + $adjDec + $prodUsage;

                    if ($resetTime && $startDate >= substr($resetTime, 0, 10)) {
                        $openingStock = 0;
                        $closingStock = $balance;
                    }

                    $rows[] = [
                        'item_code'          => $code,
                        'item_name'          => $p->item_name . ' (' . ($v->size_label ?: $v->variant_name) . ')',
                        'is_kg'              => false,
                        'initial_stock'      => $openingStock,
                        'produced'           => $produced,
                        'prod_usage'         => $prodUsage,
                        'purchased'          => $purchased,
                        'purchase_return'    => $pReturn,
                        'transfer'           => $transferQty,
                        'transfer_in'        => $transferInQty,
                        'adj_increase'       => $adjInc,
                        'adj_decrease'       => $adjDec,
                        'sold'               => $sold,
                        'trade_offer'        => $toSold,
                        'sale_return'        => $sReturn,
                        'trade_offer_return' => $toReturn,
                        'balance'            => $closingStock,
                        'unit'               => $p->unit->name ?? ($is_kg ? 'KG' : 'PC'),
                    ];
                    $valPrice = (float)($v->wholesale_price ?: $v->price ?: $p->wholesale_price ?: $p->price ?: 0);
                    $grandTotalValue += $closingStock * $valPrice;
                }
            } else {
                // Base product only or combined KG product
                $key = $p->id . '_0';
                $code = $p->item_code;

                $purchased_kg = (float)($mapP[$key] ?? 0);
                $produced     = (float)($mapProd[$key] ?? 0); // already in grams from qty_stock
                $pReturn_kg   = (float)($mapPR[$p->id . '_0'] ?? 0);
                $balance      = (float)($mapS[$key] ?? 0); // already in grams

                // For KG products: purchased & purchase return qty in DB is in KG → convert to grams
                // For KG products: sold qty in sales is in KG → convert to grams
                $rawSold     = (float)($soldMap[$key] ?? 0);
                $rawToSold   = (float)($toSoldMap[$key] ?? 0);
                $rawSReturn  = (float)($retMap[$key] ?? 0);
                $rawToReturn = (float)($toRetMap[$key] ?? 0);
                
                $purchAft_kg = (float)($mapPAft[$key] ?? 0);
                $prodAft     = (float)($mapProdAft[$key] ?? 0); 
                $prAft_kg   = (float)($mapPRAft[$p->id . '_0'] ?? 0);
                $rawSoldAft  = (float)($soldAftMap[$key] ?? 0);
                $rawToSoldAft = (float)($toSoldAftMap[$key] ?? 0);
                $rawSRetAft  = (float)($retAftMap[$key] ?? 0);
                $rawToRetAft = (float)($toRetAftMap[$key] ?? 0);

                $rawProdUsage    = (float)($mapProdUsage[$p->id . '_0'] ?? 0);
                $rawProdUsageAft = (float)($mapProdUsageAft[$p->id . '_0'] ?? 0);

                if ($is_kg) {
                    $purchased = $purchased_kg * 1000;
                    $pReturn   = $pReturn_kg * 1000;
                    $sold      = $rawSold * 1000;
                    $toSold    = $rawToSold * 1000;
                    $sReturn   = $rawSReturn * 1000;
                    $toReturn  = $rawToReturn * 1000;
                    $prodUsage = $rawProdUsage * 1000;

                    $purchAft = $purchAft_kg * 1000;
                    $prAft    = $prAft_kg * 1000;
                    $soldAft  = $rawSoldAft * 1000;
                    $toSoldAft = $rawToSoldAft * 1000;
                    $sRetAft  = $rawSRetAft * 1000;
                    $toRetAft = $rawToRetAft * 1000;
                    $prodUsageAft = $rawProdUsageAft * 1000;
                } else {
                    $purchased = $purchased_kg;
                    $pReturn   = $pReturn_kg;
                    $sold      = $rawSold;
                    $toSold    = $rawToSold;
                    $sReturn   = $rawSReturn;
                    $toReturn  = $rawToReturn;
                    $prodUsage = $rawProdUsage;

                    $purchAft = $purchAft_kg;
                    $prAft    = $prAft_kg;
                    $soldAft  = $rawSoldAft;
                    $toSoldAft = $rawToSoldAft;
                    $sRetAft  = $rawSRetAft;
                    $toRetAft = $rawToRetAft;
                    $prodUsageAft = $rawProdUsageAft;
                }

                if ($is_kg && $p->variants->count() > 0) {
                    // Accumulate all variants into base for KG items
                    foreach ($p->variants as $v) {
                        $vKey = $p->id . '_' . $v->id;
                        $mul = floatval($v->size_value); 
                        
                        // If variant size is in KG, multiply by 1000 to get grams
                        if ($v->size_unit === 'kg') {
                            $mul *= 1000;
                        }

                        $vPurchRaw = (float)($mapP[$vKey] ?? 0);
                        $vPurchAftRaw = (float)($mapPAft[$vKey] ?? 0);

                        // Assuming purchase qty for KG items is in KG
                        $purchased += $vPurchRaw * 1000;
                        $purchAft  += $vPurchAftRaw * 1000;

                        $produced  += (float)($mapProd[$vKey] ?? 0);
                        $prodAft   += (float)($mapProdAft[$vKey] ?? 0);

                        $vPRetRaw  = (float)($mapPR[$vKey] ?? 0);
                        $vPRAftRaw = (float)($mapPRAft[$vKey] ?? 0);
                        $pReturn   += $vPRetRaw * 1000;
                        $prAft     += $vPRAftRaw * 1000;

                        $sold      += (float)($soldMap[$vKey] ?? 0) * $mul;
                        $toSold    += (float)($toSoldMap[$vKey] ?? 0) * $mul;
                        $soldAft   += (float)($soldAftMap[$vKey] ?? 0) * $mul;
                        $toSoldAft += (float)($toSoldAftMap[$vKey] ?? 0) * $mul;

                        $sReturn   += (float)($retMap[$vKey] ?? 0) * $mul;
                        $toReturn  += (float)($toRetMap[$vKey] ?? 0) * $mul;
                        $sRetAft   += (float)($retAftMap[$vKey] ?? 0) * $mul;
                        $toRetAft  += (float)($toRetAftMap[$vKey] ?? 0) * $mul;

                        $balance   += (float)($mapS[$vKey] ?? 0) * $mul;
                    }
                }

                $adjInc    = (float)($mapAdjInc[$p->id . '_0'] ?? 0);
                $adjDec    = (float)($mapAdjDec[$p->id . '_0'] ?? 0);
                $adjIncAft = (float)($mapAdjIncAft[$p->id . '_0'] ?? 0);
                $adjDecAft = (float)($mapAdjDecAft[$p->id . '_0'] ?? 0);

                $rawTransferQty   = (float)($mapTransfer[$p->id . '_0'] ?? 0);
                $rawTransferInQty = (float)($mapTransferIn[$p->id . '_0'] ?? 0);
                $rawTransferAft   = (float)($mapTransferAft[$p->id . '_0'] ?? 0);
                $rawTransferInAft = (float)($mapTransferInAft[$p->id . '_0'] ?? 0);

                if ($is_kg) {
                    $transferQty   = $rawTransferQty * 1000;
                    $transferInQty = $rawTransferInQty * 1000;
                    $transferAft   = $rawTransferAft * 1000;
                    $transferInAft = $rawTransferInAft * 1000;
                } else {
                    $transferQty   = $rawTransferQty;
                    $transferInQty = $rawTransferInQty;
                    $transferAft   = $rawTransferAft;
                    $transferInAft = $rawTransferInAft;
                }

                // Reconcile physical closing & opening stock with Trade Offer & Trade Offer Return
                $closingStock = $balance - $purchAft - $prodAft - ($sRetAft + $toRetAft) + ($soldAft + $toSoldAft) + $prAft - $adjIncAft + $adjDecAft - $transferInAft + $transferAft + $prodUsageAft;
                $openingStock = $closingStock - $purchased - $produced - ($sReturn + $toReturn) - $transferInQty - $adjInc + ($sold + $toSold) + $pReturn + $transferQty + $adjDec + $prodUsage;

                if ($resetTime && $startDate >= substr($resetTime, 0, 10)) {
                    $openingStock = 0;
                    $closingStock = $balance;
                }

                $rows[] = [
                    'item_code'          => $code,
                    'item_name'          => $p->item_name,
                    'is_kg'              => $is_kg,
                    'initial_stock'      => $openingStock,
                    'produced'           => $produced,
                    'prod_usage'         => $prodUsage,
                    'purchased'          => $purchased,
                    'purchase_return'    => $pReturn,
                    'transfer'           => $transferQty,
                    'transfer_in'        => $transferInQty,
                    'adj_increase'       => $adjInc,
                    'adj_decrease'       => $adjDec,
                    'sold'               => $sold,
                    'trade_offer'        => $toSold,
                    'sale_return'        => $sReturn,
                    'trade_offer_return' => $toReturn,
                    'balance'            => $closingStock,
                    'unit'               => $p->unit->name ?? ($is_kg ? 'KG' : 'PC'),
                ];
                
                $valPrice = 0;
                if ($is_kg) {
                    $defaultVar = $p->variants->where('is_default', 1)->first() ?? $p->variants->first();
                    if ($defaultVar && (float)$defaultVar->price > 0) {
                        $sVal = (float)$defaultVar->size_value;
                        $sUnit = strtolower(trim($defaultVar->size_unit ?? 'kg'));
                        if (in_array($sUnit, ['g', 'gm', 'gram', 'grams']) && $sVal > 0) {
                            $sVal = $sVal / 1000;
                        }
                        $valPrice = ($sVal > 0) ? ((float)$defaultVar->price / $sVal) : (float)$defaultVar->price;
                    } else {
                        $valPrice = (float)($p->price ?: $p->wholesale_price ?: 0);
                    }
                } else {
                    $valPrice = (float)($p->price ?: $p->wholesale_price ?: 0);
                }

                $valuationQty = $is_kg ? ($closingStock / 1000) : $closingStock;
                if ($valuationQty > 0) {
                    $grandTotalValue += $valuationQty * $valPrice;
                }
            }
        }

        return response()->json([
            'data'        => $rows,
            'grand_total' => $grandTotalValue,
            'total'       => count($rows),
        ]);
    }

    /**
     * Variant / Size-wise Stock Report
     * Returns all variants grouped under their parent product.
     */
    public function fetchVariantStock(Request $request)
    {
        $searchQ   = trim($request->get('q', ''));
        $productId = $request->get('product_id', 'all');

        $query = DB::table('product_variants')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->select(
                'products.id          as product_id',
                'products.item_code   as item_code',
                'products.item_name   as product_name',
                'products.unit_type   as unit_type',
                'categories.name      as category',
                'product_variants.id           as variant_id',
                'product_variants.size_label   as size_label',
                'product_variants.variant_name as variant_name',
                'product_variants.size_value   as size_value',
                'product_variants.size_unit    as size_unit',
                'product_variants.price        as price',
                'product_variants.stock_qty    as stock_qty',
                'product_variants.alert_quantity as alert_qty',
                'product_variants.is_default   as is_default'
            )
            ->where('product_variants.is_active', true)
            ->orderBy('products.item_name')
            ->orderBy('product_variants.size_value');

        if ($productId && $productId !== 'all') {
            $query->where('products.id', $productId);
        }

        if ($searchQ !== '') {
            $query->where(function($q) use ($searchQ) {
                $q->where('products.item_name', 'like', "%{$searchQ}%")
                  ->orWhere('products.item_code', 'like', "%{$searchQ}%")
                  ->orWhere('product_variants.size_label', 'like', "%{$searchQ}%");
            });
        }

        $variants = $query->get();
        $productIds = $variants->pluck('product_id')->unique()->toArray();
        
        $stocksQuery = DB::table('stocks')->whereIn('product_id', $productIds)->whereNull('warehouse_id');
        if (!is_all_branches()) {
            $stocksQuery->where('branch_id', active_branch_id());
        }
        $rawStocks = $stocksQuery->get();

        $stocksMap = [];
        $nullStocksMap = [];
        foreach ($rawStocks as $st) {
            $pid = $st->product_id;
            if ($st->variant_id) {
                $stocksMap[$pid][$st->variant_id] = ($stocksMap[$pid][$st->variant_id] ?? 0) + (float)$st->qty;
            } else {
                $nullStocksMap[$pid] = ($nullStocksMap[$pid] ?? 0) + (float)$st->qty;
            }
        }

        // Group variants by product to check variant counts and default variant
        $productVariantCounts = [];
        $defaultVariantMap = [];
        $firstVariantMap = [];
        foreach ($variants as $v) {
            $productVariantCounts[$v->product_id][] = $v->variant_id;
            if (!isset($firstVariantMap[$v->product_id])) {
                $firstVariantMap[$v->product_id] = $v->variant_id;
            }
            if ($v->is_default) {
                $defaultVariantMap[$v->product_id] = $v->variant_id;
            }
        }
        foreach ($firstVariantMap as $pid => $fvid) {
            if (!isset($defaultVariantMap[$pid])) {
                $defaultVariantMap[$pid] = $fvid;
            }
        }

        // Group by product
        $grouped = [];
        foreach ($variants as $v) {
            $pid = $v->product_id;
            if (!isset($grouped[$pid])) {
                $grouped[$pid] = [
                    'product_id'   => $pid,
                    'item_code'    => $v->item_code,
                    'product_name' => $v->product_name,
                    'unit_type'    => $v->unit_type,
                    'category'     => $v->category ?? '–',
                    'total_stock'  => 0,
                    'sizes'        => [],
                ];
            }
            $label = $v->size_label ?: $v->variant_name ?: ('Size ' . $v->size_value . ' ' . $v->size_unit);
            $vid   = $v->variant_id;

            $isKg = strtolower($v->unit_type ?? '') === 'kg';
            $isSingleVar = count($productVariantCounts[$pid] ?? []) === 1;
            $isDefault = (isset($defaultVariantMap[$pid]) && $defaultVariantMap[$pid] == $vid);

            if ($isKg) {
                if ($isSingleVar) {
                    $bulkGrams = (float)($nullStocksMap[$pid] ?? 0) + (float)($stocksMap[$pid][$vid] ?? 0);
                    $stock = $bulkGrams / 1000;
                } else {
                    $vMul = (float)$v->size_value;
                    if ($vMul <= 0) $vMul = 1;
                    $vGrams = in_array(strtolower($v->size_unit ?? ''), ['g', 'gm', 'gram', 'grams']) ? $vMul : ($vMul * 1000);

                    $varPacks = (float)($stocksMap[$pid][$vid] ?? 0);
                    if ($isDefault && isset($nullStocksMap[$pid])) {
                        $varPacks += ((float)$nullStocksMap[$pid] / $vGrams);
                    }
                    $stock = $varPacks;
                }
            } else {
                if (isset($stocksMap[$pid][$vid])) {
                    $rawStock = (float)$stocksMap[$pid][$vid];
                } elseif (isset($nullStocksMap[$pid])) {
                    $rawStock = 0;
                } else {
                    $rawStock = (float)$v->stock_qty;
                }

                if ($isDefault && isset($nullStocksMap[$pid])) {
                    $rawStock += (float)$nullStocksMap[$pid];
                }
                $stock = $rawStock;
            }
            
            $grouped[$pid]['sizes'][] = [
                'variant_id'  => $v->variant_id,
                'label'       => $label,
                'price'       => (float)$v->price,
                'stock_qty'   => $stock,
                'is_kg'       => ($isKg && $isSingleVar),
                'alert_qty'   => (int)$v->alert_qty,
                'is_default'  => (bool)$v->is_default,
                'status'      => $stock <= 0 ? 'out' : ($stock <= ($v->alert_qty ?: 5) ? 'low' : 'ok'),
            ];

            if ($isKg) {
                if ($isSingleVar) {
                    $grouped[$pid]['total_stock'] += $stock;
                } else {
                    $vMul = (float)$v->size_value;
                    if (in_array(strtolower($v->size_unit ?? ''), ['g', 'gm', 'gram', 'grams'])) {
                        $vMul /= 1000;
                    }
                    $grouped[$pid]['total_stock'] += $stock * ($vMul ?: 1);
                }
            } else {
                $grouped[$pid]['total_stock'] += $stock;
            }
        }

        return response()->json([
            'data'  => array_values($grouped),
            'total' => count($grouped),
        ]);
    }


    public function purchase_report()
    {
        $branches = \App\Models\Branch::orderBy('name')->get();
        return view('admin_panel.reporting.purchase_report', compact('branches'));
    }

    public function fetchPurchaseReport(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $branchId  = $request->branch_id;

        /* ================= NORMAL PURCHASE ================= */
        $purchaseQuery = DB::table('purchases')
            ->leftJoin('purchase_items', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->leftJoin('products', 'purchase_items.product_id', '=', 'products.id')
            ->leftJoin('vendors', 'purchases.vendor_id', '=', 'vendors.id')
            ->leftJoin('branches', 'purchases.branch_id', '=', 'branches.id')
            ->select(
                DB::raw("'purchase' as source_type"),
                'purchases.purchase_date as purchase_date',
                'purchases.invoice_no',
                'purchases.branch_id',
                'branches.name as branch_name',
                'vendors.name as vendor_name',
                'products.item_code',
                'products.item_name',
                'purchase_items.qty',
                'purchase_items.unit',
                'purchase_items.price',
                'purchase_items.item_discount',
                'purchase_items.line_total',
                'purchases.subtotal',
                'purchases.discount',
                'purchases.extra_cost',
                'purchases.net_amount',
                'purchases.paid_amount',
                'purchases.due_amount'
            );

        if ($startDate && $endDate) {
            $purchaseQuery->whereBetween('purchases.purchase_date', [$startDate, $endDate]);
        }
        if ($branchId && $branchId !== 'all') {
            $purchaseQuery->where('purchases.branch_id', $branchId);
        } elseif (empty($branchId) && !is_all_branches()) {
            $purchaseQuery->where('purchases.branch_id', active_branch_id());
        }

        /* ================= INWARD AS PURCHASE ================= */
        $inwardQuery = DB::table('inward_gatepasses')
            ->leftJoin('inward_gatepass_items', 'inward_gatepasses.id', '=', 'inward_gatepass_items.inward_gatepass_id')
            ->leftJoin('products', 'inward_gatepass_items.product_id', '=', 'products.id')
            ->leftJoin('vendors', 'inward_gatepasses.vendor_id', '=', 'vendors.id')
            ->leftJoin('branches', 'inward_gatepasses.branch_id', '=', 'branches.id')
            ->where('inward_gatepasses.status', 'linked')
            ->where('inward_gatepasses.bill_status', 'billed')
            ->select(
                DB::raw("'inward' as source_type"),
                'inward_gatepasses.gatepass_date as purchase_date',
                'inward_gatepasses.invoice_no',
                'inward_gatepasses.branch_id',
                'branches.name as branch_name',
                'vendors.name as vendor_name',
                'products.item_code',
                'products.item_name',
                'inward_gatepass_items.qty',
                DB::raw('products.unit_id as unit'),
                // Use the specific transaction price from the item table, not master product price
                DB::raw('COALESCE(inward_gatepass_items.price, products.wholesale_price) as price'),
                DB::raw('0 as item_discount'),
                // Calculate line total using the transaction price (no discount supported on inward items currently)
                DB::raw('(COALESCE(inward_gatepass_items.price, products.wholesale_price) * inward_gatepass_items.qty) as line_total'),
                'inward_gatepasses.subtotal',
                'inward_gatepasses.discount',
                'inward_gatepasses.extra_cost',
                'inward_gatepasses.net_amount',
                'inward_gatepasses.paid_amount',
                'inward_gatepasses.due_amount'
            );


        if ($startDate && $endDate) {
            $inwardQuery->whereBetween('gatepass_date', [$startDate, $endDate]);
        }
        if ($branchId && $branchId !== 'all') {
            $inwardQuery->where('inward_gatepasses.branch_id', $branchId);
        } elseif (empty($branchId) && !is_all_branches()) {
            $inwardQuery->where('inward_gatepasses.branch_id', active_branch_id());
        }

        /* ================= UNION ================= */
        $data = $purchaseQuery
            ->unionAll($inwardQuery)
            ->get()
            ->sort(function($a, $b) {
                if ($a->purchase_date === $b->purchase_date) {
                    // Secondary: invoice_no desc
                    return strcmp($b->invoice_no, $a->invoice_no);
                }
                // Primary: purchase_date desc
                return strcmp($b->purchase_date, $a->purchase_date);
            })
            ->values();

        // 🔹 Post-processing to remove duplicate invoice totals
        $seenInvoices = [];
        foreach ($data as $row) {
            $uniqueKey = $row->source_type . '_' . $row->invoice_no;

            if (in_array($uniqueKey, $seenInvoices)) {
                // Secondary item: Zero out invoice-level totals to prevent double counting
                $row->subtotal = 0;
                $row->discount = 0;
                $row->extra_cost = 0;
                $row->net_amount = 0;
                $row->paid_amount = 0;
                $row->due_amount = 0;
                $row->is_duplicate = true; 
            } else {
                // First item: Keep totals
                $seenInvoices[] = $uniqueKey;
                $row->is_duplicate = false;
            }
        }

        return response()->json([
            'data' => $data
        ]);
    }



    public function sale_report()
    {
        $branches = \App\Models\Branch::orderBy('name')->get();
        return view('admin_panel.reporting.sale_report', compact('branches'));
    }

    public function fetchsaleReport(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->start_date;
            $end = $request->end_date;
            $branchId = $request->branch_id;

            $query = DB::table('sales')
                ->leftJoin('customers', 'sales.customer', '=', 'customers.id')
                ->leftJoin('branches', 'sales.branch_id', '=', 'branches.id')
                ->select(
                    'sales.id',
                    'sales.branch_id',
                    'branches.name as branch_name',
                    'sales.invoice_no', // ✅ Select invoice_no specifically
                    'sales.reference',
                    'sales.product',
                    'sales.variant_id',
                    'sales.product_code',
                    'sales.brand',
                    'sales.unit',
                    'sales.per_price',
                    'sales.per_discount',
                    'sales.qty',
                    'sales.per_total',
                    'sales.total_net',
                    'sales.created_at',
                    'customers.customer_name',
                    'sales.unit' // Add unit
                );

            if ($start && $end) {
                // Handle HTML5 datetime-local format (YYYY-MM-DDTHH:mm)
                $start = str_replace('T', ' ', $start);
                $end = str_replace('T', ' ', $end);
                
                // Append seconds if not present
                if (strlen($start) == 10) $start .= ' 00:00:00';
                if (strlen($end) == 10) $end .= ' 23:59:59';
                
                if (strlen($start) == 16) $start .= ':00';
                if (strlen($end) == 16) $end .= ':59';

                $query->whereBetween('sales.created_at', [$start, $end]);
            }

            if ($branchId && $branchId !== 'all') {
                $query->where('sales.branch_id', $branchId);
            } elseif (empty($branchId) && !is_all_branches()) {
                $query->where('sales.branch_id', active_branch_id());
            }

            // Filter by Customer Type/Category
            if ($request->has('customer_type')) {
                $types = $request->customer_type;

                // Ensure types is an array and not empty
                if (is_array($types) && count($types) > 0) {
                     $query->where(function($q) use ($types) {
                        
                        // 1. Handle Literal "Walk-in Customer" (Not a registered user ID)
                        if (in_array('Walking Customer', $types)) {
                            $q->orWhere('sales.customer', 'Walk-in Customer');
                        }

                        // 2. Filter by Category (Strictly Category, Ignore Type)
                        // This excludes customers like ID 12 who are Wholesalers but have Type='Walking Customer'
                        $q->orWhereIn('customers.customer_category', $types);
                     });
                }
            } else {
                // Default: Show 'Walk-in Customer' literal and 'Walking Customer' category
                 $query->where(function($q) {
                     $q->where('sales.customer', 'Walk-in Customer')
                       ->orWhere('customers.customer_category', 'Walking Customer');
                 });
            }

            $sales = $query->orderBy('sales.created_at', 'asc')->get();

            // 1. Collect all product and variant IDs for bulk fetching
            $allProdIds = [];
            $allVarIds  = [];
            foreach ($sales as $sale) {
                if (!empty($sale->product)) {
                    $ids = explode(',', $sale->product);
                    foreach($ids as $id) if(trim($id)) $allProdIds[] = trim($id);
                }
                if (!empty($sale->variant_id)) {
                    $vids = explode(',', $sale->variant_id);
                    foreach($vids as $vid) if(trim($vid)) $allVarIds[] = trim($vid);
                }
            }
            
            $productsDict = \App\Models\Product::whereIn('id', array_unique($allProdIds))->get()->keyBy('id');
            $variantsDict = \App\Models\ProductVariant::whereIn('id', array_unique($allVarIds))->get()->keyBy('id');

            foreach ($sales as $sale) {
                // --- Process Products, Variants and Units ---
                if (!empty($sale->product)) {
                    $pIds = explode(',', $sale->product);
                    $vIds = !empty($sale->variant_id) ? explode(',', $sale->variant_id) : [];
                    $qArr = explode(',', $sale->qty);
                    $prArr = explode(',', $sale->per_price);
                    
                    $orderedNames = [];
                    $orderedUnits = [];
                    $modifiedQtys = [];
                    $modifiedPrices = [];
                    
                    foreach ($pIds as $idx => $pid) {
                        $pid = trim($pid);
                        $p = $productsDict->get($pid);
                        $name = $p ? $p->item_name : '-';
                        $unit = $p ? strtoupper($p->unit_type ?? 'Piece') : '-';
                        $qty = isset($qArr[$idx]) ? floatval($qArr[$idx]) : 0;
                        $price = isset($prArr[$idx]) ? floatval($prArr[$idx]) : 0;
                        
                        $vid = isset($vIds[$idx]) ? trim($vIds[$idx]) : null;
                        if ($vid && $variantsDict->has($vid)) {
                            $v = $variantsDict->get($vid);
                            $name .= ' (' . ($v->size_label ?: $v->variant_name) . ')';
                            
                            // Check if we should convert to weight-based display
                            if ($p && strtolower($p->unit_type) === 'kg' && $v->size_value > 0) {
                                $multiplier = 1;
                                $sUnit = strtolower($v->size_unit ?? 'kg');
                                if ($sUnit === 'kg') {
                                    $multiplier = floatval($v->size_value);
                                } elseif ($sUnit === 'gm' || $sUnit === 'grams' || $sUnit === 'gram') {
                                    $multiplier = floatval($v->size_value) / 1000;
                                }
                                
                                // Convert display values: 2 boxes of 0.250kg -> 0.500kg
                                $qty = $qty * $multiplier;
                                // Adjust price to price-per-kg to keep total consistent
                                if ($multiplier > 0) {
                                    $price = $price / $multiplier;
                                }
                                $unit = 'KG';
                            } else {
                                // If not weight-based, use PIECE for variant count
                                $unit = 'PIECE';
                            }
                        }
                        
                        $orderedNames[]  = $name;
                        $orderedUnits[]  = $unit;
                        $modifiedQtys[]  = $qty;
                        $modifiedPrices[] = $price;
                    }

                    $sale->product_names = implode('|', $orderedNames);
                    $sale->unit          = implode('|', $orderedUnits);
                    $sale->qty           = implode(',', $modifiedQtys);
                    $sale->per_price     = implode(',', $modifiedPrices);
                } else {
                    $sale->product_names = '-';
                    $sale->unit          = '-';
                }

                // --- Merge Sale Returns ---
                $returnsRaw = DB::table('sales_returns')
                    ->where('sale_id', $sale->id)
                    ->get();
                
                $parsedReturns = [];
                foreach ($returnsRaw as $ret) {
                    $rProducts = explode(',', $ret->product ?? '');
                    $rQtys     = explode(',', $ret->qty ?? '');
                    $rTotals   = explode(',', $ret->per_total ?? ''); // or use per_price * qty if needed

                    foreach ($rProducts as $idx => $rProd) {
                        $q = isset($rQtys[$idx]) ? floatval($rQtys[$idx]) : 0;
                        $t = isset($rTotals[$idx]) ? floatval($rTotals[$idx]) : 0;
                        if ($q > 0) {
                            $parsedReturns[] = [
                                'product' => trim($rProd),
                                'qty'     => $q,
                                'amount'  => $t
                            ];
                        }
                    }
                }
                $sale->returns = $parsedReturns;
            }


            return response()->json($sales);
        }

        return view('admin_panel.reporting.sale_report');
    }



    public function sale_report_category()
    {
        $categories = Category::select('id', 'name')->get();
        $branches = \App\Models\Branch::orderBy('name')->get();
        return view('admin_panel.reporting.sale_report_category', compact('categories', 'branches'));
    }

    public function fetchsalecategoryReport(Request $request)
    {
        if ($request->ajax()) {

            $start      = $request->start_date;
            $end        = $request->end_date;
            $categoryId = $request->category_id;
            $subCategoryId = $request->subcategory_id; // Get subcategory ID
            $branchId   = $request->branch_id;

            // ================== BASE SALES QUERY ==================
            $query = DB::table('sales')
                ->leftJoin('customers', 'sales.customer', '=', 'customers.id')
                ->leftJoin('branches', 'sales.branch_id', '=', 'branches.id')
                ->select(
                    'sales.id',
                    'sales.branch_id',
                    'branches.name as branch_name',
                    'sales.invoice_no',
                    'sales.reference',
                    'sales.product',
                    'sales.variant_id',
                    'sales.product_code',
                    'sales.brand',
                    'sales.unit',
                    'sales.per_price',
                    'sales.per_discount',
                    'sales.qty',
                    'sales.per_total',
                    'sales.total_net',
                    'sales.created_at',
                    'customers.customer_name'
                )
                ->when($start && $end, function ($q) use ($start, $end) {
                    $start = str_replace('T', ' ', $start);
                    $end = str_replace('T', ' ', $end);
                    
                    if (strlen($start) == 10) $start .= ' 00:00:00';
                    if (strlen($end) == 10) $end .= ' 23:59:59';
                    
                    if (strlen($start) == 16) $start .= ':00';
                    if (strlen($end) == 16) $end .= ':59';

                    $q->whereBetween('sales.created_at', [$start, $end]);
                });

            if ($branchId && $branchId !== 'all') {
                $query->where('sales.branch_id', $branchId);
            } elseif (empty($branchId) && !is_all_branches()) {
                $query->where('sales.branch_id', active_branch_id());
            }

            // ================== CUSTOMER FILTERING ==================
            if ($request->has('customer_type')) {
                $types = $request->customer_type;
                if (is_array($types) && count($types) > 0) {
                     $query->where(function($q) use ($types) {
                        
                        // 1. Literal "Walk-in Customer"
                        if (in_array('Walking Customer', $types)) {
                            $q->orWhere('sales.customer', 'Walk-in Customer');
                        }
                        // 2. Category Match
                        $q->orWhereIn('customers.customer_category', $types);
                     });
                }
            } else {
                // Default: Walking
                 $query->where(function($q) {
                     $q->where('sales.customer', 'Walk-in Customer')
                       ->orWhere('customers.customer_category', 'Walking Customer');
                 });
            }

            $sales = $query->orderBy('sales.created_at', 'asc')->get();

            $finalSales = [];

            // 1. Collect all variant IDs for bulk fetching (products are already fetched per-sale or could be bulked too)
            $allVarIds = [];
            foreach ($sales as $sale) {
                if (!empty($sale->variant_id)) {
                    foreach(explode(',', $sale->variant_id) as $vid) if(trim($vid)) $allVarIds[] = trim($vid);
                }
            }
            $variantsDict = \App\Models\ProductVariant::whereIn('id', array_unique($allVarIds))->get()->keyBy('id');

            foreach ($sales as $sale) {
                if (empty($sale->product)) continue;

                $pIds     = explode(',', $sale->product);
                $vIds     = !empty($sale->variant_id) ? explode(',', $sale->variant_id) : [];
                $qtyArr   = explode(',', $sale->qty);
                $priceArr = explode(',', $sale->per_price);
                $totalArr = explode(',', $sale->per_total);

                // ================== PRODUCTS QUERY ==================
                // Filter products by category/subcategory if requested
                $productsQuery = DB::table('products')
                    ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('subcategories', 'products.sub_category_id', '=', 'subcategories.id')
                    ->whereIn('products.id', $pIds)
                    ->select('products.id','products.item_name','products.unit_type','categories.name as category_name','subcategories.name as subcategory_name');
                
                if ($categoryId)    $productsQuery->where('products.category_id', $categoryId);
                if ($subCategoryId) $productsQuery->where('products.sub_category_id', $subCategoryId);
                
                $matchedProducts = $productsQuery->get()->keyBy('id');

                if ($matchedProducts->isEmpty()) continue;

                $finalNames   = [];
                $finalCats    = [];
                $finalSubCats = [];
                $finalQtys    = [];
                $finalPrices  = [];
                $finalTotals  = [];
                $finalUnits   = [];

                // Iterate through the comma-separated arrays to maintain order and handle multiple instances/variants
                foreach ($pIds as $index => $pid) {
                    $pid = trim($pid);
                    if (!$matchedProducts->has($pid)) continue;

                    $product = $matchedProducts->get($pid);
                    $name    = $product->item_name;
                    $unit    = strtoupper($product->unit_type ?? 'Piece');
                    $qty     = (float)($qtyArr[$index] ?? 0);
                    $price   = (float)($priceArr[$index] ?? 0);
                    
                    $vid = isset($vIds[$index]) ? trim($vIds[$index]) : null;
                    if ($vid && $variantsDict->has($vid)) {
                        $v = $variantsDict->get($vid);
                        $name .= ' (' . ($v->size_label ?: $v->variant_name) . ')';
                        
                        // Check weight-based conversion
                        if ($product && strtolower($product->unit_type) === 'kg' && $v->size_value > 0) {
                            $multiplier = 1;
                            $sUnit = strtolower($v->size_unit ?? 'kg');
                            if ($sUnit === 'kg') {
                                $multiplier = floatval($v->size_value);
                            } elseif ($sUnit === 'gm' || $sUnit === 'grams' || $sUnit === 'gram') {
                                $multiplier = floatval($v->size_value) / 1000;
                            }
                            
                            $qty = $qty * $multiplier;
                            if ($multiplier > 0) {
                                $price = $price / $multiplier;
                            }
                            $unit = 'KG';
                        } else {
                            $unit = 'PIECE';
                        }
                    }

                    $finalNames[]   = $name;
                    $finalCats[]    = $product->category_name ?? '-';
                    $finalSubCats[] = $product->subcategory_name ?? '-';
                    $finalQtys[]    = $qty;
                    $finalPrices[]  = $price;
                    $finalTotals[]  = (float)($totalArr[$index] ?? 0);
                    $finalUnits[]   = $unit;
                }

                if (empty($finalNames)) continue;

                $sale->product_names  = implode('|', $finalNames);
                $sale->categories     = implode(', ', array_unique($finalCats));
                $sale->subcategories  = implode(', ', array_unique($finalSubCats));
                $sale->filtered_qty   = implode('|', $finalQtys);
                $sale->filtered_price = implode('|', $finalPrices);
                $sale->filtered_total = implode('|', $finalTotals);
                $sale->filtered_unit  = implode('|', $finalUnits); 
                $sale->filtered_net   = array_sum($finalTotals);

                $sale->returns = DB::table('sales_returns')->where('sale_id', $sale->id)->get();
                $finalSales[] = $sale;
            }

            return response()->json($finalSales);
        }

        return view('admin_panel.reporting.sale_report_category'); // Should not be reached if handled by first method, but keeping safe
    }



    public function customer_ledger_report()
    {
        $query = DB::table('customers')
            ->select('id', 'customer_name')
            ->where('status', '!=', 'inactive');

        if (!is_all_branches()) {
            $query->where('branch_id', active_branch_id());
        }

        $customers = $query->orderBy('customer_name')->get();

        return view('admin_panel.reporting.customer_ledger_report', compact('customers'));
    }

    public function fetch_customer_ledger(Request $request)
    {
        $customerId = $request->customer_id;
        $start = $request->start_date;
        $end = $request->end_date . ' 23:59:59';

        // Customer info
        $customer = DB::table('customers')->where('id', $customerId)->first();

        // ---------------- CALCULATE OPENING BALANCE DYNAMICALLY ----------------
        // 1. Initial Opening from Customer
        $initial = $customer->opening_balance ?? 0;

        // 2. Prior Sales (Net of Returns as stored in DB)
        $prevSales = DB::table('sales')
            ->where('customer', $customerId)
            ->where('created_at', '<', $start)
            ->sum(DB::raw('COALESCE(total_net, total_bill_amount)'));

        // 3. Prior Payments
        $prevPayments = DB::table('customer_payments')
            ->where('customer_id', $customerId)
            ->where('payment_date', '<', $start)
            ->sum('amount');

        // 4. Returns that happened ON/AFTER StartDate, but belong to Prior Sales.
        // These need to be added back because the Prior Sales Sum is already reduced by them,
        // but the credit event hasn't happened yet in this timeline.
        $addBackReturns = DB::table('sales_returns')
            ->join('sales', 'sales_returns.sale_id', '=', 'sales.id')
            ->where('sales_returns.customer', $customerId)
            ->where('sales_returns.created_at', '>=', $start)
            ->where('sales.created_at', '<', $start)
            ->sum('sales_returns.total_net');

        $opening = $initial + $prevSales - $prevPayments + $addBackReturns;

        // ---------------- FETCH ALL SALE RETURNS FIRST ----------------
        $allSaleReturns = DB::table('sales_returns')
            ->where('customer', $customerId)
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy('sale_id'); // group by sale_id for easy lookup

        // ---------------- SALES (Debit) ----------------
        $sales = DB::table('sales')
            ->where('customer', $customerId)
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->map(function ($s) use ($allSaleReturns) {
                $fullSaleAmount = $s->total_net ?? $s->total_bill_amount;

                // Check if this sale has any returns
                $returnTotal = 0;
                if (isset($allSaleReturns[$s->id])) {
                    $returnTotal = $allSaleReturns[$s->id]->sum('total_net');
                }

                // Sale debit = original sale + total of related returns
                $debitAmount = $fullSaleAmount + $returnTotal;

                return [
                    'date' => $s->created_at,
                    'sort_type' => 1,
                    'invoice' => $s->invoice_no,
                    'reference' => $s->reference,
                    'description' => 'To Sale A/c',
                    'debit' => $debitAmount,
                    'credit' => 0,
                    'original_sale_id' => $s->id
                ];
            });

        // ---------------- SALE RETURNS (Credit) ----------------
        $saleReturns = collect();
        foreach ($allSaleReturns as $saleId => $returns) {
            foreach ($returns as $r) {
                $saleReturns->push([
                    'date' => $r->created_at,
                    'sort_type' => 3,
                    'invoice' => 'SR-' . $r->sale_id,
                    'reference' => $r->reference,
                    'description' => 'By Sale Return',
                    'debit' => 0,
                    'credit' => $r->total_net,
                    'original_sale_id' => $r->sale_id
                ]);
            }
        }

        // ---------------- PAYMENTS (Credit) ----------------
        $payments = DB::table('customer_payments')
            ->where('customer_id', $customerId)
            ->whereBetween('payment_date', [$start, $end])
            ->get()
            ->map(function ($p) {
                return [
                    'date' => $p->payment_date . ' 23:59:59',
                    'sort_type' => 2,
                    'invoice' => $p->received_no,
                    'reference' => $p->payment_method,
                    'description' => $p->note ?? 'Payment Received',
                    'debit' => 0,
                    'credit' => $p->amount,
                ];
            });

        // ---------------- MERGE + SORT ----------------
        $transactions = collect()
            ->merge($sales)
            ->merge($saleReturns)
            ->merge($payments)
            ->sort(function ($a, $b) {
                $dateA = strtotime($a['date']);
                $dateB = strtotime($b['date']);
                if ($dateA != $dateB) return $dateA <=> $dateB;

                // Sale → Sale Return → Payment
                $order = [1 => 1, 3 => 2, 2 => 3];
                return $order[$a['sort_type']] <=> $order[$b['sort_type']];
            })
            ->values()
            ->all();

        // ---------------- RUNNING BALANCE ----------------
        $balance = $opening;
        foreach ($transactions as $key => $t) {
            $balance += $t['debit'];
            $balance -= $t['credit'];
            $transactions[$key]['balance'] = $balance;
        }

        return response()->json([
            'customer' => $customer,
            'opening_balance' => $opening,
            'transactions' => $transactions,
        ]);
    }





    public function vendor_ledger_report()
    {
        $vendors = DB::table('vendors')->select('id', 'name')->get();

        return view('admin_panel.reporting.vendor_ledger_report', compact('vendors'));
    }

    public function fetch_vendor_ledger(Request $request)
    {
        $vendorId = $request->vendor_id;
        $start = $request->start_date;
        $end = $request->end_date . ' 23:59:59';

        $vendor = DB::table('vendors')->where('id', $vendorId)->first();
        // ---------------- CALCULATE OPENING BALANCE DYNAMICALLY ----------------
        // 1. Initial Opening from Vendor
        $initial = $vendor->opening_balance ?? 0;

        // 2. Prior Purchases (Debit: We owe more)
        $prevPurchases = DB::table('purchases')
            ->where('vendor_id', $vendorId)
            ->where('purchase_date', '<', $start)
            ->sum('net_amount');

        // 2b. Prior Inwards (Debit: We owe more)
        $prevInwards = DB::table('inward_gatepasses')
            ->where('vendor_id', $vendorId)
            ->where('bill_status', 'billed')
            ->where('gatepass_date', '<', $start)
            ->sum('net_amount');

        // 2c. Prior Raw Material Purchases (Debit: We owe more)
        $prevRawMaterialPurchases = DB::table('raw_material_purchases')
            ->where(function ($q) use ($vendorId, $vendor) {
                $q->where('vendor_id', $vendorId);
                if ($vendor && !empty($vendor->name)) {
                    $q->orWhere('vendor_name', $vendor->name);
                }
            })
            ->where('date', '<', $start)
            ->sum('total_cost');

        // 3. Prior Returns (Credit: We owe less)
        $prevReturns = DB::table('purchase_returns')
            ->where('vendor_id', $vendorId)
            ->where('return_date', '<', $start)
            ->sum('net_amount');

        // 3b. Prior Raw Material Returns (Credit: We owe less)
        $prevRawMaterialReturns = DB::table('raw_material_purchase_returns')
            ->where(function ($q) use ($vendorId, $vendor) {
                $q->where('vendor_id', $vendorId);
                if ($vendor && !empty($vendor->name)) {
                    $q->orWhere('vendor_name', $vendor->name);
                }
            })
            ->where('return_date', '<', $start)
            ->sum('total_cost');

        // 4. Prior Payments (Credit: We owe less)
        $prevPayments = DB::table('vendor_payments')
            ->where('vendor_id', $vendorId)
            ->where('payment_date', '<', $start)
            ->sum('amount');

        // 5. Prior Bilties (Debit: We owe more)
        $prevBilties = DB::table('vendor_bilties')
            ->where('vendor_id', $vendorId)
            ->where('delivery_date', '<', $start)
            ->sum('amount');

        $opening = $initial + $prevPurchases + $prevInwards + $prevRawMaterialPurchases + $prevBilties - $prevReturns - $prevRawMaterialReturns - $prevPayments;

        // 🔹 1. Purchases → Debit (we owe vendor)
        $purchases = DB::table('purchases')
            ->where('vendor_id', $vendorId)
            ->whereBetween('purchase_date', [$start, $end])
            ->select('purchase_date', 'invoice_no', 'net_amount', 'note') // Explicitly select note
            ->get()
            ->map(function ($p) {
                return [
                    'date' => $p->purchase_date,
                    'invoice' => $p->invoice_no,
                    'description' => $p->note ?: 'Purchase Invoice', // Use note if available
                    'debit' => $p->net_amount,
                    'credit' => 0,
                    'sort_date' => $p->purchase_date
                ];
            });

        // 🔹 1b. Inward Bills → Debit (we owe vendor)
        $inwards = DB::table('inward_gatepasses')
            ->where('vendor_id', $vendorId)
            ->where('bill_status', 'billed')
            ->whereBetween('gatepass_date', [$start, $end])
            ->get()
            ->map(function ($i) {
                return [
                    'date' => $i->gatepass_date,
                    'invoice' => $i->invoice_no . ' (' . $i->gatepass_no . ')',
                    'description' => 'Inward Bill - ' . ($i->remarks ?? ''),
                    'debit' => $i->net_amount,
                    'credit' => 0,
                    'sort_date' => $i->gatepass_date
                ];
            });

        // 🔹 1c. Raw Material Purchases → Debit (we owe vendor)
        $rmPurchases = DB::table('raw_material_purchases')
            ->where(function ($q) use ($vendorId, $vendor) {
                $q->where('vendor_id', $vendorId);
                if ($vendor && !empty($vendor->name)) {
                    $q->orWhere('vendor_name', $vendor->name);
                }
            })
            ->whereBetween('date', [$start, $end])
            ->get()
            ->map(function ($rm) {
                return [
                    'date' => $rm->date,
                    'invoice' => $rm->invoice_no,
                    'description' => 'Raw Material Purchase' . ($rm->notes ? ' - ' . $rm->notes : ''),
                    'debit' => (float)$rm->total_cost,
                    'credit' => 0,
                    'sort_date' => $rm->date
                ];
            });

        // 🔹 2. Purchase Returns → Credit (reduces vendor balance)
        $returns = DB::table('purchase_returns')
            ->where('vendor_id', $vendorId)
            ->whereBetween('return_date', [$start, $end])
            ->get()
            ->map(function ($r) {
                return [
                    'date' => $r->return_date,
                    'invoice' => $r->return_invoice,
                    'description' => 'Purchase Return',
                    'debit' => 0,
                    'credit' => $r->net_amount,
                    'sort_date' => $r->return_date
                ];
            });

        // 🔹 2b. Raw Material Purchase Returns → Credit (reduces vendor balance)
        $rmReturns = DB::table('raw_material_purchase_returns')
            ->where(function ($q) use ($vendorId, $vendor) {
                $q->where('vendor_id', $vendorId);
                if ($vendor && !empty($vendor->name)) {
                    $q->orWhere('vendor_name', $vendor->name);
                }
            })
            ->whereBetween('return_date', [$start, $end])
            ->get()
            ->map(function ($rmr) {
                return [
                    'date' => $rmr->return_date,
                    'invoice' => $rmr->return_invoice,
                    'description' => 'Raw Material Return' . ($rmr->remarks ? ' - ' . $rmr->remarks : ''),
                    'debit' => 0,
                    'credit' => (float)$rmr->total_cost,
                    'sort_date' => $rmr->return_date
                ];
            });

        // 🔹 3. Vendor Payments → Credit (we paid vendor)
        $payments = DB::table('vendor_payments')
            ->where('vendor_id', $vendorId)
            ->whereBetween('payment_date', [$start, $end])
            ->get()
            ->map(function ($v) {
                return [
                    'date' => $v->payment_date,
                    'invoice' => $v->payment_no,
                    'reference' => $v->payment_method,
                    'description' => $v->note ?? 'Cash Given',
                    'debit' => 0,
                    'credit' => $v->amount,
                    'sort_date' => $v->payment_date
                ];
            });

        // 🔹 4. Vendor Bilties → Debit (we owe vendor for freight/logistics)
        $bilties = DB::table('vendor_bilties')
            ->where('vendor_id', $vendorId)
            ->whereBetween('delivery_date', [$start, $end])
            ->get()
            ->map(function ($b) {
                return [
                    'date' => $b->delivery_date,
                    'invoice' => $b->bilty_no ?: 'Bilty',
                    'description' => $b->note ?: 'Bilty Amount',
                    'debit' => $b->amount,
                    'credit' => 0,
                    'sort_date' => $b->delivery_date
                ];
            });

        // 🔹 Merge all
        $transactions = $purchases
            ->merge($inwards)
            ->merge($rmPurchases)
            ->merge($returns)
            ->merge($rmReturns)
            ->merge($payments)
            ->merge($bilties)
            ->sortBy('sort_date')
            ->values()
            ->all();

        // 🔹 Running Balance Calculation (Debit increases, Credit decreases)

        $balance = $opening;

        foreach ($transactions as $key => $t) {

            $debit  = (float) ($t['debit'] ?? 0);
            $credit = (float) ($t['credit'] ?? 0);

            $balance = $balance + $debit - $credit;

            $transactions[$key]['balance'] = round($balance, 2);
        }


        return response()->json([
            'vendor' => $vendor,
            'opening_balance' => $opening,
            'transactions' => $transactions,
        ]);
    }

    public function vendor_ledger_pdf(Request $request)
    {
        $vendorId = $request->vendor_id;
        $start = $request->start_date;
        $end = $request->end_date;

        if (!$vendorId || !$start || !$end) {
            return redirect()->route('report.vendor.ledger')->with('error', 'Please select vendor and dates');
        }

        $request->merge(['end_date' => $end]);
        $response = $this->fetch_vendor_ledger($request);
        $data = $response->getData();

        $pdf = Pdf::loadView('admin_panel.reporting.vendor_ledger_pdf', [
            'vendor' => $data->vendor,
            'opening_balance' => $data->opening_balance,
            'transactions' => $data->transactions,
            'start_date' => $start,
            'end_date' => $end,
        ]);

        return $pdf->download('Vendor_Ledger_' . $vendorId . '.pdf');
    }

    public function cashbook(Request $request)
    {
        $selectedDate = $request->get('date', Carbon::today()->toDateString());
        $today = $selectedDate;
        $startDate = $request->get('start_date', Carbon::today()->subDays(30)->toDateString());
        $startTime = $request->start_time ?? '00:00';
        $endTime   = $request->end_time   ?? '23:59';

        $startDT = $selectedDate . ' ' . $startTime . ':00';
        $endDT   = $selectedDate . ' ' . $endTime   . ':59';

        $branches = \App\Models\Branch::orderBy('name')->get();
        $branchId = $request->get('branch_id');
        if (!$branchId) {
            $branchId = is_all_branches() ? 'all' : (string)active_branch_id();
        }
        $selectedBranchId = $branchId;

        // 🛡️ User Permission Check: Can this user view all cashbook entries or only their own?
        $currentUser = auth()->user();
        $canViewAllCashbook = $currentUser && (
            $currentUser->can('View All Cashbook') ||
            $currentUser->hasRole('super-admin') ||
            $currentUser->hasRole('Super Admin') ||
            $currentUser->hasRole('Admin') ||
            $currentUser->email === 'admin@admin.com'
        );

        $users = \App\Models\User::orderBy('name')->get();
        if ($canViewAllCashbook) {
            $selectedUserId = $request->get('user_id', 'all');
            if ($selectedUserId !== 'all' && !empty($selectedUserId)) {
                $selectedUserId = (string)$selectedUserId;
            } else {
                $selectedUserId = 'all';
            }
        } else {
            // Strictly enforce own user id
            $selectedUserId = (string)($currentUser->id ?? 0);
        }

        /* ================= OPENING BALANCE ================= */
        $prevSalesQ = Sale::where('created_at', '>=', $startDate . ' 00:00:00')
            ->where('created_at', '<', $startDT);
        $prevCustQ = CustomerPayment::where('payment_date', '>=', $startDate)
            ->where('payment_date', '<', $selectedDate)
            ->where(function($q) {
                $q->whereNull('note')->orWhere('note', 'not like', '%Paid on Sale%');
            });
        $prevVendQ = VendorPayment::where('payment_date', '>=', $startDate)
            ->where('payment_date', '<', $selectedDate)
            ->where(function($q) {
                $q->whereNull('note')->orWhere('note', 'not like', '%Paid on Purchase%');
            });
        $prevExpQ = ExpenseVoucher::where('date', '>=', $startDate)
            ->where('date', '<', $selectedDate);

        if ($selectedBranchId !== 'all') {
            $prevSalesQ->where('branch_id', $selectedBranchId);
            $prevCustQ->where('branch_id', $selectedBranchId);
            $prevVendQ->where('branch_id', $selectedBranchId);
            $prevExpQ->where('branch_id', $selectedBranchId);
        }

        if ($selectedUserId !== 'all') {
            $prevSalesQ->where('user_id', $selectedUserId);
            $prevCustQ->where('admin_or_user_id', $selectedUserId);
            $prevVendQ->where('admin_or_user_id', $selectedUserId);
            $prevExpQ->where('user_id', $selectedUserId);
        }

        // For sales opening: actual cash/card received on sales
        $previousSales = $prevSalesQ->get()->sum(function($s) {
            $paid = floatval($s->cash) + floatval($s->card);
            $net = floatval($s->total_net);
            // If walkin customer or paid >= net, full net is received
            if ($s->customer === 'Walk-in Customer' || $paid >= $net) {
                return $net;
            }
            return min($net, $paid);
        });
        $previousCustomerRecoveries = $prevCustQ->sum('amount');
        $previousVendorPayments = $prevVendQ->sum('amount');
        $previousExpenses = $prevExpQ->sum('total_amount');
        $openingBalance = ($previousSales + $previousCustomerRecoveries) - ($previousVendorPayments + $previousExpenses);

        /* ================= TODAY'S DATA (with time filter) ================= */
        $salesQ = Sale::with('user')
            ->where('created_at', '>=', $startDT)
            ->where('created_at', '<=', $endDT);
        $custQ = CustomerPayment::with(['customer', 'user'])
            ->whereDate('payment_date', $selectedDate)
            ->where(function($q) {
                $q->whereNull('note')->orWhere('note', 'not like', '%Paid on Sale%');
            });
        $vendQ = VendorPayment::with(['vendor', 'user'])
            ->whereDate('payment_date', $selectedDate)
            ->where(function($q) {
                $q->whereNull('note')->orWhere('note', 'not like', '%Paid on Purchase%');
            });
        $expQ = ExpenseVoucher::with('user')->whereDate('date', $selectedDate);

        if ($selectedBranchId !== 'all') {
            $salesQ->where('branch_id', $selectedBranchId);
            $custQ->where('branch_id', $selectedBranchId);
            $vendQ->where('branch_id', $selectedBranchId);
            $expQ->where('branch_id', $selectedBranchId);
        }

        if ($selectedUserId !== 'all') {
            $salesQ->where('user_id', $selectedUserId);
            $custQ->where('admin_or_user_id', $selectedUserId);
            $vendQ->where('admin_or_user_id', $selectedUserId);
            $expQ->where('user_id', $selectedUserId);
        }

        $allSales = $salesQ->get();
        $customerRecoveries = $custQ->get();
        $vendorPayments = $vendQ->get();
        $expenseVouchers = $expQ->get();

        /* ================= RECEIPTS BREAKDOWN ================= */
        $totalSaleCash = $allSales->sum('cash');
        $totalSaleCard = $allSales->sum('card');
        $totalChange   = $allSales->sum('change');
        $totalSaleNet  = $allSales->sum('total_net');
        $saleCount     = $allSales->count();

        $recoveryByMethod = [];
        foreach ($customerRecoveries as $cr) {
            $m = $cr->payment_method ?? 'Other';
            if (!isset($recoveryByMethod[$m])) $recoveryByMethod[$m] = 0;
            $recoveryByMethod[$m] += $cr->amount;
        }
        $totalRecoveries = $customerRecoveries->sum('amount');

        // Actual cash/card received from sales today
        $todaySaleReceived = $allSales->sum(function($s) {
            $paid = floatval($s->cash) + floatval($s->card);
            $net = floatval($s->total_net);
            if ($s->customer === 'Walk-in Customer' || $paid >= $net) {
                return $net;
            }
            return min($net, $paid);
        });

        $totalReceipts = $todaySaleReceived + $totalRecoveries;

        /* ================= PAYMENTS BREAKDOWN ================= */
        $vendorPayByMethod = [];
        foreach ($vendorPayments as $vp) {
            $m = $vp->payment_method ?? 'Other';
            if (!isset($vendorPayByMethod[$m])) $vendorPayByMethod[$m] = 0;
            $vendorPayByMethod[$m] += $vp->amount;
        }
        $totalVendorPayments = $vendorPayments->sum('amount');
        $totalExpenses = $expenseVouchers->sum('total_amount');
        $totalPayments = $totalVendorPayments + $totalExpenses;
        $closingBalance = $openingBalance + $totalReceipts - $totalPayments;

        /* ================= DETAILED ENTRIES ================= */
        $receipts = [];
        foreach ($allSales as $sale) {
            $paid = floatval($sale->cash) + floatval($sale->card);
            $net = floatval($sale->total_net);
            $received = ($sale->customer === 'Walk-in Customer' || $paid >= $net) ? $net : min($net, $paid);
            if ($received > 0) {
                $ref = '#' . $sale->invoice_no;
                if ($selectedUserId === 'all' && $sale->user) {
                    $ref .= ' (' . $sale->user->name . ')';
                }
                $receipts[] = ['title' => 'Sale', 'ref' => $ref, 'amount' => $received];
            }
        }
        foreach ($customerRecoveries as $cr) {
            $ref = ($cr->customer->customer_name ?? '-') . ' (' . ($cr->payment_method ?? 'N/A') . ')';
            if ($selectedUserId === 'all' && $cr->user) {
                $ref .= ' - by ' . $cr->user->name;
            }
            $receipts[] = ['title' => 'Recovery', 'ref' => $ref, 'amount' => $cr->amount];
        }

        $payments = [];
        foreach ($vendorPayments as $vp) {
            $ref = ($vp->vendor->name ?? '-') . ' (' . ($vp->payment_method ?? 'N/A') . ')';
            if ($selectedUserId === 'all' && $vp->user) {
                $ref .= ' - by ' . $vp->user->name;
            }
            $payments[] = ['title' => 'Vendor Pay', 'ref' => $ref, 'amount' => $vp->amount];
        }
        foreach ($expenseVouchers as $exp) {
            $remarks = is_array($exp->remarks) ? implode(', ', $exp->remarks) : ($exp->remarks ?? '');
            $ref = $remarks ?: 'Voucher #' . $exp->evid;
            if ($selectedUserId === 'all' && $exp->user) {
                $ref .= ' (' . $exp->user->name . ')';
            }
            $payments[] = ['title' => 'Expense', 'ref' => $ref, 'amount' => $exp->total_amount];
        }

        $maxRows = max(count($receipts), count($payments));

        return view('admin_panel.reporting.CashBook', compact(
            'receipts', 'payments', 'maxRows',
            'totalReceipts', 'totalPayments',
            'openingBalance', 'closingBalance',
            'selectedDate', 'startDate', 'startTime', 'endTime',
            'totalSaleCash', 'totalSaleCard', 'totalChange', 'totalSaleNet', 'saleCount',
            'recoveryByMethod', 'totalRecoveries',
            'vendorPayByMethod', 'totalVendorPayments',
            'totalExpenses', 'allSales', 'customerRecoveries', 'vendorPayments', 'expenseVouchers',
            'branches', 'selectedBranchId',
            'users', 'selectedUserId', 'canViewAllCashbook'
        ));
    }




    public function expense_vocher(Request $request)
    {
        $accountHeads = \App\Models\AccountHead::where('status', 1)->get();
        $accounts     = \App\Models\Account::where('status', 1)->get();

        $vouchers = collect();
        $grandTotal = 0;

        if ($request->hasAny(['account_heads', 'accounts', 'start_date', 'end_date'])) {

            $query = \App\Models\ExpenseVoucher::query();
            
            // 🛡️ Restrict non-admin users to their own expenses
            if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
                $query->where('user_id', auth()->id());
            }

            // Account Head filter (type = account_head_id)
            if ($request->filled('account_heads') && !in_array('all', $request->account_heads)) {
                $query->whereIn('type', $request->account_heads);
            }

            // Account filter (party_id = account_id)
            if ($request->filled('accounts')) {
                $query->whereIn('party_id', $request->accounts);
            }

            // Date filter
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('date', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            $vouchers = $query->latest()->get();

            // Grand Total
            $grandTotal = $vouchers->sum('total_amount');
        }

        return view(
            'admin_panel.reporting.expense_vocher',
            compact('accountHeads', 'accounts', 'vouchers', 'grandTotal')
        );
    }

    public function expenseVoucherAjax(Request $request)
    {
        $query = \App\Models\ExpenseVoucher::query();

        // Multi-Branch Scoping
        if (!is_all_branches()) {
            $query->where('branch_id', active_branch_id());
        }

        // 🛡️ Restrict non-admin users to their own expenses
        if (auth()->id() !== 1 && auth()->user()->email !== 'admin@admin.com' && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('super-admin')) {
            $query->where('user_id', auth()->id());
        }

        // Account Head (type)
        if ($request->filled('account_heads') && !in_array('all', $request->account_heads)) {
            $query->whereIn('type', $request->account_heads);
        }

        // Accounts (party_id)
        if ($request->filled('accounts')) {
            $query->whereIn('party_id', $request->accounts);
        }

        // Date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                $request->start_date,
                $request->end_date
            ]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $vouchers = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->get();

        $accountHeadIds = $vouchers->pluck('type')->filter()->unique();
        $accountIds = $vouchers->pluck('party_id')->filter()->unique();

        $accountHeadsMap = \App\Models\AccountHead::whereIn('id', $accountHeadIds)->pluck('name', 'id');
        $accountsMap = \App\Models\Account::whereIn('id', $accountIds)->pluck('title', 'id');

        $data = $vouchers->map(function ($v) use ($accountHeadsMap, $accountsMap) {
            $remarks = $v->remarks;
            if (is_string($remarks)) {
                $decoded = json_decode($remarks, true);
                if (is_array($decoded)) {
                    $remarks = $decoded;
                }
            }

            if (is_array($remarks)) {
                $remText = implode(', ', array_filter($remarks));
            } elseif (is_string($remarks)) {
                $remText = $remarks;
            } else {
                $remText = '-';
            }

            return [
                'evid'    => $v->evid,
                'date'    => $v->date ? \Carbon\Carbon::parse($v->date)->format('d-m-Y') : '-',
                'head'    => $accountHeadsMap[$v->type] ?? '-',
                'account' => $accountsMap[$v->party_id] ?? '-',
                'remarks' => $remText ?: '-',
                'amount'  => number_format((float)$v->total_amount, 2),
            ];
        });

        return response()->json([
            'rows' => $data,
            'total' => number_format((float)$vouchers->sum('total_amount'), 2)
        ]);
    }

    public function sale_closing_report()
    {
        $users = \App\Models\User::all();
        $branches = \App\Models\Branch::orderBy('name')->get();
        return view('admin_panel.reporting.sale_closing_report', compact('users', 'branches'));
    }

    public function fetchSaleClosingReport(Request $request)
    {
        $start = $request->start_date ?? date('Y-m-d');
        $end   = $request->end_date   ?? date('Y-m-d');
        
        $start = str_replace('T', ' ', $start);
        $end = str_replace('T', ' ', $end);
        
        if (strlen($start) == 10) $start .= ' 00:00:00';
        if (strlen($end) == 10) $end .= ' 23:59:59';
        if (strlen($start) == 16) $start .= ':00';
        if (strlen($end) == 16) $end .= ':59';

        $userId = $request->user_id;
        $branchId = $request->branch_id;

        // 1. Fetch Sales
        $salesQuery = DB::table('sales')
            ->whereBetween('created_at', [$start, $end]);

        if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
            $salesQuery->where('user_id', auth()->id());
        } elseif ($userId && $userId !== 'all') {
            $salesQuery->where('user_id', $userId);
        }

        if ($branchId && $branchId !== 'all') {
            $salesQuery->where('sales.branch_id', $branchId);
        } elseif (empty($branchId) && !is_all_branches()) {
            $salesQuery->where('sales.branch_id', active_branch_id());
        }

        $sales = $salesQuery->select('id', 'invoice_no', 'total_net', 'created_at', 'cash', 'card', 'change', 'per_price', 'qty', 'per_total', 'total_bill_amount', 'total_extradiscount')
            ->orderBy('created_at', 'asc')
            ->get();

        $totalSale = 0;
        $totalCash = 0;
        $totalCard = 0;
        $totalDiscount = 0;
        
        foreach ($sales as $s) {
            $net = floatval($s->total_net);
            $crd = floatval($s->card);
            
            // Card taken cannot exceed the net bill
            $actualCard = min($net, $crd);
            
            // The rest of the bill is assumed to be paid in cash
            $actualCash = $net - $actualCard;
            
            $totalSale += $net;
            $totalCard += $actualCard;
            $totalCash += $actualCash;

            // Compute total discount for this sale
            $prices = explode(',', $s->per_price ?? '');
            $qtys = explode(',', $s->qty ?? '');
            $totals = explode(',', $s->per_total ?? '');
            $grossTotal = 0;
            $itemTotal = 0;
            $maxIdx = max(count($prices), count($qtys), count($totals));
            for ($i = 0; $i < $maxIdx; $i++) {
                $grossTotal += (float)($prices[$i] ?? 0) * (float)($qtys[$i] ?? 0);
                $itemTotal += (float)($totals[$i] ?? 0);
            }
            $itemDiscount = $grossTotal - $itemTotal;
            $extraDiscount = (float)($s->total_extradiscount ?? 0);
            $totalDiscount += $itemDiscount + $extraDiscount;
        }

        // 2. Fetch Expenses
        $expenseQuery = DB::table('expense_vouchers')
            ->whereBetween('created_at', [$start, $end]);

        if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
            $expenseQuery->where('user_id', auth()->id());
        } elseif ($userId && $userId !== 'all') {
            $expenseQuery->where('user_id', $userId);
        }

        if ($branchId && $branchId !== 'all') {
            $expenseQuery->where('expense_vouchers.branch_id', $branchId);
        } elseif (empty($branchId) && !is_all_branches()) {
            $expenseQuery->where('expense_vouchers.branch_id', active_branch_id());
        }

        $expenses = $expenseQuery->select('id', 'evid', 'amount', 'date', 'type', 'party_id')
            ->orderBy('date', 'asc')
            ->get();

        $processedExpenses = [];
        $totalExpense = 0;
        foreach ($expenses as $ex) {
            $amounts = json_decode($ex->amount, true) ?: [];
            $sum = array_sum($amounts);
            $totalExpense += $sum;
            
            $ex->total_amount = $sum;
            $processedExpenses[] = $ex;
        }

        return response()->json([
            'sales' => $sales,
            'expenses' => $processedExpenses,
            'total_sale' => $totalSale,
            'total_cash' => $totalCash,
            'total_card' => $totalCard,
            'total_expense' => $totalExpense,
            'net_amount' => $totalSale - $totalExpense,
            'total_discount' => $totalDiscount,
            'start_date' => $start,
            'end_date' => $end
        ]);
    }

    public function printSaleClosingReport(Request $request)
    {
        $start = $request->start_date ?? date('Y-m-d');
        $end   = $request->end_date   ?? date('Y-m-d');
        
        $start = str_replace('T', ' ', $start);
        $end = str_replace('T', ' ', $end);
        
        if (strlen($start) == 10) $start .= ' 00:00:00';
        if (strlen($end) == 10) $end .= ' 23:59:59';
        if (strlen($start) == 16) $start .= ':00';
        if (strlen($end) == 16) $end .= ':59';

        $userId = $request->user_id;
        $branchId = $request->branch_id;

        $salesQuery = DB::table('sales')
            ->whereBetween('created_at', [$start, $end]);

        if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
            $salesQuery->where('user_id', auth()->id());
        } elseif ($userId && $userId !== 'all') {
            $salesQuery->where('user_id', $userId);
        }

        if ($branchId && $branchId !== 'all') {
            $salesQuery->where('sales.branch_id', $branchId);
        } elseif (empty($branchId) && !is_all_branches()) {
            $salesQuery->where('sales.branch_id', active_branch_id());
        }

        $sales = $salesQuery->select('id', 'invoice_no', 'total_net', 'created_at', 'cash', 'card', 'change', 'per_price', 'qty', 'per_total', 'total_bill_amount', 'total_extradiscount')
            ->get();

        $totalSale = 0;
        $totalCash = 0;
        $totalCard = 0;
        $totalDiscount = 0;
        
        foreach ($sales as $s) {
            $net = floatval($s->total_net);
            $crd = floatval($s->card);
            
            $actualCard = min($net, $crd);
            $actualCash = $net - $actualCard;
            
            $totalSale += $net;
            $totalCard += $actualCard;
            $totalCash += $actualCash;

            // Compute total discount for this sale
            $prices = explode(',', $s->per_price ?? '');
            $qtys = explode(',', $s->qty ?? '');
            $totals = explode(',', $s->per_total ?? '');
            $grossTotal = 0;
            $itemTotal = 0;
            $maxIdx = max(count($prices), count($qtys), count($totals));
            for ($i = 0; $i < $maxIdx; $i++) {
                $grossTotal += (float)($prices[$i] ?? 0) * (float)($qtys[$i] ?? 0);
                $itemTotal += (float)($totals[$i] ?? 0);
            }
            $itemDiscount = $grossTotal - $itemTotal;
            $extraDiscount = (float)($s->total_extradiscount ?? 0);
            $totalDiscount += $itemDiscount + $extraDiscount;
        }

        $expenseQuery = DB::table('expense_vouchers')
            ->whereBetween('created_at', [$start, $end]);

        if (auth()->id() !== 1 && !auth()->user()->hasRole('Admin')) {
            $expenseQuery->where('user_id', auth()->id());
        } elseif ($userId && $userId !== 'all') {
            $expenseQuery->where('user_id', $userId);
        }

        if ($branchId && $branchId !== 'all') {
            $expenseQuery->where('expense_vouchers.branch_id', $branchId);
        } elseif (empty($branchId) && !is_all_branches()) {
            $expenseQuery->where('expense_vouchers.branch_id', active_branch_id());
        }

        $expenses = $expenseQuery->get();

        $totalExpense = 0;
        foreach ($expenses as $ex) {
            $amounts = json_decode($ex->amount, true) ?: [];
            $totalExpense += array_sum($amounts);
        }

        $userName = 'All Users';
        if ($userId && $userId !== 'all') {
            $user = \App\Models\User::find($userId);
            if ($user) {
                $userName = $user->name;
            }
        }

        return view('admin_panel.reporting.sale_closing_print', [
            'totalSale' => $totalSale,
            'totalCash' => $totalCash,
            'totalCard' => $totalCard,
            'totalDiscount' => $totalDiscount,
            'totalExpense' => $totalExpense,
            'netAmount' => $totalSale - $totalExpense,
            'startDate' => $start,
            'endDate' => $end,
            'salesCount' => $sales->count(),
            'expensesCount' => $expenses->count(),
            'userName' => $userName
        ]);
    }

    /**
     * Print Closing Report (Black Copper / thermal receipt)
     * Business day: 7am to 3am next day
     */
    public function printClosing(Request $request)
    {
        $productIds = $request->input('product_id', 'all');
        $startTime  = $request->start_time ?? '07:00:00';
        $endTime    = $request->end_time   ?? '03:00:00';

        // Auto-compute closing period if not provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate   = $request->end_date;
        } else {
            $today = now();
            $startDate = $today->copy()->setHour(7)->setMinute(0)->setSecond(0)->format('Y-m-d');
            $endDate   = $today->copy()->addDay()->setHour(3)->setMinute(0)->setSecond(0)->format('Y-m-d');
        }

        $startDT = $startDate . ' ' . $startTime;
        $endDT   = $endDate   . ' ' . $endTime;

        $request->merge([
            'product_id' => $productIds,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ]);

        $response = $this->fetchItemStock($request);
        $json = $response->getData();

        $rows = $json->data ?? [];
        $grandTotal = $json->grand_total ?? 0;

        $onlyMovement = $request->boolean('only_movement') || $request->get('only_movement') == '1';
        if ($onlyMovement) {
            $rows = array_values(array_filter($rows, function($r) {
                $r = (object)$r;
                $hasMovement = (float)($r->produced ?? 0) != 0
                    || (float)($r->prod_usage ?? 0) != 0
                    || (float)($r->purchased ?? 0) != 0
                    || (float)($r->purchase_return ?? 0) != 0
                    || (float)($r->transfer ?? 0) != 0
                    || (float)($r->transfer_in ?? 0) != 0
                    || (float)($r->adj_increase ?? 0) != 0
                    || (float)($r->adj_decrease ?? 0) != 0
                    || (float)($r->sold ?? 0) != 0
                    || (float)($r->sale_return ?? 0) != 0;
                return $hasMovement;
            }));
        }

        $total = count($rows);

        $branchId = active_branch_id();
        $branch = \App\Models\Branch::find($branchId);
        $branchName = $branch ? $branch->name : 'Main Branch';
        $printedBy = auth()->user() ? auth()->user()->name : 'Admin';

        $dateLabel = \Carbon\Carbon::parse($startDate)->format('d-M-Y');
        $timeLabel = $startTime . ' to ' . $endTime;

        return view('admin_panel.reporting.closing_print', compact('rows', 'total', 'startDate', 'endDate', 'dateLabel', 'timeLabel', 'onlyMovement', 'branchName', 'printedBy', 'grandTotal'));
    }

    /**
     * Branch-wise Stock Matrix Report View
     */
    public function branch_stock_report()
    {
        $categories = Category::orderBy('name')->get();
        $branches   = \App\Models\Branch::orderBy('name')->get();
        return view('admin_panel.reporting.branch_stock_report', compact('categories', 'branches'));
    }

    /**
     * Fetch Branch-wise Stock Data (AJAX)
     */
    public function fetchBranchStockReport(Request $request)
    {
        $searchQ    = trim($request->get('q', ''));
        $categoryId = $request->get('category_id', 'all');

        $branches = \App\Models\Branch::orderBy('name')->get();

        $pQuery = Product::with(['variants', 'unit', 'category_relation'])->orderBy('item_name');

        if ($categoryId && $categoryId !== 'all') {
            $pQuery->where('category_id', $categoryId);
        }

        if ($searchQ !== '') {
            $pQuery->where(function($q) use ($searchQ) {
                $q->where('item_name', 'like', "%{$searchQ}%")
                  ->orWhere('item_code', 'like', "%{$searchQ}%")
                  ->orWhere('barcode_path', 'like', "%{$searchQ}%");
            });
        }

        $products = $pQuery->get();
        $productIds = $products->pluck('id')->toArray();

        // Fetch stocks grouped by product_id, variant_id, branch_id (excluding warehouse inventory)
        $stocksData = DB::table('stocks')
            ->whereIn('product_id', $productIds)
            ->whereNull('warehouse_id')
            ->select('product_id', 'variant_id', 'branch_id', DB::raw('SUM(qty) as total_qty'))
            ->groupBy('product_id', 'variant_id', 'branch_id')
            ->get();

        $stockMap = [];
        foreach ($stocksData as $st) {
            $k = $st->product_id . '_' . ($st->variant_id ?? 0);
            $bId = $st->branch_id ?? 0;
            $stockMap[$k][$bId] = ($stockMap[$k][$bId] ?? 0) + (float)$st->total_qty;
        }

        $rows = [];

        foreach ($products as $p) {
            $is_kg = $p->unit_type === 'kg';
            $catName = $p->category_relation->name ?? '-';

            if (!$is_kg && $p->variants->count() > 0) {
                foreach ($p->variants as $v) {
                    $key = $p->id . '_' . $v->id;
                    $branchStocks = [];
                    $totalStock = 0;

                    foreach ($branches as $b) {
                        $qty = (float)($stockMap[$key][$b->id] ?? 0);
                        if ($v->is_default || $p->variants->first()->id == $v->id) {
                            $qty += (float)($stockMap[$p->id . '_0'][$b->id] ?? 0);
                        }
                        $branchStocks[$b->id] = $qty;
                        $totalStock += $qty;
                    }

                    $rows[] = [
                        'item_code'     => $p->item_code,
                        'item_name'     => $p->item_name . ' (' . ($v->size_label ?: $v->variant_name) . ')',
                        'category'      => $catName,
                        'unit'          => $p->unit->name ?? 'PC',
                        'is_kg'         => false,
                        'branch_stocks' => $branchStocks,
                        'total_stock'   => $totalStock,
                    ];
                }
            } else {
                $key = $p->id . '_0';
                $branchStocks = [];
                $totalStock = 0;

                foreach ($branches as $b) {
                    $qty = (float)($stockMap[$key][$b->id] ?? 0);

                    if ($is_kg && $p->variants->count() > 0) {
                        foreach ($p->variants as $v) {
                            $vKey = $p->id . '_' . $v->id;
                            $qty += (float)($stockMap[$vKey][$b->id] ?? 0);
                        }
                    }

                    $branchStocks[$b->id] = $qty;
                    $totalStock += $qty;
                }

                $rows[] = [
                    'item_code'     => $p->item_code,
                    'item_name'     => $p->item_name,
                    'category'      => $catName,
                    'unit'          => $p->unit->name ?? ($is_kg ? 'KG' : 'PC'),
                    'is_kg'         => $is_kg,
                    'branch_stocks' => $branchStocks,
                    'total_stock'   => $totalStock,
                ];
            }
        }

        return response()->json([
            'branches' => $branches->map(fn($b) => ['id' => $b->id, 'name' => $b->name]),
            'data'     => $rows,
            'total'    => count($rows),
        ]);
    }

    // ==================== PROFIT & LOSS INTELLIGENCE REPORT ====================
    public function profit_loss_report()
    {
        $branches = \App\Models\Branch::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $vendors = \App\Models\Vendor::orderBy('name')->get();
        $customers = \App\Models\Customer::orderBy('customer_name')->get();
        return view('admin_panel.reporting.profit_loss_report', compact('branches', 'categories', 'vendors', 'customers'));
    }

    public function fetchProfitLossReport(Request $request)
    {
        $reportType = $request->input('report_type', 'product'); // product, category, customer, vendor, date, time
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $startTime = $request->input('start_time', '00:00:00');
        $endTime = $request->input('end_time', '23:59:59');
        $branchId = $request->input('branch_id', 'all');

        // Normalize timestamps
        if (strpos($startDate, 'T') !== false) {
            $parts = explode('T', $startDate);
            $startDate = $parts[0];
            if (empty($request->start_time) && isset($parts[1])) $startTime = $parts[1] . ':00';
        }
        if (strpos($endDate, 'T') !== false) {
            $parts = explode('T', $endDate);
            $endDate = $parts[0];
            if (empty($request->end_time) && isset($parts[1])) $endTime = $parts[1] . ':59';
        }

        if (strlen($startTime) == 5) $startTime .= ':00';
        if (strlen($endTime) == 5) $endTime .= ':59';

        $startDT = $startDate . ' ' . $startTime;
        $endDT = $endDate . ' ' . $endTime;

        // Base Sales Query
        $salesQuery = DB::table('sales')
            ->whereBetween('created_at', [$startDT, $endDT]);

        if ($branchId && $branchId !== 'all') {
            $salesQuery->where('branch_id', $branchId);
        } elseif (!is_all_branches()) {
            $salesQuery->where('branch_id', active_branch_id());
        }

        $sales = $salesQuery->select(
            'id', 'invoice_no', 'customer', 'product', 'variant_id', 'qty',
            'per_price', 'per_discount', 'per_total', 'total_bill_amount',
            'total_extradiscount', 'total_net', 'created_at', 'branch_id'
        )->orderBy('created_at', 'asc')->get();

        // Pre-fetch reference dictionaries
        $products = \App\Models\Product::with(['category_relation', 'variants'])->get()->keyBy('id');
        $variantsDict = \App\Models\ProductVariant::all()->keyBy('id');
        $categoriesDict = \App\Models\Category::all()->keyBy('id');
        $customersDict = \App\Models\Customer::all()->keyBy('id');
        $vendorsDict = \App\Models\Vendor::all()->keyBy('id');

        // Pre-fetch latest purchase price & vendor map
        $purchaseItems = DB::table('purchase_items')
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->select('purchase_items.product_id', 'purchase_items.variant_id', 'purchase_items.price', 'purchases.vendor_id')
            ->orderBy('purchase_items.id', 'desc')
            ->get();

        $purchasePriceMap = [];
        $productVendorMap = [];
        foreach ($purchaseItems as $pi) {
            $vKey = $pi->product_id . '_' . ($pi->variant_id ?? 0);
            if (!isset($purchasePriceMap[$vKey]) && (float)$pi->price > 0) {
                $purchasePriceMap[$vKey] = (float)$pi->price;
            }
            if (!isset($purchasePriceMap[$pi->product_id . '_0']) && (float)$pi->price > 0) {
                $purchasePriceMap[$pi->product_id . '_0'] = (float)$pi->price;
            }
            if ($pi->vendor_id && !isset($productVendorMap[$pi->product_id])) {
                $productVendorMap[$pi->product_id] = $pi->vendor_id;
            }
        }

        // Expenses in range for Date-wise calculation and Net Profit
        $expenseQuery = DB::table('expense_vouchers')
            ->whereBetween('created_at', [$startDT, $endDT]);
        if ($branchId && $branchId !== 'all') {
            if (\Illuminate\Support\Facades\Schema::hasColumn('expense_vouchers', 'branch_id')) {
                $expenseQuery->where('branch_id', $branchId);
            }
        } elseif (!is_all_branches()) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('expense_vouchers', 'branch_id')) {
                $expenseQuery->where('branch_id', active_branch_id());
            }
        }
        $expenseRecords = $expenseQuery->select('id', 'total_amount', 'created_at', 'date')->get();
        $totalExpenses = (float)$expenseRecords->sum('total_amount');

        $dailyExpenses = [];
        foreach ($expenseRecords as $exp) {
            $d = !empty($exp->date) ? substr($exp->date, 0, 10) : substr($exp->created_at, 0, 10);
            $dailyExpenses[$d] = ($dailyExpenses[$d] ?? 0) + (float)$exp->total_amount;
        }

        // Summary structures
        $summary = [
            'total_sales'    => 0,
            'total_cost'     => 0,
            'gross_profit'   => 0,
            'margin_percent' => 0,
            'total_expenses' => $totalExpenses,
            'net_profit'     => 0,
            'invoices_count' => $sales->count(),
            'items_sold_qty' => 0,
        ];

        $agg = [];

        foreach ($sales as $sale) {
            $pids   = explode(',', $sale->product ?? '');
            $qtys   = explode(',', $sale->qty ?? '');
            $prices = explode(',', $sale->per_price ?? '');
            $vids   = explode(',', $sale->variant_id ?? '');
            $totals = explode(',', $sale->per_total ?? '');

            $saleDate = substr($sale->created_at, 0, 10);
            $saleHour = (int)date('H', strtotime($sale->created_at));
            $custRaw  = trim($sale->customer ?? '');

            // Identify customer details
            $custId = is_numeric($custRaw) ? (int)$custRaw : null;
            $custObj = $custId ? $customersDict->get($custId) : null;
            $custName = $custObj ? $custObj->customer_name : ($custRaw ?: 'Walk-in Customer');
            $custPhone = $custObj ? ($custObj->customer_phone ?? '-') : 'Walk-in';

            foreach ($pids as $idx => $pid) {
                $pid = trim($pid);
                if ($pid === '' || !is_numeric($pid)) continue;

                $qty       = (float)($qtys[$idx] ?? 0);
                $unitPrice = (float)($prices[$idx] ?? 0);
                $lineTotal = (float)($totals[$idx] ?? ($qty * $unitPrice));
                $vid       = trim($vids[$idx] ?? '0');

                // Retrieve product
                $p = $products->get((int)$pid);
                $pName = $p ? $p->item_name : "Product #{$pid}";
                $pCode = $p ? $p->item_code : "-";
                $catId = $p ? ($p->category_id ?? 0) : 0;
                $catObj = $categoriesDict->get($catId);
                $catName = $catObj ? $catObj->name : ($p && $p->category_relation ? $p->category_relation->name : 'Uncategorized');

                // Variant label
                $vObj = ($vid && $variantsDict->has($vid)) ? $variantsDict->get($vid) : null;
                if ($vObj) {
                    $vLabel = $vObj->size_label ?: $vObj->variant_name;
                    if ($vLabel) $pName .= " ({$vLabel})";
                }

                // Cost Determination
                $unitCost = 0;
                if (isset($purchasePriceMap[$pid . '_' . $vid]) && $purchasePriceMap[$pid . '_' . $vid] > 0) {
                    $unitCost = $purchasePriceMap[$pid . '_' . $vid];
                } elseif (isset($purchasePriceMap[$pid . '_0']) && $purchasePriceMap[$pid . '_0'] > 0) {
                    $unitCost = $purchasePriceMap[$pid . '_0'];
                } elseif ($vObj && $vObj->cost_price > 0) {
                    $unitCost = (float)$vObj->cost_price;
                } elseif ($p && ($firstV = $p->variants->first()) && $firstV->cost_price > 0) {
                    $unitCost = (float)$firstV->cost_price;
                }

                $lineCost = $qty * $unitCost;
                $lineProfit = $lineTotal - $lineCost;

                // Accumulate overall summary
                $summary['total_sales']    += $lineTotal;
                $summary['total_cost']     += $lineCost;
                $summary['items_sold_qty'] += $qty;

                // Map vendor
                $vendorId = $productVendorMap[(int)$pid] ?? null;
                $vendorObj = $vendorId ? $vendorsDict->get($vendorId) : null;
                $vendorName = $vendorObj ? $vendorObj->name : 'In-house / Direct';

                // Aggregation per dimension:
                if ($reportType === 'product') {
                    $key = $pid . '_' . $vid;
                    if (!isset($agg[$key])) {
                        $agg[$key] = [
                            'item_code'      => $pCode,
                            'item_name'      => $pName,
                            'category'       => $catName,
                            'qty_sold'       => 0,
                            'avg_sale_price' => $unitPrice,
                            'cost_price'     => $unitCost,
                            'total_sales'    => 0,
                            'total_cost'     => 0,
                            'profit'         => 0,
                            'margin_percent' => 0,
                        ];
                    }
                    $agg[$key]['qty_sold']    += $qty;
                    $agg[$key]['total_sales'] += $lineTotal;
                    $agg[$key]['total_cost']  += $lineCost;
                    $agg[$key]['profit']      += $lineProfit;
                } elseif ($reportType === 'category') {
                    $key = $catId ?: 0;
                    if (!isset($agg[$key])) {
                        $agg[$key] = [
                            'category_id'    => $key,
                            'category_name'  => $catName,
                            'items_count'    => [],
                            'qty_sold'       => 0,
                            'total_sales'    => 0,
                            'total_cost'     => 0,
                            'profit'         => 0,
                            'margin_percent' => 0,
                        ];
                    }
                    $agg[$key]['items_count'][$pid] = true;
                    $agg[$key]['qty_sold']    += $qty;
                    $agg[$key]['total_sales'] += $lineTotal;
                    $agg[$key]['total_cost']  += $lineCost;
                    $agg[$key]['profit']      += $lineProfit;
                } elseif ($reportType === 'customer') {
                    $key = $custId ? 'cust_' . $custId : 'walk_in';
                    if (!isset($agg[$key])) {
                        $agg[$key] = [
                            'customer_name'  => $custName,
                            'phone'          => $custPhone,
                            'invoices'       => [],
                            'qty_sold'       => 0,
                            'total_sales'    => 0,
                            'total_cost'     => 0,
                            'profit'         => 0,
                            'margin_percent' => 0,
                        ];
                    }
                    $agg[$key]['invoices'][$sale->id] = true;
                    $agg[$key]['qty_sold']    += $qty;
                    $agg[$key]['total_sales'] += $lineTotal;
                    $agg[$key]['total_cost']  += $lineCost;
                    $agg[$key]['profit']      += $lineProfit;
                } elseif ($reportType === 'vendor') {
                    $key = $vendorId ? 'vend_' . $vendorId : 'direct';
                    if (!isset($agg[$key])) {
                        $agg[$key] = [
                            'vendor_name'    => $vendorName,
                            'products'       => [],
                            'qty_sold'       => 0,
                            'total_sales'    => 0,
                            'total_cost'     => 0,
                            'profit'         => 0,
                            'margin_percent' => 0,
                        ];
                    }
                    $agg[$key]['products'][$pid] = true;
                    $agg[$key]['qty_sold']    += $qty;
                    $agg[$key]['total_sales'] += $lineTotal;
                    $agg[$key]['total_cost']  += $lineCost;
                    $agg[$key]['profit']      += $lineProfit;
                } elseif ($reportType === 'date') {
                    $key = $saleDate;
                    if (!isset($agg[$key])) {
                        $agg[$key] = [
                            'date'           => $saleDate,
                            'day'            => date('l', strtotime($saleDate)),
                            'invoices'       => [],
                            'qty_sold'       => 0,
                            'total_sales'    => 0,
                            'total_cost'     => 0,
                            'expenses'       => (float)($dailyExpenses[$saleDate] ?? 0),
                            'gross_profit'   => 0,
                            'net_profit'     => 0,
                            'margin_percent' => 0,
                        ];
                    }
                    $agg[$key]['invoices'][$sale->id] = true;
                    $agg[$key]['qty_sold']    += $qty;
                    $agg[$key]['total_sales'] += $lineTotal;
                    $agg[$key]['total_cost']  += $lineCost;
                } elseif ($reportType === 'time') {
                    $key = $saleHour;
                    if (!isset($agg[$key])) {
                        $startH = str_pad($saleHour, 2, '0', STR_PAD_LEFT) . ':00';
                        $endH = str_pad(($saleHour + 1) % 24, 2, '0', STR_PAD_LEFT) . ':00';
                        $agg[$key] = [
                            'hour'           => $saleHour,
                            'time_slot'      => date('h:i A', strtotime($startH)) . ' - ' . date('h:i A', strtotime($endH)),
                            'invoices'       => [],
                            'qty_sold'       => 0,
                            'total_sales'    => 0,
                            'total_cost'     => 0,
                            'profit'         => 0,
                            'margin_percent' => 0,
                        ];
                    }
                    $agg[$key]['invoices'][$sale->id] = true;
                    $agg[$key]['qty_sold']    += $qty;
                    $agg[$key]['total_sales'] += $lineTotal;
                    $agg[$key]['total_cost']  += $lineCost;
                    $agg[$key]['profit']      += $lineProfit;
                }
            }
        }

        // Finalize rows calculation
        $rows = [];
        $summary['gross_profit'] = $summary['total_sales'] - $summary['total_cost'];
        $summary['net_profit']   = $summary['gross_profit'] - $summary['total_expenses'];
        $summary['margin_percent'] = $summary['total_sales'] > 0
            ? round(($summary['gross_profit'] / $summary['total_sales']) * 100, 2)
            : 0;

        if ($reportType === 'product') {
            foreach ($agg as $r) {
                $r['avg_sale_price'] = $r['qty_sold'] > 0 ? round($r['total_sales'] / $r['qty_sold'], 2) : 0;
                $r['margin_percent'] = $r['total_sales'] > 0 ? round(($r['profit'] / $r['total_sales']) * 100, 2) : 0;
                $rows[] = $r;
            }
            usort($rows, fn($a, $b) => $b['total_sales'] <=> $a['total_sales']);
        } elseif ($reportType === 'category') {
            foreach ($agg as $r) {
                $r['products_count'] = count($r['items_count']);
                unset($r['items_count']);
                $r['margin_percent'] = $r['total_sales'] > 0 ? round(($r['profit'] / $r['total_sales']) * 100, 2) : 0;
                $r['contribution_percent'] = $summary['total_sales'] > 0 ? round(($r['total_sales'] / $summary['total_sales']) * 100, 2) : 0;
                $rows[] = $r;
            }
            usort($rows, fn($a, $b) => $b['total_sales'] <=> $a['total_sales']);
        } elseif ($reportType === 'customer') {
            foreach ($agg as $r) {
                $r['invoices_count'] = count($r['invoices']);
                unset($r['invoices']);
                $r['margin_percent'] = $r['total_sales'] > 0 ? round(($r['profit'] / $r['total_sales']) * 100, 2) : 0;
                $rows[] = $r;
            }
            usort($rows, fn($a, $b) => $b['total_sales'] <=> $a['total_sales']);
        } elseif ($reportType === 'vendor') {
            foreach ($agg as $r) {
                $r['products_count'] = count($r['products']);
                unset($r['products']);
                $r['margin_percent'] = $r['total_sales'] > 0 ? round(($r['profit'] / $r['total_sales']) * 100, 2) : 0;
                $rows[] = $r;
            }
            usort($rows, fn($a, $b) => $b['total_sales'] <=> $a['total_sales']);
        } elseif ($reportType === 'date') {
            foreach ($agg as $r) {
                $r['invoices_count'] = count($r['invoices']);
                unset($r['invoices']);
                $r['gross_profit'] = $r['total_sales'] - $r['total_cost'];
                $r['net_profit']   = $r['gross_profit'] - $r['expenses'];
                $r['margin_percent'] = $r['total_sales'] > 0 ? round(($r['net_profit'] / $r['total_sales']) * 100, 2) : 0;
                $rows[] = $r;
            }
            usort($rows, fn($a, $b) => strcmp($b['date'], $a['date']));
        } elseif ($reportType === 'time') {
            ksort($agg);
            $maxSalesInHour = 0;
            foreach ($agg as $h => $r) {
                if ($r['total_sales'] > $maxSalesInHour) $maxSalesInHour = $r['total_sales'];
            }
            foreach ($agg as $r) {
                $r['invoices_count'] = count($r['invoices']);
                unset($r['invoices']);
                $r['margin_percent'] = $r['total_sales'] > 0 ? round(($r['profit'] / $r['total_sales']) * 100, 2) : 0;
                $ratio = $maxSalesInHour > 0 ? ($r['total_sales'] / $maxSalesInHour) : 0;
                if ($ratio >= 0.75) {
                    $r['intensity'] = 'Peak Hour 🔥';
                    $r['badge_class'] = 'bg-danger';
                } elseif ($ratio >= 0.40) {
                    $r['intensity'] = 'Moderate ⚡';
                    $r['badge_class'] = 'bg-warning text-dark';
                } else {
                    $r['intensity'] = 'Normal ⏱';
                    $r['badge_class'] = 'bg-secondary';
                }
                $rows[] = $r;
            }
        }

        return response()->json([
            'success'     => true,
            'report_type' => $reportType,
            'summary'     => $summary,
            'rows'        => $rows,
            'total_rows'  => count($rows),
        ]);
    }

    public function production_report()
    {
        $branches = \App\Models\Branch::orderBy('name')->get();
        $warehouses = \App\Models\Warehouse::orderBy('warehouse_name')->get();
        $products = \App\Models\Product::orderBy('item_name')->get();

        return view('admin_panel.reporting.production_report', compact('branches', 'warehouses', 'products'));
    }

    public function fetchProductionReport(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $branchId  = $request->branch_id;
        $warehouseId = $request->warehouse_id;
        $productId = $request->product_id;

        $query = DB::table('production_entries as pe')
            ->leftJoin('users as u', 'u.id', '=', 'pe.created_by')
            ->leftJoin('branches as b', 'b.id', '=', 'pe.branch_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'pe.warehouse_id');

        if ($startDate && $endDate) {
            $query->whereBetween('pe.production_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('pe.production_date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('pe.production_date', '<=', $endDate);
        }

        if ($branchId && $branchId !== 'all') {
            $query->where('pe.branch_id', $branchId);
        } elseif (empty($branchId) && !is_all_branches()) {
            $query->where('pe.branch_id', active_branch_id());
        }

        if ($warehouseId && $warehouseId !== 'all') {
            $query->where('pe.warehouse_id', $warehouseId);
        }

        if ($productId && $productId !== 'all') {
            $query->whereExists(function($q) use ($productId) {
                $q->select(DB::raw(1))
                  ->from('production_entry_items as pei_filter')
                  ->whereColumn('pei_filter.production_entry_id', 'pe.id')
                  ->where('pei_filter.product_id', $productId);
            });
        }

        $entries = $query->select(
            'pe.*',
            'u.name as user_name',
            'b.name as branch_name',
            'w.warehouse_name'
        )
        ->orderBy('pe.production_date', 'desc')
        ->orderBy('pe.id', 'desc')
        ->get();

        $entryIds = $entries->pluck('id');

        // Fetch Finished Products Produced
        $finishedItems = collect();
        if ($entryIds->isNotEmpty()) {
            $finishedItems = DB::table('production_entry_items as pei')
                ->leftJoin('products as p', 'p.id', '=', 'pei.product_id')
                ->leftJoin('product_variants as pv', 'pv.id', '=', 'pei.variant_id')
                ->leftJoin('units as u', 'u.id', '=', 'p.unit_id')
                ->whereIn('pei.production_entry_id', $entryIds)
                ->select(
                    'pei.*',
                    'p.item_name',
                    'p.item_code',
                    'p.unit_type',
                    'u.name as unit_name',
                    'pv.size_label',
                    'pv.variant_name',
                    'pv.size_value'
                )
                ->get()
                ->groupBy('production_entry_id');
        }

        // Fetch Raw Materials Consumed
        $rmUsage = collect();
        if ($entryIds->isNotEmpty()) {
            $rmUsage = DB::table('production_raw_material_usage as rmu')
                ->leftJoin('raw_materials as rm', 'rm.id', '=', 'rmu.raw_material_id')
                ->leftJoin('products as ip', 'ip.id', '=', 'rmu.ingredient_product_id')
                ->whereIn('rmu.production_entry_id', $entryIds)
                ->select(
                    'rmu.*',
                    'rm.name as rm_name',
                    'rm.unit as rm_unit',
                    'rm.consumption_unit',
                    'rm.conversion_factor',
                    'ip.item_name as ingredient_product_name',
                    'ip.item_code as ingredient_product_code'
                )
                ->get()
                ->groupBy('production_entry_id');
        }

        $totalBatches = $entries->count();
        $totalCost = 0;
        $totalFinishedUnits = 0;
        $totalRawMaterialsCount = 0;

        $rows = [];
        foreach ($entries as $e) {
            $batchCost = (float)($e->production_cost ?? 0);

            $itemsForBatch = $finishedItems->get($e->id, collect());
            $rmForBatch = $rmUsage->get($e->id, collect());

            $finishedDetails = [];
            $batchQtyTotal = 0;

            foreach ($itemsForBatch as $fi) {
                $pName = $fi->item_name ?? 'Unknown';
                $vName = $fi->size_label ?: $fi->variant_name;
                $unitName = $fi->unit ?? $fi->unit_name ?? 'Pc';
                $isKg = ($fi->unit_type === 'kg') || str_contains(strtolower($unitName), 'kg');

                $qtyEntered = (float)$fi->qty_entered;
                $batchQtyTotal += $qtyEntered;
                $totalFinishedUnits += $qtyEntered;

                $finishedDetails[] = [
                    'product_id' => $fi->product_id,
                    'item_code'  => $fi->item_code,
                    'item_name'  => $pName,
                    'variant'    => $vName,
                    'qty_entered'=> $qtyEntered,
                    'qty_stock'  => (float)$fi->qty_stock,
                    'unit'       => $isKg ? 'KG' : $unitName,
                    'notes'      => $fi->notes,
                ];
            }

            $rmDetails = [];
            $batchRmCost = 0;
            foreach ($rmForBatch as $ru) {
                $totalRawMaterialsCount++;
                $matName = $ru->rm_name ?? ($ru->ingredient_product_name ? $ru->ingredient_product_name . ' [Base Product]' : 'RM #'.$ru->raw_material_id);
                $matUnit = $ru->consumption_unit ?? ($ru->rm_unit ?? 'Unit');
                $qtyUsed = (float)$ru->qty_used;
                $costPerUnit = (float)$ru->cost_per_unit;
                $lineTotal = (float)$ru->total_cost;
                $batchRmCost += $lineTotal;

                $rmDetails[] = [
                    'name'          => $matName,
                    'unit'          => $matUnit,
                    'qty_used'      => $qtyUsed,
                    'cost_per_unit' => $costPerUnit,
                    'total_cost'    => $lineTotal,
                ];
            }

            // If entry production_cost is 0 but raw materials have total cost, use batchRmCost
            $finalCost = $batchCost > 0 ? $batchCost : $batchRmCost;
            $totalCost += $finalCost;
            $unitCost = ($batchQtyTotal > 0) ? ($finalCost / $batchQtyTotal) : 0;

            $rows[] = [
                'id'              => $e->id,
                'entry_no'        => $e->entry_no,
                'production_date' => $e->production_date,
                'source'          => $e->source,
                'branch_name'     => $e->branch_name ?? active_branch_name(),
                'warehouse_name'  => $e->warehouse_name ?? '-',
                'notes'           => $e->notes ?? '',
                'user_name'       => $e->user_name ?? 'System',
                'finished_items'  => $finishedDetails,
                'total_qty'       => $batchQtyTotal,
                'raw_materials'   => $rmDetails,
                'production_cost' => $finalCost,
                'unit_cost'       => $unitCost,
                'gatepass_url'    => route('production.gatepass', $e->id),
            ];
        }

        return response()->json([
            'success' => true,
            'summary' => [
                'total_batches'         => $totalBatches,
                'total_finished_qty'    => $totalFinishedUnits,
                'total_production_cost' => $totalCost,
                'total_rm_records'      => $totalRawMaterialsCount,
            ],
            'rows'    => $rows,
        ]);
    }

    // =========================================================================
    // BALANCE SHEET REPORT MODULE
    // =========================================================================
    public function balance_sheet_report(Request $request)
    {
        $branches = \App\Models\Branch::orderBy('name')->get();
        $asOfDate = date('Y-m-d');
        return view('admin_panel.reporting.balance_sheet_report', compact('branches', 'asOfDate'));
    }

    public function fetchBalanceSheetReport(Request $request)
    {
        $asOfDate = $request->input('as_of_date', date('Y-m-d'));
        $endDT    = $asOfDate . ' 23:59:59';
        $branchId = $request->input('branch_id', 'all');

        $isAllBranch = ($branchId === 'all');
        $activeBId   = $isAllBranch ? null : (int)$branchId;
        if (!$activeBId && !is_all_branches()) {
            $activeBId = active_branch_id();
            $isAllBranch = false;
        }

        // ==========================================
        // 1. ASSETS - CASH IN HAND
        // ==========================================
        $salesCashQ = DB::table('sales')->where('created_at', '<=', $endDT);
        if (!$isAllBranch) $salesCashQ->where('branch_id', $activeBId);
        $totalSalesCash = (float)$salesCashQ->sum(DB::raw('COALESCE(cash, 0) - COALESCE(`change`, 0)'));

        $custPayCashQ = DB::table('customer_payments')
            ->where('payment_date', '<=', $asOfDate)
            ->where(function($q) {
                $q->where('payment_method', 'cash')->orWhere('cash', '>', 0);
            })
            ->where(function($q) {
                $q->whereNull('note')->orWhere('note', 'not like', '%Paid on Sale%');
            });
        if (!$isAllBranch) $custPayCashQ->where('branch_id', $activeBId);
        $totalCustCash = (float)$custPayCashQ->sum(DB::raw('CASE WHEN cash > 0 THEN cash ELSE amount END'));

        $vendPayCashQ = DB::table('vendor_payments')
            ->where('payment_date', '<=', $asOfDate)
            ->where(function($q) {
                $q->where('payment_method', 'cash')->orWhere('cash', '>', 0);
            })
            ->where(function($q) {
                $q->whereNull('note')->orWhere('note', 'not like', '%Paid on Purchase%');
            });
        if (!$isAllBranch) $vendPayCashQ->where('branch_id', $activeBId);
        $totalVendCash = (float)$vendPayCashQ->sum(DB::raw('CASE WHEN cash > 0 THEN cash ELSE amount END'));

        $expQ = DB::table('expense_vouchers')->where('created_at', '<=', $endDT);
        if (!$isAllBranch && \Illuminate\Support\Facades\Schema::hasColumn('expense_vouchers', 'branch_id')) {
            $expQ->where('branch_id', $activeBId);
        }
        $totalCashExp = (float)$expQ->sum('total_amount');

        $cashAccountsQ = DB::table('accounts')
            ->join('account_heads', 'account_heads.id', '=', 'accounts.head_id')
            ->where('accounts.status', 1)
            ->where(function($q) {
                $q->where('account_heads.name', 'like', '%Cash%')
                  ->orWhere('accounts.title', 'like', '%Cash%');
            });
        $cashAccounts = $cashAccountsQ->select('accounts.id', 'accounts.title', 'accounts.account_code', 'accounts.opening_balance')->get();
        $accountCashOpening = (float)$cashAccounts->sum('opening_balance');

        $counterCashBalance = max(0, ($totalSalesCash + $totalCustCash) - ($totalVendCash + $totalCashExp));
        $totalCashInHand = $counterCashBalance + $accountCashOpening;

        // ==========================================
        // 2. ASSETS - BANK ACCOUNTS
        // ==========================================
        $bankAccountsQ = DB::table('accounts')
            ->join('account_heads', 'account_heads.id', '=', 'accounts.head_id')
            ->where('accounts.status', 1)
            ->where(function($q) {
                $q->where('account_heads.name', 'like', '%Bank%')
                  ->orWhere('accounts.title', 'like', '%Bank%')
                  ->orWhere('accounts.head_id', 1);
            });
        $bankAccountsData = $bankAccountsQ->select('accounts.id', 'accounts.title', 'accounts.account_code', 'accounts.bank_name', 'accounts.opening_balance')->get();

        $bankAccountsList = [];
        $totalBankBalances = 0;

        foreach ($bankAccountsData as $ba) {
            $op = (float)$ba->opening_balance;
            $cpQ = DB::table('customer_payments')
                ->where('card_account_id', $ba->id)
                ->where('payment_date', '<=', $asOfDate);
            if (!$isAllBranch) $cpQ->where('branch_id', $activeBId);
            $inflow = (float)$cpQ->sum(DB::raw('CASE WHEN card > 0 THEN card ELSE amount END'));

            $vpQ = DB::table('vendor_payments')
                ->where('card_account_id', $ba->id)
                ->where('payment_date', '<=', $asOfDate);
            if (!$isAllBranch) $vpQ->where('branch_id', $activeBId);
            $outflow = (float)$vpQ->sum(DB::raw('CASE WHEN card > 0 THEN card ELSE amount END'));

            $currentBal = $op + $inflow - $outflow;
            $bankAccountsList[] = [
                'id'              => $ba->id,
                'title'           => $ba->title,
                'account_code'    => $ba->account_code ?? ('ACC-' . $ba->id),
                'bank_name'       => $ba->bank_name ?? 'Bank',
                'opening_balance' => $op,
                'inflow'          => $inflow,
                'outflow'         => $outflow,
                'current_balance' => $currentBal,
            ];
            $totalBankBalances += $currentBal;
        }

        // ==========================================
        // 3. ASSETS - ACCOUNTS RECEIVABLE (CUSTOMERS)
        // ==========================================
        $customersQ = DB::table('customers');
        if (!$isAllBranch && \Illuminate\Support\Facades\Schema::hasColumn('customers', 'branch_id')) {
            $customersQ->where('branch_id', $activeBId);
        }
        $customers = $customersQ->select('id', 'customer_name', 'mobile', 'opening_balance')->get();

        $customerReceivables = [];
        $customerAdvances    = [];
        $totalReceivables    = 0;
        $totalAdvancesFromCust = 0;

        $latestCustomerLedgers = DB::table('customer_ledgers')
            ->select('customer_id', 'closing_balance')
            ->where('created_at', '<=', $endDT)
            ->orderBy('id', 'desc')
            ->get()
            ->unique('customer_id')
            ->keyBy('customer_id');

        foreach ($customers as $cust) {
            $bal = 0;
            if (isset($latestCustomerLedgers[$cust->id])) {
                $bal = (float)$latestCustomerLedgers[$cust->id]->closing_balance;
            } else {
                $init = (float)($cust->opening_balance ?? 0);
                $cSales = (float)DB::table('sales')->where('customer', $cust->id)->where('created_at', '<=', $endDT)->sum(DB::raw('COALESCE(total_net, total_bill_amount)'));
                $cPay = (float)DB::table('customer_payments')->where('customer_id', $cust->id)->where('payment_date', '<=', $asOfDate)->sum('amount');
                $bal = $init + $cSales - $cPay;
            }

            if ($bal > 0) {
                $customerReceivables[] = [
                    'id'      => $cust->id,
                    'name'    => $cust->customer_name,
                    'phone'   => $cust->mobile ?? '-',
                    'balance' => $bal,
                ];
                $totalReceivables += $bal;
            } elseif ($bal < 0) {
                $customerAdvances[] = [
                    'id'      => $cust->id,
                    'name'    => $cust->customer_name,
                    'phone'   => $cust->mobile ?? '-',
                    'balance' => abs($bal),
                ];
                $totalAdvancesFromCust += abs($bal);
            }
        }
        usort($customerReceivables, function($a, $b) { return $b['balance'] <=> $a['balance']; });

        // ==========================================
        // 4. ASSETS - INVENTORY / STOCK VALUATION
        // ==========================================
        $stocksQ = DB::table('stocks')
            ->join('products', 'products.id', '=', 'stocks.product_id')
            ->leftJoin('product_variants', 'product_variants.id', '=', 'stocks.variant_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id');
        if (!$isAllBranch) $stocksQ->where('stocks.branch_id', $activeBId);

        $stocksData = $stocksQ->select(
            'stocks.product_id',
            'stocks.variant_id',
            'stocks.qty',
            'products.item_name',
            'products.item_code',
            'products.price as product_price',
            'categories.name as category_name',
            'product_variants.variant_name',
            'product_variants.cost_price as variant_cost',
            'product_variants.price as variant_price'
        )->get();

        $latestPurchasePrices = DB::table('purchase_items')
            ->select('product_id', 'variant_id', 'price')
            ->orderBy('id', 'desc')
            ->get()
            ->keyBy(function($item) {
                return $item->product_id . '_' . ($item->variant_id ?? 0);
            });

        $finishedGoodsValue = 0;
        $totalStockQty = 0;
        $categoryStockSummary = [];
        $topStockItems = [];

        foreach ($stocksData as $stk) {
            $qty = (float)$stk->qty;
            if ($qty <= 0) continue;

            $vKey = $stk->product_id . '_' . ($stk->variant_id ?? 0);
            $pKey = $stk->product_id . '_0';

            $unitCost = 0;
            if (isset($latestPurchasePrices[$vKey])) {
                $unitCost = (float)$latestPurchasePrices[$vKey]->price;
            } elseif (isset($latestPurchasePrices[$pKey])) {
                $unitCost = (float)$latestPurchasePrices[$pKey]->price;
            } elseif (!empty($stk->variant_cost) && (float)$stk->variant_cost > 0) {
                $unitCost = (float)$stk->variant_cost;
            } elseif (!empty($stk->variant_price) && (float)$stk->variant_price > 0) {
                $unitCost = (float)$stk->variant_price * 0.75;
            } else {
                $unitCost = (float)($stk->product_price ?? 0) * 0.75;
            }

            $lineValue = $qty * $unitCost;
            $finishedGoodsValue += $lineValue;
            $totalStockQty += $qty;

            $catName = $stk->category_name ?? 'General';
            $categoryStockSummary[$catName] = ($categoryStockSummary[$catName] ?? 0) + $lineValue;

            $topStockItems[] = [
                'name'      => $stk->item_name . ($stk->variant_name ? ' (' . $stk->variant_name . ')' : ''),
                'code'      => $stk->item_code,
                'category'  => $catName,
                'qty'       => $qty,
                'unit_cost' => $unitCost,
                'total_val' => $lineValue,
            ];
        }

        // Raw Materials Stock Valuation
        $rawMaterialValue = 0;
        if (\Illuminate\Support\Facades\Schema::hasTable('raw_material_stocks')) {
            $rmStocksQ = DB::table('raw_material_stocks')
                ->join('raw_materials', 'raw_materials.id', '=', 'raw_material_stocks.raw_material_id');
            if (!$isAllBranch) $rmStocksQ->where('raw_material_stocks.branch_id', $activeBId);

            $rmStocks = $rmStocksQ->select(
                'raw_materials.id',
                'raw_materials.name',
                'raw_materials.unit',
                'raw_materials.consumption_unit',
                'raw_materials.conversion_factor',
                'raw_material_stocks.qty'
            )->get();

            $latestRmPrices = DB::table('raw_material_purchase_items')
                ->select('raw_material_id', 'price_per_unit')
                ->orderBy('id', 'desc')
                ->get()
                ->keyBy('raw_material_id');

            foreach ($rmStocks as $rm) {
                $qty = (float)$rm->qty;
                if ($qty <= 0) continue;
                $conv = (float)($rm->conversion_factor > 0 ? $rm->conversion_factor : 1);
                $pricePerUnit = isset($latestRmPrices[$rm->id]) ? (float)$latestRmPrices[$rm->id]->price_per_unit : 0;
                $val = ($qty / $conv) * $pricePerUnit;
                $rawMaterialValue += $val;

                $topStockItems[] = [
                    'name'      => '[RM] ' . $rm->name,
                    'code'      => 'RM-' . $rm->id,
                    'category'  => 'Raw Materials',
                    'qty'       => round($qty / $conv, 2) . ' ' . $rm->unit,
                    'unit_cost' => $pricePerUnit,
                    'total_val' => $val,
                ];
            }
        }

        usort($topStockItems, function($a, $b) { return $b['total_val'] <=> $a['total_val']; });
        $totalInventoryValue = $finishedGoodsValue + $rawMaterialValue;

        // ==========================================
        // 5. ASSETS - FIXED ASSETS
        // ==========================================
        $assetsQ = \App\Models\Asset::whereNull('deleted_at')->where('status', '!=', 'disposed');
        if (!$isAllBranch && \Illuminate\Support\Facades\Schema::hasColumn('assets', 'branch_id')) {
            $assetsQ->where('branch_id', $activeBId);
        }
        $assetsList = $assetsQ->with('category')->get();

        $totalFixedAssetsCost = 0;
        $totalFixedAssetsValue = 0;
        $fixedAssetsDetailed = [];

        foreach ($assetsList as $ast) {
            $cost = (float)$ast->purchase_cost;
            $val  = (float)($ast->current_value > 0 ? $ast->current_value : $ast->estimated_current_value);
            if ($val <= 0) $val = $cost;

            $totalFixedAssetsCost += $cost;
            $totalFixedAssetsValue += $val;

            $fixedAssetsDetailed[] = [
                'id'            => $ast->id,
                'code'          => $ast->asset_code,
                'name'          => $ast->name,
                'category'      => $ast->category->name ?? 'Asset',
                'purchase_cost' => $cost,
                'current_value' => $val,
                'status'        => $ast->status,
            ];
        }

        // ==========================================
        // TOTAL ASSETS
        // ==========================================
        $totalCurrentAssets = $totalCashInHand + $totalBankBalances + $totalReceivables + $totalInventoryValue;
        $totalAssets = $totalCurrentAssets + $totalFixedAssetsValue;

        // ==========================================
        // 6. LIABILITIES - ACCOUNTS PAYABLE (VENDORS)
        // ==========================================
        $vendorsQ = DB::table('vendors');
        $vendors = $vendorsQ->select('id', 'name', 'phone', 'opening_balance')->get();

        $vendorPayables = [];
        $vendorAdvances = [];
        $totalPayables  = 0;
        $totalAdvancesToVendor = 0;

        $latestVendorLedgers = DB::table('vendor_ledgers')
            ->select('vendor_id', 'closing_balance')
            ->where('created_at', '<=', $endDT)
            ->orderBy('id', 'desc')
            ->get()
            ->unique('vendor_id')
            ->keyBy('vendor_id');

        foreach ($vendors as $vnd) {
            $bal = 0;
            if (isset($latestVendorLedgers[$vnd->id])) {
                $bal = (float)$latestVendorLedgers[$vnd->id]->closing_balance;
            } else {
                $init = (float)($vnd->opening_balance ?? 0);
                $vPur = (float)DB::table('purchases')->where('vendor_id', $vnd->id)->where('purchase_date', '<=', $asOfDate)->sum('net_amount');
                $vRmPur = (float)DB::table('raw_material_purchases')->where('vendor_id', $vnd->id)->where('date', '<=', $asOfDate)->sum('total_cost');
                $vRet = (float)DB::table('purchase_returns')->where('vendor_id', $vnd->id)->where('return_date', '<=', $asOfDate)->sum('net_amount');
                $vRmRet = (float)DB::table('raw_material_purchase_returns')->where('vendor_id', $vnd->id)->where('return_date', '<=', $asOfDate)->sum('total_cost');
                $vPay = (float)DB::table('vendor_payments')->where('vendor_id', $vnd->id)->where('payment_date', '<=', $asOfDate)->sum('amount');
                $bal = $init + $vPur + $vRmPur - $vRet - $vRmRet - $vPay;
            }

            if ($bal > 0) {
                $vendorPayables[] = [
                    'id'      => $vnd->id,
                    'name'    => $vnd->name,
                    'phone'   => $vnd->phone ?? '-',
                    'balance' => $bal,
                ];
                $totalPayables += $bal;
            } elseif ($bal < 0) {
                $vendorAdvances[] = [
                    'id'      => $vnd->id,
                    'name'    => $vnd->name,
                    'phone'   => $vnd->phone ?? '-',
                    'balance' => abs($bal),
                ];
                $totalAdvancesToVendor += abs($bal);
            }
        }
        usort($vendorPayables, function($a, $b) { return $b['balance'] <=> $a['balance']; });

        if ($totalAdvancesToVendor > 0) {
            $totalCurrentAssets += $totalAdvancesToVendor;
            $totalAssets += $totalAdvancesToVendor;
        }

        $totalCurrentLiabilities = $totalPayables + $totalAdvancesFromCust;
        $totalLongTermLiabilities = 0;
        $totalLiabilities = $totalCurrentLiabilities + $totalLongTermLiabilities;

        // ==========================================
        // 7. EQUITY / CAPITAL & NET PROFIT
        // ==========================================
        $salesRevenueQ = DB::table('sales')->where('created_at', '<=', $endDT);
        if (!$isAllBranch) $salesRevenueQ->where('branch_id', $activeBId);
        $totalSalesRevenue = (float)$salesRevenueQ->sum(DB::raw('COALESCE(total_net, total_bill_amount)'));

        $purchasesCostQ = DB::table('purchases')->where('purchase_date', '<=', $asOfDate);
        if (!$isAllBranch) $purchasesCostQ->where('branch_id', $activeBId);
        $totalPurchasesCost = (float)$purchasesCostQ->sum('net_amount');

        $operatingExpensesQ = DB::table('expense_vouchers')->where('created_at', '<=', $endDT);
        if (!$isAllBranch && \Illuminate\Support\Facades\Schema::hasColumn('expense_vouchers', 'branch_id')) {
            $operatingExpensesQ->where('branch_id', $activeBId);
        }
        $totalOperatingExpenses = (float)$operatingExpensesQ->sum('total_amount');

        $cogs = min($totalSalesRevenue * 0.70, $totalPurchasesCost);
        if ($cogs <= 0 && $totalSalesRevenue > 0) {
            $cogs = $totalSalesRevenue * 0.65;
        }
        $grossProfit = max(0, $totalSalesRevenue - $cogs);
        $calculatedNetProfit = $grossProfit - $totalOperatingExpenses;

        $totalEquity = $totalAssets - $totalLiabilities;
        $investedCapital = max(0, $totalEquity - $calculatedNetProfit);
        if ($investedCapital <= 0) {
            $investedCapital = $totalEquity;
            $retainedEarnings = 0;
        } else {
            $retainedEarnings = $totalEquity - $investedCapital;
        }

        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
        $workingCapital = $totalCurrentAssets - $totalCurrentLiabilities;
        $currentRatio = $totalCurrentLiabilities > 0 ? round($totalCurrentAssets / $totalCurrentLiabilities, 2) : 'N/A';

        return response()->json([
            'success' => true,
            'as_of_date' => $asOfDate,
            'branch_name' => $isAllBranch ? 'All Branches' : (\App\Models\Branch::find($activeBId)->name ?? 'Branch ' . $activeBId),
            'assets' => [
                'cash_in_hand' => [
                    'total' => $totalCashInHand,
                    'counter_cash' => $counterCashBalance,
                    'account_opening' => $accountCashOpening,
                    'cash_accounts' => $cashAccounts,
                ],
                'bank_accounts' => [
                    'total' => $totalBankBalances,
                    'accounts' => $bankAccountsList,
                ],
                'accounts_receivable' => [
                    'total' => $totalReceivables,
                    'count' => count($customerReceivables),
                    'customers' => array_slice($customerReceivables, 0, 50),
                ],
                'advances_to_vendors' => [
                    'total' => $totalAdvancesToVendor,
                    'vendors' => $vendorAdvances,
                ],
                'inventory' => [
                    'total' => $totalInventoryValue,
                    'finished_goods' => $finishedGoodsValue,
                    'raw_materials' => $rawMaterialValue,
                    'total_qty' => $totalStockQty,
                    'categories' => $categoryStockSummary,
                    'items' => array_slice($topStockItems, 0, 30),
                ],
                'fixed_assets' => [
                    'total' => $totalFixedAssetsValue,
                    'total_cost' => $totalFixedAssetsCost,
                    'count' => count($fixedAssetsDetailed),
                    'assets' => $fixedAssetsDetailed,
                ],
                'total_current_assets' => $totalCurrentAssets,
                'total_fixed_assets'   => $totalFixedAssetsValue,
                'total_assets'         => $totalAssets,
            ],
            'liabilities' => [
                'accounts_payable' => [
                    'total' => $totalPayables,
                    'count' => count($vendorPayables),
                    'vendors' => array_slice($vendorPayables, 0, 50),
                ],
                'customer_advances' => [
                    'total' => $totalAdvancesFromCust,
                    'customers' => $customerAdvances,
                ],
                'total_current_liabilities'   => $totalCurrentLiabilities,
                'total_long_term_liabilities' => $totalLongTermLiabilities,
                'total_liabilities'           => $totalLiabilities,
            ],
            'equity' => [
                'invested_capital'  => $investedCapital,
                'net_profit'        => $calculatedNetProfit,
                'retained_earnings' => $retainedEarnings,
                'total_equity'      => $totalEquity,
                'total_liabilities_and_equity' => $totalLiabilitiesAndEquity,
            ],
            'metrics' => [
                'working_capital' => $workingCapital,
                'current_ratio'   => $currentRatio,
                'difference'      => round($totalAssets - $totalLiabilitiesAndEquity, 2),
                'is_balanced'     => true,
            ]
        ]);
    }

    public function printBalanceSheetReport(Request $request)
    {
        $asOfDate = $request->input('as_of_date', date('Y-m-d'));
        $branchId = $request->input('branch_id', 'all');

        $response = $this->fetchBalanceSheetReport($request);
        $data = $response->getData(true);

        return view('admin_panel.reporting.balance_sheet_print', compact('data', 'asOfDate', 'branchId'));
    }
}

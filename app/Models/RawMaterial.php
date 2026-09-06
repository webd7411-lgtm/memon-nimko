<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $guarded = [];

    public function stock()
    {
        return $this->hasOne(RawMaterialStock::class);
    }

    public function stocks()
    {
        return $this->hasMany(RawMaterialStock::class);
    }

    public function currentStock(?int $warehouseId = null, ?int $branchId = null)
    {
        if (!$branchId) {
            $branchId = session('active_branch_id') === 'all' ? null : active_branch_id();
        }
        $query = $this->stocks();
        if ($branchId) $query->where('branch_id', $branchId);
        if ($warehouseId) {
            $st = $query->where('warehouse_id', $warehouseId)->first();
            return $st ? (float)$st->qty : 0;
        }
        $sum = $query->sum('qty');
        return (float)($sum ?? 0);
    }

    public function lastPurchaseCost()
    {
        $cost = (float)(\App\Models\RawMaterialPurchaseItem::where('raw_material_id', $this->id)
            ->latest('id')
            ->value('price_per_unit') ?? 0);
        return $cost;
    }
}

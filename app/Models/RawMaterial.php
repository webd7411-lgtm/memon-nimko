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
        if ($warehouseId) {
            $st = $this->stocks()->where('warehouse_id', $warehouseId)->first();
            return $st ? (float)$st->qty : 0;
        }

        // Branch / Shop stock (strictly exclude warehouse stocks)
        if (!$branchId) {
            $branchId = session('active_branch_id') === 'all' ? null : active_branch_id();
        }
        $query = $this->stocks()->where(function($q) {
            $q->whereNull('warehouse_id')->orWhere('warehouse_id', 0);
        });
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        $sum = $query->sum('qty');
        return (float)($sum ?? 0);
    }

    public function branchStock(?int $branchId = null)
    {
        return $this->currentStock(null, $branchId);
    }

    public function warehouseStock(?int $warehouseId = null)
    {
        $query = $this->stocks()->whereNotNull('warehouse_id')->where('warehouse_id', '>', 0);
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        return (float)($query->sum('qty') ?? 0);
    }

    public function totalStock()
    {
        return (float)($this->stocks()->sum('qty') ?? 0);
    }

    public function lastPurchaseCost()
    {
        $cost = (float)(\App\Models\RawMaterialPurchaseItem::where('raw_material_id', $this->id)
            ->latest('id')
            ->value('price_per_unit') ?? 0);
        return $cost;
    }
}

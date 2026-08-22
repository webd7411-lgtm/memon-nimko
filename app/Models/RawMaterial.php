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

    public function currentStock(?int $warehouseId = null)
    {
        if ($warehouseId) {
            $st = $this->stocks()->where('warehouse_id', $warehouseId)->first();
            return $st ? (float)$st->qty : 0;
        }
        $sum = $this->stocks()->sum('qty');
        return $sum > 0 ? (float)$sum : ($this->stock ? (float)$this->stock->qty : 0);
    }

    public function lastPurchaseCost()
    {
        return \App\Models\RawMaterialPurchaseItem::where('raw_material_id', $this->id)
            ->latest('id')
            ->value('price_per_unit') ?? 0;
    }
}

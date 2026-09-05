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

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    public function currentStock(?int $warehouseId = null)
    {
        if ($warehouseId) {
            $st = $this->stocks()->where('warehouse_id', $warehouseId)->first();
            return $st ? (float)$st->qty : 0;
        }
        $sum = $this->stocks()->sum('qty');
        return (float)($sum ?? 0);
    }

    public function lastPurchaseCost()
    {
        $cost = (float)(\App\Models\RawMaterialPurchaseItem::where('raw_material_id', $this->id)
            ->latest('id')
            ->value('price_per_unit') ?? 0);
        // Return purchase price per purchase unit (kg, litre, etc.) - NOT converted to consumption unit
        return $cost;
    }
}

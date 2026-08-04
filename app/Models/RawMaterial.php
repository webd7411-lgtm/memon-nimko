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

    public function currentStock()
    {
        return $this->stock ? $this->stock->qty : 0;
    }

    public function lastPurchaseCost()
    {
        return \App\Models\RawMaterialPurchaseItem::where('raw_material_id', $this->id)
            ->latest('id')
            ->value('price_per_unit') ?? 0;
    }
}

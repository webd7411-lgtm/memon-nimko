<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterialPurchase extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(RawMaterialPurchaseItem::class, 'purchase_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

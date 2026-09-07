<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionEntryItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'qty_entered' => 'float',
        'qty_kg'      => 'float',
        'qty_stock'   => 'float',
    ];

    public function productionEntry()
    {
        return $this->belongsTo(ProductionEntry::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}

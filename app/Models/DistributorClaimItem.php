<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorClaimItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'qty'       => 'float',
        'unit_rate' => 'float',
        'subtotal'  => 'float',
    ];

    public function claim()
    {
        return $this->belongsTo(DistributorClaim::class, 'distributor_claim_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}

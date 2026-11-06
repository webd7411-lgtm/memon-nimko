<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorCustomerTier extends Model
{
    use HasFactory;

    protected $table = 'distributor_customer_tiers';

    protected $guarded = [];

    protected $casts = [
        'discount_percentage'  => 'float',
        'trade_offer_buy_qty'  => 'float',
        'trade_offer_free_qty' => 'float',
    ];

    /**
     * Relationship: Customers belonging to this tier
     */
    public function customers()
    {
        return $this->hasMany(DistributorCustomer::class, 'tier_id');
    }

    /**
     * Human-friendly scheme label e.g. "Buy 12 Get 1 Free"
     */
    public function getSchemeLabelAttribute()
    {
        if ($this->trade_offer_buy_qty > 0 && $this->trade_offer_free_qty > 0) {
            return "Buy {$this->trade_offer_buy_qty} Get {$this->trade_offer_free_qty} Free";
        }
        return "No Scheme (0 Free)";
    }
}

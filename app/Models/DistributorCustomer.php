<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorCustomer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'opening_balance'      => 'float',
        'current_balance'      => 'float',
        'approved_discount'    => 'float',
        'trade_offer_buy_qty'  => 'float',
        'trade_offer_free_qty' => 'float',
    ];

    public function tier()
    {
        return $this->belongsTo(DistributorCustomerTier::class, 'tier_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function booker()
    {
        return $this->belongsTo(User::class, 'booker_user_id');
    }

    public function sales()
    {
        return $this->hasMany(DistributorSale::class);
    }

    public function ledgers()
    {
        return $this->hasMany(DistributorCustomerLedger::class);
    }

    /**
     * Effective company-assigned discount percentage.
     * Uses approved_discount if explicitly set (>0), otherwise falls back to tier discount.
     */
    public function getEffectiveDiscountAttribute()
    {
        if ($this->approved_discount !== null && (float)$this->approved_discount > 0) {
            return (float)$this->approved_discount;
        }
        return (float)($this->tier?->discount_percentage ?? 0.00);
    }

    /**
     * Effective Buy Qty for Trade Offer Scheme
     */
    public function getEffectiveTradeOfferBuyQtyAttribute()
    {
        if ($this->trade_offer_buy_qty !== null && (float)$this->trade_offer_buy_qty > 0) {
            return (float)$this->trade_offer_buy_qty;
        }
        return (float)($this->tier?->trade_offer_buy_qty ?? 0.00);
    }

    /**
     * Effective Free Qty for Trade Offer Scheme
     */
    public function getEffectiveTradeOfferFreeQtyAttribute()
    {
        if ($this->trade_offer_free_qty !== null && (float)$this->trade_offer_free_qty > 0) {
            return (float)$this->trade_offer_free_qty;
        }
        return (float)($this->tier?->trade_offer_free_qty ?? 0.00);
    }

    /**
     * Scheme display text e.g. "Buy 12 Get 1 Free"
     */
    public function getSchemeLabelAttribute()
    {
        $buy = $this->effective_trade_offer_buy_qty;
        $free = $this->effective_trade_offer_free_qty;
        if ($buy > 0 && $free > 0) {
            return "Buy " . rtrim(rtrim(number_format($buy, 2), '0'), '.') . " Get " . rtrim(rtrim(number_format($free, 2), '0'), '.') . " Free";
        }
        return "No Scheme";
    }

    /**
     * Calculate exact trade offer free quantity for a given purchase quantity.
     * Guaranteed exact match: distributor cannot give more or less.
     */
    public function calculateTradeOffer($qty)
    {
        $buy = (float) $this->effective_trade_offer_buy_qty;
        $free = (float) $this->effective_trade_offer_free_qty;
        $qty = (float) $qty;

        if ($buy > 0 && $free > 0 && $qty >= $buy) {
            return floor($qty / $buy) * $free;
        }
        return 0.00;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(DistributorOrder::class);
    }

    public function stocks()
    {
        return $this->hasMany(DistributorStock::class);
    }

    public function customers()
    {
        return $this->hasMany(DistributorCustomer::class);
    }

    public function sales()
    {
        return $this->hasMany(DistributorSale::class);
    }

    public function ledgers()
    {
        return $this->hasMany(DistributorLedger::class);
    }

    public function payments()
    {
        return $this->hasMany(DistributorPayment::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function bookers()
    {
        return $this->hasMany(User::class, 'distributor_id')->where('usertype', 'order_booker');
    }

    public function shopBookings()
    {
        return $this->hasMany(DistributorShopBooking::class);
    }

    public function claims()
    {
        return $this->hasMany(DistributorClaim::class);
    }

    public static function generateCode()
    {
        $last = self::latest('id')->first();
        $num = $last ? ($last->id + 1) : 1;
        return 'DIST-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}

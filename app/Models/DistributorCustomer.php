<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorCustomer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function sales()
    {
        return $this->hasMany(DistributorSale::class);
    }

    public function ledgers()
    {
        return $this->hasMany(DistributorCustomerLedger::class);
    }
}

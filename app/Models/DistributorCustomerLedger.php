<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorCustomerLedger extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(DistributorCustomer::class, 'distributor_customer_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}

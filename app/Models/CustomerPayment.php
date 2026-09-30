<?php

// app/Models/CustomerPayment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    protected $guarded = [];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cardAccount()
    {
        return $this->belongsTo(Account::class, 'card_account_id');
    }
}

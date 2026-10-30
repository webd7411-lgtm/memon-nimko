<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id', // null = Super Admin / Head Office, > 0 = Distributor
        'head_id',        // foreign key: account head
        'account_code',   // account code
        'title',          // account title
        'account_number', // account number / IBAN
        'bank_name',      // bank name
        'branch_name',    // branch name
        'type',           // Debit / Credit
        'status',         // active/inactive
        'opening_balance',
    ];

    // Relation with AccountHead
    public function head()
    {
        return $this->belongsTo(AccountHead::class, 'head_id');
    }

    // Relation with Distributor
    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorLedger extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

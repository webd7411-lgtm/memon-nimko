<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'maintenance_date',
        'service_type',
        'cost',
        'performed_by',
        'next_service_date',
        'description',
        'parts_replaced',
        'status',
        'created_by',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'next_service_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

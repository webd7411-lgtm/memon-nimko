<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionEntry extends Model
{
    protected $guarded = [];

    protected $casts = [
        'production_date' => 'date',
    ];

    public static function generateEntryNo()
    {
        $prefix = 'PROD-' . date('Y') . '-';

        $lastEntry = self::orderBy('id', 'desc')->first();
        $lastNumber = 0;

        if ($lastEntry && $lastEntry->entry_no) {
            $lastNumber = (int) substr($lastEntry->entry_no, strlen($prefix));
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $newNumber;
    }

    public function items()
    {
        return $this->hasMany(ProductionEntryItem::class);
    }

    public function rawMaterialUsage()
    {
        return $this->hasMany(ProductionRawMaterialUsage::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}

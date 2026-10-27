<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_code',
        'name',
        'category_id',
        'branch_id',
        'warehouse_id',
        'location_details',
        'vendor_id',
        'purchase_date',
        'purchase_cost',
        'current_value',
        'salvage_value',
        'useful_life_years',
        'depreciation_rate',
        'model_no',
        'serial_no',
        'warranty_expiry',
        'custodian_name',
        'condition',
        'status',
        'disposal_date',
        'disposal_amount',
        'disposal_reason',
        'image',
        'invoice_doc',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'disposal_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'current_value' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
    ];

    /**
     * Generate unique auto asset code
     */
    public static function generateAssetCode($categoryId = null): string
    {
        $prefix = 'AST';
        if ($categoryId) {
            $cat = AssetCategory::find($categoryId);
            if ($cat && !empty($cat->code)) {
                $prefix .= '-' . strtoupper($cat->code);
            }
        }

        $latest = self::withTrashed()
            ->where('asset_code', 'LIKE', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest->asset_code, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate current book value based on straight line depreciation
     */
    public function getEstimatedCurrentValueAttribute(): float
    {
        if ($this->status === 'disposed' || $this->status === 'scrapped') {
            return (float) ($this->disposal_amount ?? 0);
        }

        if (!$this->purchase_date || $this->purchase_cost <= 0) {
            return (float) $this->purchase_cost;
        }

        $yearsPassed = now()->diffInDays($this->purchase_date) / 365.25;
        $rate = (float) $this->depreciation_rate;

        if ($rate <= 0 && $this->useful_life_years > 0) {
            $rate = 100 / $this->useful_life_years;
        }

        $totalDepreciationPct = min(100, $yearsPassed * $rate);
        $depreciableAmount = max(0, (float)$this->purchase_cost - (float)$this->salvage_value);
        $depreciatedValue = $depreciableAmount * ($totalDepreciationPct / 100);

        return max((float)$this->salvage_value, (float)$this->purchase_cost - $depreciatedValue);
    }

    // Relations
    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class, 'asset_id')->orderBy('maintenance_date', 'desc');
    }

    public function transfers()
    {
        return $this->hasMany(AssetTransfer::class, 'asset_id')->orderBy('transfer_date', 'desc');
    }
}

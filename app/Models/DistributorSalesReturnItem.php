<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorSalesReturnItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function salesReturn()
    {
        return $this->belongsTo(DistributorSalesReturn::class, 'distributor_sales_return_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(DistributorSaleItem::class, 'distributor_sale_item_id');
    }
}

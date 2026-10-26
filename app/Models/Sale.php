<?php

// app/Models/Sale.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $guarded = [];
    protected $casts = [
        'total_items' => 'float',
    ];

    public static function generateInvoiceNo()
    {
        $prefix = 'SAL-';

        $lastInvoice = self::orderBy('id', 'desc')->first();
        $lastNumber = 0;

        if ($lastInvoice && $lastInvoice->invoice_no) {
            $lastNumber = (int)substr($lastInvoice->invoice_no, strlen($prefix));
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $newNumber;
    }

    public function getCustomerDisplayNameAttribute()
    {
        if (empty($this->customer) || $this->customer === "Walk-in Customer") {
            return "Walk-in Customer";
        }
        if ($this->customer_relation) {
            return $this->customer_relation->customer_name;
        }
        if (str_starts_with($this->customer, "dist_")) {
            $id = (int) str_replace("dist_", "", $this->customer);
            $dist = \App\Models\Distributor::find($id);
            return $dist ? $dist->name . " (Distributor)" : "Distributor #" . $id;
        }
        if (str_starts_with($this->customer, "booker_")) {
            $id = (int) str_replace("booker_", "", $this->customer);
            $booker = \App\Models\Distributor::find($id);
            return $booker ? $booker->name . " (Order Booker)" : "Order Booker #" . $id;
        }
        if (str_starts_with($this->customer, "officer_")) {
            $id = (int) str_replace("officer_", "", $this->customer);
            $so = \App\Models\SalesOfficer::find($id);
            return $so ? $so->name . " (Sales Officer)" : "Sales Officer #" . $id;
        }
        if (str_starts_with($this->customer, "dc_")) {
            $id = (int) str_replace("dc_", "", $this->customer);
            $dc = \App\Models\DistributorCustomer::find($id);
            return $dc ? $dc->shop_name . " (" . $dc->owner_name . ")" : "Retail Shop #" . $id;
        }
        if (is_numeric($this->customer)) {
            $c = \App\Models\Customer::find($this->customer);
            if ($c) return $c->customer_name;
        }
        return $this->customer;
    }

    public function customer_relation()
    {
        return $this->belongsTo(Customer::class, 'customer', 'id');
    }

    public function product_relation()
    {
        return $this->belongsTo(Product::class, 'product', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function returns()
    {
        return $this->hasMany(SalesReturn::class, 'sale_id');
    }

    public function cardAccount()
    {
        return $this->belongsTo(Account::class, 'card_account_id');
    }
}


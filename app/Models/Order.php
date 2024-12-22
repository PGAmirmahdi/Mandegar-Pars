<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];
    const STATUS = [
        'orders' => 'ثبت سفارش',
        'pending' => 'پیش فاکتور شده',
        'invoiced' => 'فاکتور شده',
    ];

    const REQ_FOR = [
        'pre-invoice' => 'پیش فاکتور',
        'invoice' => 'فاکتور',
        'amani-invoice' => 'فاکتور امانی',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function action()
    {
        return $this->hasOne(OrderAction::class);
    }
    public function order_status()
    {
        return $this->hasMany(CustomerOrderStatus::class);
    }

    public function getNetAmount()
    {
        $productsData = json_decode($this->products);

        $total = 0;


        if (isset($productsData->products)) {
            foreach ($productsData->products as $product) {
                $total += ($product->counts * $product->prices);
            }
        }

        if (isset($productsData->other_products)) {
            foreach ($productsData->other_products as $otherProduct) {
                $total += ($otherProduct->other_counts * $otherProduct->other_prices);
            }
        }

        return $total;
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_id');
    }


}

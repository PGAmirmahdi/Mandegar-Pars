<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS = [
        'order' => 'ثبت سفارش',
        'orders' => 'ثبت سفارش',
        'pending' => 'پیش فاکتور شده',
        'invoiced' => 'فاکتور شده',
//        'return' => 'عودت داده شده',
    ];

    const REQ_FOR = [
        'pre-invoice' => 'پیش فاکتور',
        'invoice' => 'فاکتور',
        'amani-invoice' => 'فاکتور امانی',
    ];
    public function getTotalPriceAttribute()
    {
        $total = ($this->products ? $this->products->sum(function ($product) {
                return $product->pivot->total_price;
            }) : 0) + ($this->other_products ? $this->other_products->sum('total_price') : 0);

        $tax = $total * 0.10; // محاسبه ۱۰ درصد مالیات
        return $total + $tax; // اضافه کردن مالیات به کل قیمت
    }

    public function transports()
    {
        return $this->hasMany(Transport::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot([
            'color',
            'count',
            'unit',
            'price',
            'total_price',
            'discount_amount',
            'extra_amount',
            'tax',
            'invoice_net',
        ]);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class);
    }

    public function packet()
    {
        return $this->hasOne(Packet::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function factor()
    {
        return $this->hasOne(Factor::class);
    }

    public function other_products()
    {
        return $this->hasMany(OtherProduct::class);
    }

    public function sale_reports()
    {
        return $this->hasMany(SaleReport::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function order_status()
    {
        return $this->hasMany(OrderStatus::class);
    }

    public function action()
    {
        return $this->hasOne(InvoiceAction::class);
    }

    public function inventory_report()
    {
        return $this->hasOne(InventoryReport::class);
    }

    public function getNetAmount()
    {
        return $this->products()->sum('invoice_net') + $this->other_products()->sum('invoice_net');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
}

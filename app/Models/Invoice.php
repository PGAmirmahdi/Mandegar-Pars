<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS = [
        'pending' => 'در دست اقدام',
        'invoiced' => 'فاکتور شده',
//        'return' => 'عودت داده شده',
    ];

    const CREATED_IN = [
        'website' => 'وبسایت',
        'automation' => 'اتوماسیون',
    ];

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

    public function getNetAmount()
    {
        return $this->products()->sum('invoice_net') + $this->other_products()->sum('invoice_net');
    }
}

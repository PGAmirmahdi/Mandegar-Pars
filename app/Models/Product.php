<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    const COLORS = [
        'black' => 'مشکی'
    ];

    const UNITS = [
        'number' => 'عدد'
    ];

    const PRICE_TYPE = [
        'system_price' => 'قیمت سامانه',
        'partner_price_tehran' => 'قیمت همکار - تهران',
        'partner_price_other' => 'قیمت همکار - شهرستان',
        'single_price' => 'قیمت تک فروشی',
        'market_price' => 'قیمت بازار',
        'domestic_price' => 'قیمت داخلی',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function printers()
    {
        return $this->belongsToMany(Printer::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class)->withPivot([
            'count',
            'color',
            'unit',
            'price',
            'total_price',
            'discount_amount',
            'extra_amount',
            'tax',
            'invoice_net',
        ]);
    }

    public function histories()
    {
        return $this->hasMany(PriceHistory::class);
    }

    public function getPrice()
    {
        if (auth()->user()->hasPermission('system-user')){
            return $this->system_price;
        }elseif (auth()->user()->hasPermission('partner-other-user')){
            return $this->partner_price_other;
        }elseif (auth()->user()->hasPermission('partner-tehran-user')){
            return $this->partner_price_tehran;
        }else{
            return $this->single_price;
        }
    }
}

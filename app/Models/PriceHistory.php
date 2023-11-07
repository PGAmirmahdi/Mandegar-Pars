<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    const FIELDS = [
        'system_price' => 'قیمت سامانه',
        'partner_price_tehran' => 'قیمت همکار - تهران',
        'partner_price_other' => 'قیمت همکار - شهرستان',
        'single_price' => 'قیمت تک فروشی',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

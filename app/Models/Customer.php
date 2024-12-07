<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    const TYPE = [
        'government' => 'سازمانی/دولتی/نیروهای مسلح',
        'private' => 'بازار/تهران/شهرستان',
    ];

    const CUSTOMER_TYPE = [
        'system' => 'سامانه',
        'tehran' => 'تهران',
        'city' => 'شهرستان',
        'single-sale' => 'تک فروشی',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function buy_orders()
    {
        return $this->hasMany(BuyOrder::class);
    }
}

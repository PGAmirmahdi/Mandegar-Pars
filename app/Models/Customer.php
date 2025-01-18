<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    const TYPE = [
        'Industrial'=>'واحد های صنعتی',
        'Organizations' => 'سازمانها',
        'Global' => 'سراسری',
        'government' => 'سازمانی/دولتی/نیروهای مسلح',
        'private' => 'بازار/تهران/شهرستان',
    ];

    const CUSTOMER_TYPE = [
        'tehran' => 'تهران',
        'city' => 'شهرستان',
        'single-sale' => 'تک فروشی',
        'setad' => 'سامانه ستاد',
        'system' => 'سامانه(ام پی سیستم)',
        'online-sale' => 'فروش اینترنتی',
        'free-sale' => 'آزاد (بازار)',
    ];
    public function debtor()
    {
        return $this->hasMany(Debtor::class);
    }
    public function setadRequest()
    {
        return $this->hasMany(SalePriceRequest::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function buy_orders()
    {
        return $this->hasMany(BuyOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

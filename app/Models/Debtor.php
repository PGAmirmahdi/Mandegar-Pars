<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debtor extends Model
{
    use HasFactory;
    protected $guarded = [];


    /**
     * رابطه با مدل Customer
     * یک بدهکار به یک مشتری تعلق دارد.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * دسترسی به وضعیت بدهی به صورت توصیفی
     */
    const STATUS = [
        'unpaid' => 'پرداخت نشده',
        'paid' => 'پرداخت شده',
        'partial' => 'پرداخت ناقص',
    ];
    /**
     * فرمت قیمت برای نمایش
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price) . ' ریال';
    }
}

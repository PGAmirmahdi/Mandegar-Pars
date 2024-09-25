<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = true;

    const STATUS = [
        'register' => 'ثبت سفارش',
        'processing' => 'آماده سازی سفارش',
        'out' => 'خروج از انبار',
        'exit_door' => 'تایید درب خروج',
        'sending' => 'درحال ارسال',
        'delivered' => 'تحویل به مشتری',
    ];

    const ORDER = [
        1 => 'register',
        2 => 'processing',
        3 => 'out',
        4 => 'exit_door',
        5 => 'sending',
        6 => 'delivered',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

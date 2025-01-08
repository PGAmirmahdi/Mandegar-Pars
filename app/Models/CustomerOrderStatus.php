<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOrderStatus extends Model
{
    use HasFactory;

    protected $guarded = [];


    const STATUS = [
        'register' => 'ثبت سفارش مشتری',
        'processing_by_accountant_step_1' => 'در انتظار بررسی توسط حسابدار',
        'pre_invoice' => 'صدور پیش فاکتور',
        'awaiting_confirm_by_sales_manager' => 'در انتظار تایید توسط همکار فروش',
        'setad_fee' => 'ثبت کارمزد ستاد',
        'processing_by_accountant_step_2' => 'در انتظار بررسی توسط حسابدار',
        'upload_setad_fee' => 'آپلود رسید کارمزد',
        'send_invoice' => 'صدور فاکتور',
    ];


    const ORDER = [
        1 => 'register',
        2 => 'processing_by_accountant_step_1',
        3 => 'pre_invoice',
        4 => 'awaiting_confirm_by_sales_manager',
        5 => 'setad_fee',
        6 => 'processing_by_accountant_step_2',
        7 => 'upload_setad_fee',
        8 => 'send_invoice',
    ];
    const ORDER_OTHER = [
        1 => 'register',
        2 => 'processing_by_accountant_step_1',
        3 => 'pre_invoice',
        4 => 'awaiting_confirm_by_sales_manager',
        8 => 'send_invoice',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

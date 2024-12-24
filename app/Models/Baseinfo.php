<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Baseinfo extends Model
{
    use HasFactory;
    protected $guarded = [];

    const TYPE = [
        'base' => 'اطلاعات پایه',
        'manager' => 'اطلاعات مدیرعامل',
        'bank' => 'اطلاعات حساب بانکی',
        'call' => 'اطلاعات تماس',
        'file' => 'اطلاعات فایل',
        'Irancell-bussiness' => 'شماره تلفن سازمانی'
    ];
    const ACCESS = [
        'public' => 'عمومی',
        'private' => 'خصوصی',
    ];
}

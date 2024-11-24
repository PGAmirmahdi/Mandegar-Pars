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
        'call' => 'اطلاعات تماس',
        'file' => 'اطلاعات فایل',
    ];
    const ACCESS = [
        'public' => 'عمومی',
        'private' => 'خصوصی',
    ];
}

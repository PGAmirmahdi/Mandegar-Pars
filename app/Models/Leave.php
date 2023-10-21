<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $guarded = [];

    const TYPE = [
        'daily' => 'روزانه',
        'hourly' => 'ساعتی',
    ];

    const STATUS = [
        'accept' => 'تایید شده',
        'reject' => 'رد شده',
        'pending' => 'در حال بررسی',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function acceptor()
    {
        return $this->belongsTo(User::class);
    }
}

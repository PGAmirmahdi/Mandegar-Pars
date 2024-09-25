<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS = [
        'pending' => 'درانتظار قیمت',
        'sent' => 'ثبت قیمت'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

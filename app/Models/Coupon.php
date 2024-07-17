<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class);
    }
    const  TYPE = [
        'once' => 'یکبار مصرف',
        'many' => 'چند بار مصرف',
    ];
    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}

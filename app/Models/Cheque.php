<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS = [
        'pending' => 'درانتظار ثبت درخواست چک',
        'sent' => 'ثبت درخواست بررسی چک'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

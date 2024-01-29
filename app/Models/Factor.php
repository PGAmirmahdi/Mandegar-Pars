<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factor extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS = [
        'invoiced' => 'فاکتور شده',
        'paid' => 'تسویه شده',
        'canceled' => 'ابطال شده',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

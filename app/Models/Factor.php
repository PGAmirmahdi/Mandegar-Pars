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
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function inventory_report()
    {
        return $this->hasOne(InventoryReport::class);
    }
}

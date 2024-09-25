<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guarantee extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS = [
        'inactive' => 'غیرفعال',
        'active' => 'فعال',
        'voided' => 'باطل شده',
        'expired' => 'منقضی شده',
    ];

    const PERIOD = [
        '12' => '12 ماهه',
        '24' => '24 ماهه',
    ];

    public function inventory_report()
    {
        return $this->hasOne(InventoryReport::class);
    }
}

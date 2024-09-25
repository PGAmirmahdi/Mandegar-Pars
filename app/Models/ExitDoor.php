<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExitDoor extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'exit_door';

    const STATUS = [
        'confirmed' => 'تایید شده',
        'not_confirmed' => 'تایید نشده',
    ];

    public function inventory_report()
    {
        return $this->belongsTo(InventoryReport::class);
    }
}

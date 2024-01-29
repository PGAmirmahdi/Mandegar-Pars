<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function in_outs()
    {
        return $this->hasMany(InOut::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function exit_door()
    {
        return $this->hasOne(ExitDoor::class);
    }

    public function guarantee()
    {
        return $this->belongsTo(Guarantee::class);
    }
}

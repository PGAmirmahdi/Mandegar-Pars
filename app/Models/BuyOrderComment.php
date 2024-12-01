<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyOrderComment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function buyOrder()
    {
        return $this->belongsTo(BuyOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

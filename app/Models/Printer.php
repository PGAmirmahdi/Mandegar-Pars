<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    use HasFactory;

    protected $guarded = [];

    const BRANDS = [
        'HP',
        'Canon',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    const NAMES = [
        'printer' => 'پرینتر'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function models()
    {
        return $this->hasMany(ProductModel::class);
    }
    public function suplliers()
    {
        return $this->hasMany(Supplier::class);
    }
}

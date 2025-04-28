<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analyse extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function products()
    {
        return $this->belongsToMany(Product::class, 'analyse_products')
            ->withPivot(['quantity','storage_count','sold_count'])
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function brand()
    {
        return $this->belongsTo(ProductModel::class);
    }
    public function analyseProducts()
    {
        return $this->hasMany(AnalyseProducts::class);
    }
}

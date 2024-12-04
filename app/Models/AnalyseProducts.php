<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyseProducts extends Model
{
    use HasFactory;

    protected $fillable = ['analyse_id', 'product_id', 'quantity'];

    public function analyse()
    {
        return $this->belongsTo(Analyse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

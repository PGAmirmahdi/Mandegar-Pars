<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventorySnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'stock_count',
        'snapshot_date',
        'warehouse_id'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}

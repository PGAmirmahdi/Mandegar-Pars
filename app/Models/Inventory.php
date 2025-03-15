<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $guarded = [];

    const TYPE = [
        'main_box' => 'جعبه مادر',
        'cartridge_box' => 'جعبه کارتریج',
        'cartridge' => 'کارتریج',
        'wide_tape' => 'چسب پهن',
        'hot_glue' => 'چسب حرارتی',
        'ribbon' => 'ریبون',
        'label' => 'لیبل',
        'drum' => 'درام',
        'paper'=>'کاغذ',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function in_outs()
    {
        return $this->hasMany(InOut::class);
    }
    // تعریف ارتباط با جدول products
    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
    public function getInputCount()
    {
        $inventory_report_id = InventoryReport::where('type','input')->pluck('id');
        return $this->in_outs()->whereIn('inventory_report_id', $inventory_report_id)->sum('count');
    }

    public function getOutputCount()
    {
        $inventory_report_id = InventoryReport::where('type','output')->pluck('id');
        return $this->in_outs()->whereIn('inventory_report_id', $inventory_report_id)->sum('count');
    }
    public function inventoryReport()
    {
        return $this->belongsTo(InventoryReport::class, 'inventory_report_id');
    }

}

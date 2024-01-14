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
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function in_outs()
    {
        return $this->hasMany(InOut::class);
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
}

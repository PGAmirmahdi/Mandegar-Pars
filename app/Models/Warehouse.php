<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function getInitialCount()
    {
        return $this->inventories()->sum('initial_count');
    }

    public function getCurrentCount()
    {
        return $this->inventories()->sum('current_count');
    }

    public function getInputCount()
    {
        $inventory_report_id = InventoryReport::where('type','input')->pluck('id');
        $inventories_id = $this->inventories()->pluck('id');

        return InOut::whereIn('inventory_id',$inventories_id)->whereIn('inventory_report_id', $inventory_report_id)->sum('count');
    }

    public function getOutputCount()
    {
        $inventory_report_id = InventoryReport::where('type','output')->pluck('id');
        $inventories_id = $this->inventories()->pluck('id');

        return InOut::whereIn('inventory_id',$inventories_id)->whereIn('inventory_report_id', $inventory_report_id)->sum('count');
    }
}

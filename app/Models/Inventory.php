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
    ];

    public function reports()
    {
        return $this->hasMany(InventoryReport::class);
    }
}

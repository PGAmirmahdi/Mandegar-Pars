<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    const COLORS = [
        'black' => 'مشکی'
    ];

    const UNITS = [
        'number' => 'عدد'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function printers()
    {
        return $this->belongsToMany(Printer::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class)->withPivot([
            'count',
            'color',
            'unit',
            'price',
            'total_price',
            'discount_amount',
            'extra_amount',
            'tax',
            'invoice_net',
        ]);
    }

    public function getPrice()
    {
        if (auth()->user()->hasPermission('system-user')){
            return $this->system_price;
        }elseif (auth()->user()->hasPermission('partner-other-user')){
            return $this->partner_price_other;
        }elseif (auth()->user()->hasPermission('partner_price_tehran')){
            return $this->partner_price_tehran;
        }else{
            return $this->single_price;
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    const COLORS = [
        'black' => 'مشکی',
        'white' => 'سفید',
        'red' => 'قرمز',
        'yellow' => 'زرد',
        'blue' => 'آبی',
        'green' => 'سبز',
    ];
    const STATUS = [
        'approved' => 'تایید شده',
        'pending' => 'منتظر تایید',
        'rejected' => 'رد شده',
    ];
    const UNITS = [
        'number' => 'عدد',
        'pack' => 'بسته',
        'box' => 'جعبه',
        'kg' => 'کیلوگرم',
        'ton ' => 'تن',
    ];

    const PRICE_TYPE = [
        'system_price' => 'قیمت سامانه',
        'partner_price_tehran' => 'قیمت همکار - تهران',
        'partner_price_other' => 'قیمت همکار - شهرستان',
        'single_price' => 'قیمت تک فروشی',
        'market_price' => 'قیمت بازار',
        'domestic_price' => 'قیمت داخلی',
    ];
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function mandegarprice()
    {
        return $this->hasMany(MandegarPrice::class);
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

    public function histories()
    {
        return $this->hasMany(PriceHistory::class);
    }

    public function getPrice()
    {
        if (auth()->user()->hasPermission('system-user')) {
            return $this->system_price;
        } elseif (auth()->user()->hasPermission('partner-other-user')) {
            return $this->partner_price_other;
        } elseif (auth()->user()->hasPermission('partner-tehran-user')) {
            return $this->partner_price_tehran;
        } else {
            return $this->single_price;
        }
    }

    public function productModels()
    {
        return $this->belongsTo(ProductModel::class, 'brand_id');
    }

    public function latestInventory()
    {
        // گرفتن آخرین موجودی برای این محصول
        return $this->inventories()->latest()->first()->current_count ?? 0;
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'analyse_products')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function analyses()
    {
        return $this->belongsToMany(Analyse::class, 'analyse_products')
            ->withPivot('quantity') // دسترسی به ستون quantity از جدول میانی
            ->withTimestamps();
    }

    public function cost()
    {
        return $this->hasMany(Cost::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}

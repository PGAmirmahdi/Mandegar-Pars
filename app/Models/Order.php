<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];
    const STATUS = [
        'orders' => 'ثبت سفارش',
        'pending' => 'پیش فاکتور شده',
        'invoiced' => 'فاکتور شده',
    ];
    const Payment_Type = [
        'cash'=>'نقدی',
        '1M'=>'یک ماهه',
        '2M'=>'دو ماهه',
        '3M'=>'سه ماهه',
        '4M'=>'چهار ماهه',
        '6M'=>'شش ماهه',
        '12M'=>'یک ساله',
        '24M'=>'دو ساله',
        '45D'=>'45 روزه',
        '45DW'=>'45 روز کاری',
        '15D'=>'15 روزه'
    ];
    const CREATED_IN = [
    'website' => 'وبسایت',
    'automation' => 'اتوماسیون',
    'app' => 'اپلیکیشن',
];

    const REQ_FOR = [
        'pre-invoice' => 'پیش فاکتور',
        'invoice' => 'فاکتور',
        'amani-invoice' => 'فاکتور امانی',
    ];
    public function getPaymentTypeTextAttribute()
    {
        return self::Payment_Type[$this->payment_type] ?? 'تعیین نشده';
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function action()
    {
        return $this->hasOne(OrderAction::class);
    }
    public function order_status()
    {
        return $this->hasMany(CustomerOrderStatus::class);
    }

    public function getNetAmount()
    {
        $productsData = json_decode($this->products);

        $total = 0;


        if (isset($productsData->products)) {
            foreach ($productsData->products as $product) {
                $total += ($product->counts * $product->prices);
            }
        }

        if (isset($productsData->other_products)) {
            foreach ($productsData->other_products as $otherProduct) {
                $total += ($otherProduct->other_counts * $otherProduct->other_prices);
            }
        }

        return $total;
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_id');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')
            ->withPivot(['count', 'price', 'total_price', 'color', 'discount_amount', 'extra_amount', 'tax', 'invoice_net'])
            ->withTimestamps();
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePriceRequest extends Model
{
    use HasFactory;

    protected $guarded = [];
    const STATUS = [
        'pending' => 'منتظر تایید',
        'accepted' => 'تایید شد',
        'rejected' => 'رد شد',
        'pending_result' => 'منتظر نتیجه ستاد',
        'finished' => 'بسته شده',
        'winner' => 'برنده',
        'lose' => 'برنده نشده',
    ];
    const TYPE = [
        'free_sale' => 'فروش آزاد',
        'global_sale' => 'فروش سراسری',
        'industrial_sale' => 'فروش صنعتی',
        'setad_sale' => 'فروش ستاد',
        'internet_sale' => 'فروش اینترنتی',
        'organization_sale' => 'فروش سازمانی'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function acceptor()
    {
        return $this->belongsTo(User::class);
    }
}

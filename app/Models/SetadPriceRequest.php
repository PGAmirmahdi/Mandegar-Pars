<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetadPriceRequest extends Model
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
        'lose' => 'برنده نشده'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function acceptor()
    {
        return $this->belongsTo(User::class, 'acceptor_id');
    }
}

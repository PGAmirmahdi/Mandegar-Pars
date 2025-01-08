<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetadPriceRequest extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function customer()
    {
        $this->belongsTo(Customer::class, 'customer_id');
    }
    public function user()
    {
        $this->belongsTo(User::class, 'user_id');
    }
    public function acceptor()
    {
        $this->belongsTo(Customer::class, 'acceptor_id');
    }
}

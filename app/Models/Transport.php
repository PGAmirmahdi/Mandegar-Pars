<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    use HasFactory;
    // فیلدهای قابل پر شدن
    protected $fillable = ['invoice_id', 'status', 'user_id'];
    const Payment_Type= [
        'prepaid' => 'پیش پرداخت',
        'paid'=>'پس پرداخت'
    ];

    // رابطه با مدل Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // رابطه با مدل Transporter
    public function transporter()
    {
        return $this->belongsTo(Transporter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // رابطه با جدول transport_items
    public function items()
    {
        return $this->hasMany(TransportItem::class, 'transport_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packet extends Model
{
    use HasFactory;

    protected $guarded = [];

    const PACKET_STATUS = [
        'sending' => 'در حال ارسال',
        'delivered' => 'تحویل شده',
    ];

    const INVOICE_STATUS = [
        'unknown' => 'نامشخص',
        'delivered' => 'تحویل شرکت',
    ];

    const SENT_TYPE = [
        'post' => 'پست',
        'tipax' => 'تیپاکس',
        'delivery' => 'پیک',
    ];

    const DELIVERY_VERIFY = [
        'confirmed' => 'تایید شده',
        'unconfirmed' => 'تایید نشده',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

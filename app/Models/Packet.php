<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packet extends Model
{
    use HasFactory;

    protected $guarded = [];

    const PACKET_STATUS = [
        'delivered' => 'تحویل شده',
        'sending' => 'در حال ارسال',
    ];

    const INVOICE_STATUS = [
        'delivered' => 'تحویل شرکت',
        'unknown' => 'نامشخص',
    ];

    const SENT_TYPE = [
        'post' => 'پست',
        'tipax' => 'تیپاکس',
        'delivery' => 'پیک',
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

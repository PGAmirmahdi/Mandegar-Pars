<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalTicket extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS = [
        'pending' => 'درحال بررسی',
        'closed' => 'بسته شده',
    ];


    const COMPANIES = [
        'parso' => 'پرسو تجارت ایرانیان',
        'barman' => 'بارمان سیستم سرزمین پارس',
        'adaktejarat' => 'آداک تجارت خورشید قشم',
        'adakhamrah' => 'آداک همراه خورشید قشم',
//        'mandegarpars' => 'ماشین های اداری ماندگار پارس',
        'adakpetro' => 'آداک پترو خورشید قشم',
        'adaksanat' => 'آداک صنعت خورشید قشم',
        'sayman' => 'فناوران رایانه سایمان داده',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}

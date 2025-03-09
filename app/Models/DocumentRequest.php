<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;
    protected $guarded = [];

    const STATUS = [
        'pending' => 'منتظر ارسال',
        'sent' =>'ارسال شده',
        'not-sent' =>'عدم ارسال',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function sender()
    {
        return $this->belongsTo(User::class);
    }
}

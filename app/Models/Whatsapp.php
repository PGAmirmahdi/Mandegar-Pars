<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Whatsapp extends Model
{
    use HasFactory;
    protected $table='whatsapp';
    protected $fillable = [
        'user_id',
        'sender_name',
        'receiver_name',
        'phone',
        'description',
        'status',
    ];
    const STATUS = [
        'successful' => 'موفق',
        'failed' => 'ناموفق'
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}

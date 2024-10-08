<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    // فیلدهایی که قابل پر کردن هستند
    protected $fillable = ['user_id', 'message', 'is_user_message'];

    /**
     * ارتباط با مدل User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

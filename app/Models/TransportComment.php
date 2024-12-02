<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TransportComment extends Model
{
    use HasFactory;

    protected $fillable = ['transporter_id', 'user_id', 'comment'];

    public function transporter()
    {
        return $this->belongsTo(Transporter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporter extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'address', 'code','phone'];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->code = self::generateCode();
        });
    }

    private static function generateCode()
    {
        return str_pad(rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
    }
}

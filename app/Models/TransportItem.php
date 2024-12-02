<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportItem extends Model
{
    protected $fillable = ['transport_id', 'transporter_id', 'price', 'payment_type'];

    // رابطه با مدل Transport
    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }

    // رابطه با مدل Transporter
    public function transporter()
    {
        return $this->belongsTo(Transporter::class);
    }
}


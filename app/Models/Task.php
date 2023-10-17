<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['status','done_at','description'])
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }
}

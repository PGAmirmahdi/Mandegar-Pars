<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $attributes = [
        'file_path' => '',
    ];
    protected $fillable = [
        'user_id', 'file_name', 'file_path', 'file_type', 'parent_folder_id'
    ];
    public function parentFolder()
    {
        return $this->belongsTo(File::class, 'parent_folder_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

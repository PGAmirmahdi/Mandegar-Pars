<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'family',
        'phone',
        'role_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin()
    {
        return $this->role->name == 'admin';
    }

    public function hasPermission(string $permission)
    {
        return $this->role->permissions->pluck('name')->contains($permission);
    }

    public function packets()
    {
        return $this->hasMany(Packet::class);
    }

    public function isSystemUser()
    {
        return $this->hasPermission('system-user');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class)
            ->withPivot(['id','status','done_at','description'])
            ->withTimestamps();
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function fullName()
    {
        return $this->name.' '.$this->family;
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function leavesCount()
    {
        $leave_info = DB::table('leave_info')->where('user_id', $this->id)->first();
        return $leave_info->count;
    }
}

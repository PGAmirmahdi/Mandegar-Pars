<?php

namespace App\Models;

use Carbon\Carbon;
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
        'sign_image',
        'password',
        'najva_token',
        'fcm_token'
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

    public function isWareHouseKeeper()
    {
        return $this->role->permissions->pluck('name')->contains('warehouse-keeper');
    }

    public function isAccountant()
    {
        return $this->role->permissions->pluck('name')->contains('accountant');
    }

    public function isCEO()
    {
        return $this->role->permissions->pluck('name')->contains('ceo');
    }

    public function isSalesManager()
    {
        return $this->role->permissions->pluck('name')->contains('sales-manager');
    }

    public function isExitDoor()
    {
        return $this->role->permissions->pluck('name')->contains('exit-door');
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
        $this->leavesUpdate();
        $leave_info = DB::table('leave_info')->where('user_id', $this->id)->first();
        return $leave_info->count;
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function price_requests()
    {
        return $this->hasMany(PriceRequest::class);
    }
    public function cheque()
    {
        return $this->hasMany(Cheque::class);
    }
    public function buy_orders()
    {
        return $this->hasMany(BuyOrder::class);
    }

    private function leavesUpdate()
    {
        $leave_info = DB::table('leave_info')->where('user_id', $this->id);

        $last_month_leaves = Leave::where(['user_id' => auth()->id(), 'type' => 'daily', 'status' => 'accept'])->get();
        $last_month_leaves_days = 0; // تعداد روز مرخصی های ماه قبل
        foreach ($last_month_leaves as $leave)
        {
            $form_date = Carbon::parse($leave->from_date);
            $to_date = Carbon::parse($leave->to_date);

            if ($form_date->diff($to_date)->days){
                $last_month_leaves_days += $form_date->diff($to_date)->days;
            }else{
                $last_month_leaves_days += 1;
            }
        }

        // افزودن روزهای جدید به ماه بعد و بررسی اینکه چند روز از ماه قبل برایش باقی مانده
        $month_updated = $leave_info->first()->month_updated;
        $current_month = verta()->month;
        if ($month_updated != $current_month){
            $new_month_count = 2;
            $remain = 0;

            if ($last_month_leaves_days >= 2){
                $remain = 0;
            }else{
                $remain = 1;
            }

            $leave_info->update([
                'month_updated' => $current_month,
                'count' => $leave_info->first()->count += ($new_month_count + $remain)
            ]);
        }
    }
}

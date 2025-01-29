<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query()->with('user'); // اضافه کردن رابطه کاربران

        // اعمال فیلتر بر اساس user_id اگر کاربر مشخص شده باشد
        if ($request->user && $request->user !== 'all') {
            $query->where('user_id', $request->user);
        }

        // دریافت فعالیت‌ها با مرتب‌سازی بر اساس جدیدترین فعالیت‌ها
        $activities = $query->latest()->paginate(10);

        return view('panel.activity.index', compact('activities'));
    }

    public function search(Request $request)
    {
        $users_id = $request->user == 'all' ? User::all()->pluck('id') : [$request->user];

        $activities = Activity::whereIn('user_id', $users_id)->latest()->paginate(10);

        return view('panel.activity.index', compact('activities'));
    }

}

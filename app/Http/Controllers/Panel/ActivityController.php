<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('activity-list');

        $query = Activity::query()->with('user');

        if ($request->user && $request->user !== 'all') {
            $query->where('user_id', $request->user);
        }

        // دریافت فعالیت‌ها با مرتب‌سازی بر اساس جدیدترین فعالیت‌ها
        $activities = $query->latest()->paginate(10);

        return view('panel.activity.index', compact('activities'));
    }

    public function search(Request $request)
    {
        $this->authorize('activity-list');
        // تعیین شناسه‌های کاربر بر اساس انتخاب
        $users_id = $request->user == 'all' ? User::all()->pluck('id') : [$request->user];
        $query = Activity::whereIn('user_id', $users_id);
        // فیلتر بر اساس بازه زمانی
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', Verta::parse($request->start_date)->toCarbon()->toDateTimeString());
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', Verta::parse($request->end_date)->toCarbon()->toDateTimeString() . ' 23:59:59');
        }

        $activities = $query->latest()->paginate(10);

        return view('panel.activity.index', compact('activities'));
    }

}

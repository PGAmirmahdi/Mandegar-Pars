<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query()->with('user'); // اضافه کردن رابطه کاربران
        // اگر جستجو انجام شده باشد
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $activities = $query->latest()->paginate(10);
        return view('panel.activity.index',compact('activities'));
    }
    public function destroy($id)
    {
        // بررسی مجوز حذف فعالیت
        $this->authorize('activity-delete');

        // پیدا کردن فعالیت بر اساس ID
        $activity = Activity::findOrFail($id);

        // حذف فعالیت
        $activity->delete();
        alert()->success('فعالیت مورد نظر باموفقیت حذف شد','حذف فعالیت');
        return route('activity');
    }

}

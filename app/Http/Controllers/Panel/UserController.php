<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Activity;
use App\Models\User;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            $users = User::latest()->paginate(10);
        } else {
            // در غیر این صورت، فقط کاربر جاری را بگیرید
            $users = User::where('id', auth()->user()->id)->paginate(10);
        }
        return view('panel.users.index', compact('users'));
    }

    public function create(User $user)
    {
        $this->authorize('users-create');

        return view('panel.users.create',compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // به‌روزرسانی اطلاعات کاربر
        $dataToUpdate = [
            'name' => $request->name,
            'family' => $request->family,
            'phone' => $request->phone,
            'role_id' => $request->role ?? $user->role_id,
            'gender' => $request->gender,
        ];

        // بروزرسانی رمز عبور در صورت ادمین بودن
        if (auth()->user()->isAdmin() && $request->filled('password')) {
            $dataToUpdate['password'] = bcrypt($request->password);
        }

        // آپلود و به‌روزرسانی فایل امضا در صورت موجود بودن
        if ($request->hasFile('sign_image')) {
            // حذف فایل قدیمی اگر وجود داشته باشد
            if ($user->sign_image) {
                Storage::disk('public')->delete($user->sign_image);
            }

            // ذخیره فایل جدید
            $filePath = upload_file($request->file('sign_image'), 'sign_images');
            $user->sign_image = $filePath;
        }

        // آپلود و به‌روزرسانی فایل پروفایل در صورت موجود بودن
        if ($request->hasFile('profile')) {
            // حذف فایل قدیمی اگر وجود داشته باشد
            if ($user->profile) {
                Storage::disk('public')->delete($user->profile);
            }

            // ذخیره فایل جدید
            $filePath = upload_file($request->file('profile'), 'profiles');
            $user->profile = $filePath;
        }

        // به‌روزرسانی کاربر در دیتابیس
        $user->update($dataToUpdate);
        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'description' => 'همکار ' . auth()->user()->family . '(' . Auth::user()->role->label . ')'  . ' اطلاعات همکار ' . $user->family . ' را ویرایش کرد',
            'action' => 'ویرایش همکار',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        // پیام موفقیت و بازگرداندن کاربر به صفحه لیست کاربران
        alert()->success('پروفایل شما با موفقیت ویرایش شد', 'ویرایش پروفایل');
        return redirect()->route('users.index');
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('users-create');

        // ایجاد کاربر جدید
        $user = User::create([
            'name' => $request->name,
            'family' => $request->family,
            'phone' => $request->phone,
            'role_id' => $request->role,
            'password' => bcrypt($request->password),
            'gender' => $request->gender,
        ]);

        // آپلود فایل پروفایل در صورت موجود بودن
        if ($request->hasFile('profile')) {
            // ذخیره فایل جدید
            $filePath = upload_file($request->file('profile'), 'profiles');
            $user->update(['profile' => $filePath]);
        }

        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'description' => 'همکار ' . auth()->user()->family . '(' . Auth::user()->role->label . ')'  . ' همکار جدیدی با نام ' . $user->family . ' ایجاد کرد',
            'action' => 'ایجاد همکار',
            'created_at' => now(),
        ];
        Activity::create($activityData);

        // ارسال پیام موفقیت
        alert()->success('همکار با موفقیت ساخته شد', 'ایجاد همکار');
        return redirect()->route('users.index');
    }



    public function show(User $user)
    {
        //
    }

    public function edit(User $user)
    {
        if (!auth()->user()->isAdmin()){
            if (!Gate::allows('edit-profile',$user->id)){
                abort(403);
            }
        }

        return view('panel.users.edit', compact('user'));
    }

    public function destroy(User $user)
    {
        $this->authorize('users-delete');
        $activityData = [
            'user_id' => auth()->id(),
            'description' => 'همکار ' . auth()->user()->family . '(' . Auth::user()->role->label . ')'  . ' همکار با نام ' . $user->family . ' را حذف کرد',
            'action' => 'حذف همکار',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        $user->delete();
        return back();
    }

    private function createLeaveInfo(User $user)
    {
        DB::table('leave_info')->insert([
            'user_id' => $user->id,
            'count' => 2,
            'month_updated' => verta()->month,
        ]);
    }
    public function uploadProfile(Request $request, User $user)
    {
        $this->authorize('users-edit');
        $activityData = [
            'user_id' => auth()->id(),
            'description' => 'همکار ' . auth()->user()->family . '(' . Auth::user()->role->label . ')'  . ' برای خود عکس پروفایل آپلود کرد ',
            'action' => 'آپلود عکس همکار',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        // مدیریت آپلود تصویر پروفایل
        if ($request->hasFile('profile')) {
            // حذف فایل قدیمی اگر وجود داشته باشد
            if ($user->profile) {
                Storage::disk('public')->delete($user->profile);
            }

            // ذخیره فایل جدید
            $filePath = upload_file($request->file('profile'), 'profiles');
            $user->profile = $filePath;
            $user->save();

            return response()->json(['message' => 'پروفایل با موفقیت به‌روز شد', 'redirect' => route('users.index')]);
        }

        return response()->json(['message' => 'فایلی برای آپلود وجود ندارد.'], 400);
    }
    public function search(Request $request)
    {
        if (in_array(auth()->user()->role->name , ['admin','ceo','office-manager'])) {
            $activityData = [
                'user_id' => auth()->id(),
                'description' => 'همکار ' . auth()->user()->family . '(' . Auth::user()->role->label . ')'  . ' همکار با شناسه ' . $request->user . ' را جست وجو کرد',
                'action' => 'جست و جو همکار',
                'created_at' => now(),
            ];
            Activity::create($activityData);
            $users_id = $request->user == 'all' ? User::all()->pluck('id') : [$request->user];
            $query = User::whereIn('id', $users_id);
            $users = $query->latest()->paginate(10);

            return view('panel.users.index', compact('users'));
        } else {
            return back();
        }
    }

}

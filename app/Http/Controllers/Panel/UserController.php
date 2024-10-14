<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index()
    {
        if (auth()->user()->role() == 'admin') {
            // اگر کاربر ادمین است، همه کاربران را بگیرید
            $users = User::latest()->paginate(10);
        } else {
            // در غیر این صورت، فقط کاربر جاری را بگیرید
            $users = User::where('id', auth()->user()->id)->paginate(10);
        }
        return view('panel.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('users-create');

        return view('panel.users.create');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('users-edit');

        // به‌روزرسانی اطلاعات کاربر
        $dataToUpdate = [
            'name' => $request->name,
            'family' => $request->family,
            'phone' => $request->phone,
            'role_id' => $request->role ?? $user->role_id,
        ];

        // بروزرسانی رمز عبور در صورت ادمین بودن
        if (auth()->user()->isAdmin()) {
            $dataToUpdate['password'] = $request->password ? bcrypt($request->password) : $user->password;
        }

        $user->update($dataToUpdate);

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
        ]);

        // ارسال پیام موفقیت
        alert()->success('کاربر با موفقیت ساخته شد', 'ساخت کاربر');

        // برگرداندن پاسخ JSON
        return response()->json(['redirect' => route('users.index')]); // به جای return route
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
    public function uploadSignImage(Request $request, User $user)
    {
        $this->authorize('users-edit');

        // آپلود و به‌روزرسانی فایل امضا
        if ($request->hasFile('sign_image')) {
            // حذف فایل امضای قدیمی
            if ($user->sign_image) {
                unlink(public_path($user->sign_image));
            }

            // آپلود فایل جدید
            $sign_image = upload_file($request->file('sign_image'), 'signs');
            $user->sign_image = $sign_image;
            $user->save();

            return response()->json(['message' => 'امضا با موفقیت به‌روز شد', 'redirect' => route('users.index')]);
        }

        return response()->json(['message' => 'فایلی برای آپلود وجود ندارد.'], 400);
    }
}

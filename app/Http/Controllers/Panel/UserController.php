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
        $this->authorize('users-list');

        $users = User::latest()->paginate(10);
        return view('panel.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('users-create');

        return view('panel.users.create');
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

        // مدیریت آپلود تصویر پروفایل
        if ($request->hasFile('profile')) {
            // حذف فایل قدیمی اگر وجود داشته باشد
            if ($user->profile) {
                Storage::disk('public')->delete($user->profile);
            }

            // ذخیره فایل جدید
            $filePath = $request->file('profile')->store('profile', 'public');
            $user->profile = $filePath;
        }

        // مدیریت آپلود تصویر امضاء
        if ($request->hasFile('sign_image')) {
            // حذف فایل قدیمی اگر وجود داشته باشد
            if ($user->sign_image) {
                Storage::disk('public')->delete($user->sign_image);
            }

            // ذخیره فایل جدید
            $signImagePath = $request->file('sign_image')->store('signatures', 'public');
            $user->sign_image = $signImagePath;
        }

        // ذخیره تغییرات
        $user->save();

        // اجرای متد ایجاد اطلاعات مرخصی برای کاربر
        $this->createLeaveInfo($user);

        // ارسال پیام موفقیت
        alert()->success('کاربر مورد نظر با موفقیت ایجاد شد','ایجاد کاربر');
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

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('users-edit');

        if (auth()->user()->isAdmin()) {
            if ($request->hasFile('sign_image')) {
                if ($user->sign_image) {
                    // حذف فایل امضای قدیمی
                    unlink(public_path($user->sign_image));
                }
                // آپلود فایل جدید
                $sign_image = upload_file($request->file('sign_image'), 'Signs');
            } else {
                $sign_image = $user->sign_image;
            }
        } else {
            $sign_image = $user->sign_image;
        }

        // به‌روزرسانی اطلاعات کاربر
        $user->update([
            'name' => $request->name,
            'family' => $request->family,
            'phone' => $request->phone,
            'role_id' => $request->role ?? $user->role_id,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'sign_image' => $sign_image,
        ]);

        // به‌روزرسانی فایل پروفایل
        if ($request->hasFile('profile')) {
            // حذف فایل قدیمی پروفایل
            if ($user->profile) {
                Storage::disk('public')->delete($user->profile);
            }
            // ذخیره فایل جدید پروفایل
            $filePath = $request->file('profile')->store('profile', 'public');
            $user->profile = $filePath;
        }

        // ذخیره تغییرات نهایی
        $user->save();

        // پیام موفقیت و بازگشت
        if (Gate::allows('edit-profile', $user->id)) {
            alert()->success('پروفایل شما با موفقیت ویرایش شد', 'ویرایش پروفایل');
            return redirect()->back();
        } else {
            alert()->success('کاربر مورد نظر با موفقیت ویرایش شد', 'ویرایش کاربر');
            return redirect()->route('users.index');
        }
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
    public function userFile($filename){
        // بررسی وجود فایل در دیسک public
        if (Storage::disk('public')->exists('profile/' . $filename)) {
            // دریافت فایل و ارسال آن به مرورگر
            $filePath = 'profile/' . $filename;
            return response()->file(Storage::disk('public')->path($filePath));
        } else {
            // فایل پیدا نشد
            abort(Response::HTTP_NOT_FOUND, 'فایل پیدا نشد');
        }
    }
    public function userSign($filename){
        // بررسی وجود فایل در دیسک public
        if (Storage::disk('public')->exists('signs/' . $filename)) {
            // دریافت فایل و ارسال آن به مرورگر
            $filePath = 'signs/' . $filename;
            return response()->file(Storage::disk('public')->path($filePath));
        } else {
            // فایل پیدا نشد
            abort(Response::HTTP_NOT_FOUND, 'فایل پیدا نشد');
        }
    }
}

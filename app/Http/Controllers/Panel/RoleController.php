<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Activity;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('roles-list');

        $roles = Role::latest()->paginate(30);
        return view('panel.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorize('roles-create');

        $permissions = Permission::all();
        return view('panel.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $this->authorize('roles-create');

        $role = Role::create([
            'name' => $request->name,
            'label' => $request->label,
        ]);

        $role->permissions()->sync($request->permissions);
// ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ایجاد نقش',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') ' . 'نقش جدید با نام "' . $role->name . '" ایجاد کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        alert()->success('نقش مورد نظر با موفقیت ایجاد شد','ایجاد نقش');
        return redirect()->route('roles.index');
    }

    public function show(Role $role)
    {
        //
    }

    public function edit(Role $role)
    {
        $this->authorize('roles-edit');

        $permissions = Permission::all();
        return view('panel.roles.edit', compact('permissions','role'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $this->authorize('roles-edit');

        $role->update([
            'name' => $request->name,
            'label' => $request->label,
        ]);

        $role->permissions()->sync($request->permissions);
        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ویرایش نقش',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') ' . 'نقش "' . $role->name . '" را ویرایش کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        alert()->success('نقش مورد نظر با موفقیت ویرایش شد','ویرایش نقش');
        return redirect()->route('roles.index');
    }

    public function destroy(Role $role)
    {
        $this->authorize('roles-delete');

        if (!$role->users()->exists()){
            // ثبت فعالیت قبل از حذف نقش
            $activityData = [
                'user_id' => auth()->id(),
                'action' => 'حذف نقش',
                'description' => 'کاربر ' . auth()->user()->family . '(' . auth()->user()->role->label . ') ' . 'نقش "' . $role->name . '" را حذف کرد.',
                'created_at' => now(),
            ];
            Activity::create($activityData);

            $role->permissions()->detach();
            $role->delete();
            return back();
        }else{
            return response('کاربرانی با این نقش وجود دارند',500);
        }
    }
}

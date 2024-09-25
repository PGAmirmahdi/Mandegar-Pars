<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

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

        alert()->success('نقش مورد نظر با موفقیت ویرایش شد','ویرایش نقش');
        return redirect()->route('roles.index');
    }

    public function destroy(Role $role)
    {
        $this->authorize('roles-delete');

        if (!$role->users()->exists()){
            $role->permissions()->detach();
            $role->delete();
            return back();
        }else{
            return response('کاربرانی با این نقش وجود دارند',500);
        }
    }
}

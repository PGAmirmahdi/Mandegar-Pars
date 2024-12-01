<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function index()
    {
        $this->authorize('warehouses-list');

        $warehouses = Warehouse::paginate(30);
        return view('panel.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $this->authorize('warehouses-create');

        return view('panel.warehouses.create');
    }

    public function store(Request $request)
    {
        $this->authorize('warehouses-create');

        $request->validate(['name' => 'required']);

        $warehouse=Warehouse::create([
            'name' => $request->name
        ]);
// ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'description' => 'کاربر ' . auth()->user()->family  . '(' . Auth::user()->role->label . ')'  . ' انباری با نام ' . $warehouse->name . ' ایجاد کرد',
            'action' => 'ایجاد انبار',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        alert()->success('انبار با موفقیت ایجاد شد','ایجاد انبار');
        return redirect()->route('warehouses.index');
    }

    public function show(Warehouse $warehouse)
    {
        //
    }

    public function edit(Warehouse $warehouse)
    {
        $this->authorize('warehouses-edit');

        return view('panel.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $this->authorize('warehouses-edit');

        $request->validate(['name' => 'required']);

        $warehouse->update([
            'name' => $request->name
        ]);
        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')'  . ' انبار با نام قبلی ' . $warehouse->getOriginal('name') . ' را به ' . $warehouse->name . ' تغییر داد',
            'action' => 'ویرایش انبار',
            'created_at' => now(),
        ];
        Activity::create($activityData);

        alert()->success('انبار با موفقیت ویرایش شد','ویرایش انبار');
        return redirect()->route('warehouses.index');
    }

    public function destroy(Warehouse $warehouse)
    {
        $this->authorize('warehouses-delete');

        if (!$warehouse->inventories()->exists()) {
            $warehouseName = $warehouse->name; // ذخیره نام انبار قبل از حذف
            $warehouse->delete();

            // ثبت فعالیت
            $activityData = [
                'user_id' => auth()->id(),
                'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')' . ' انبار با نام ' . $warehouseName . ' را حذف کرد',
                'action' => 'حذف انبار',
                'created_at' => now(),
            ];
            Activity::create($activityData);

            return back();
        } else {
            // ثبت تلاش ناموفق برای حذف
            $activityData = [
                'user_id' => auth()->id(),
                'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')' . ' تلاش کرد انبار با نام ' . $warehouse->name . ' را حذف کند، اما این انبار شامل کالاهایی است',
                'action' => 'تلاش ناموفق برای حذف انبار',
                'created_at' => now(),
            ];
            Activity::create($activityData);

            return response('پیش از حذف ابتدا کالاهای موجود در این انبار را انتقال دهید', 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Activity;
use App\Models\Inventory;
use App\Models\InventoryReport;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
{
    public function index()
    {
        $this->authorize('inventory-list');

        $warehouse_id = \request()->warehouse_id;

        $data = Inventory::where('warehouse_id',$warehouse_id)->latest()->paginate(30);
        return view('panel.inventory.index', compact('data', 'warehouse_id'));
    }

    public function create()
    {
        $this->authorize('inventory-create');
        $warehouse_id = \request()->warehouse_id;
        return view('panel.inventory.create', compact('warehouse_id'));
    }

    public function store(StoreInventoryRequest $request)
    {
        $this->authorize('inventory-create');

        $product_id = $request->product_id;
        $warehouse_id = $request->warehouse_id;

        // بررسی اینکه آیا این محصول قبلاً در این انبار ثبت شده است یا خیر
        if (Inventory::where(['warehouse_id' => $warehouse_id, 'product_id' => $product_id])->exists()) {
            return back()->withErrors(['product_id' => 'این محصول قبلاً در این انبار ثبت شده است'])->withInput();
        }

        // اگر محصول قبلاً ثبت نشده باشد، کالا را ایجاد می‌کنیم
        $inventory = Inventory::create([
            'warehouse_id' => $warehouse_id,
            'product_id' => $product_id,
            'current_count' => $request->count,
            'initial_count' => $request->count,
        ]);

        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ایجاد کالا',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') کالا با نام ' . $inventory->title . ' را ایجاد کرد',
            'created_at' => now(),
        ];
        Activity::create($activityData);

        // نمایش پیام موفقیت
        alert()->success('کالا مورد نظر با موفقیت ایجاد شد', 'ایجاد کالا');

        // بازگشت به صفحه فهرست انبار
        return redirect()->route('inventory.index', ['warehouse_id' => $warehouse_id]);
    }


    public function show(Inventory $inventory)
    {
        $this->authorize('inventory');
    }

    public function edit(Inventory $inventory)
    {
        $this->authorize('inventory-edit');
        $warehouse_id = request()->get('warehouse_id');
        return view('panel.inventory.edit', compact('inventory', 'warehouse_id'));
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        $this->authorize('inventory-edit');
        $product_id = $request->product_id;
        $warehouse_id = $request->warehouse_id;
        // بررسی اینکه آیا این محصول قبلاً در این انبار ثبت شده است یا خیر
        if (Inventory::where(['warehouse_id' => $warehouse_id, 'product_id' => $product_id])->exists()) {
            return back()->withErrors(['product_id' => 'این محصول قبلاً در این انبار ثبت شده است'])->withInput();
        }

        $inventory->update([
            'current_count' => $request->count,
//            'initial_count' => $request->count,
//            'current_count' => ($inventory->current_count - $inventory->initial_count) + $request->count,
        ]);
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ویرایش کالا',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') کالا با نام ' . $inventory->title . ' را ویرایش کرد',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        alert()->success('کالا مورد نظر با موفقیت ویرایش شد','ویرایش کالا');
        return redirect()->route('inventory.index', ['warehouse_id' => $warehouse_id]);
    }

    public function destroy(Inventory $inventory)
    {
        $this->authorize('inventory-delete');

        $inventory->delete();
        return back();
    }

    public function search(Request $request)
    {
        $this->authorize('inventory-list');

        // فیلتر نوع (type)
        $type = $request->type == 'all' ? array_keys(Inventory::TYPE) : [$request->type];

        // فیلتر id انبار
        $warehouse_id = $request->warehouse_id;

        // شروع ساخت کوئری برای Inventory
        $data = Inventory::where('warehouse_id', $warehouse_id)
            // فیلتر برای محصول
            ->when($request->product && $request->product !== 'all', function ($query) use ($request) {
                return $query->where('product_id', $request->product); // توجه کنید که اینجا 'product_id' به عنوان کلید خارجی استفاده می‌شود
            })
            // فیلتر برای کد
            ->when($request->code, function ($query) use ($request) {
                return $query->where('code', 'LIKE', '%' . $request->code . '%');
            })
            // فیلتر برای دسته بندی
            ->when($request->category && $request->category !== 'all', function ($query) use ($request) {
                return $query->whereHas('product.category', function ($query) use ($request) {
                    $query->where('id', $request->category);
                });
            })
            // فیلتر برای مدل (brand)
            ->when($request->brand && $request->brand !== 'all', function ($query) use ($request) {
                return $query->whereHas('product.productModels', function ($query) use ($request) {
                    $query->where('id', $request->brand);
                });
            })
            ->latest()
            // صفحه بندی با 30 مورد در هر صفحه
            ->paginate(30);

        // بازگشت به ویو با ارسال داده‌ها
        return view('panel.inventory.index', compact('data', 'warehouse_id'));
    }



    public function excel()
    {
        $warehouse_id = \request()->warehouse_id;
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'خروجی اکسل از  کالاها',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ' از کالا ها خروجی اکسل گرفت',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        return Excel::download(new \App\Exports\InventoryExport($warehouse_id), 'inventory.xlsx');
    }

    public function move(Request $request)
    {
        $this->authorize('inventory-edit');

        $warehouse_id = $request->warehouse_id;
        $new_warehouse_id = $request->new_warehouse_id;
        $inventory_id = $request->inventory_id;
        $count = $request->count;

        $warehouse_name = Warehouse::find($warehouse_id)->name;
        $new_warehouse_name = Warehouse::find($new_warehouse_id)->name;
        $warehouseInventory = Inventory::find($inventory_id);

        // create output report

        if ($warehouseInventory->current_count < $count) {
            alert()->error('موجودی انبار کافی نیست','عدم موجودی');
            return back();
        }

        $report = InventoryReport::create([
            'warehouse_id' => $warehouse_id,
            'type' => 'output',
            'person' => auth()->user()->fullName(),
            'description' => "انتقال یافته به $new_warehouse_name",
        ]);

        $warehouseInventory->current_count -= $count;
        $warehouseInventory->save();

        $report->in_outs()->create([
            'inventory_id' => $warehouseInventory->id,
            'count' => $count,
        ]);

        // end create output report

        // create input report
        $report = InventoryReport::create([
            'warehouse_id' => $new_warehouse_id,
            'type' => 'input',
            'person' => auth()->user()->fullName(),
            'description' => "انتقال یافته از $new_warehouse_name",
        ]);

        $newWarehouseInventory = Inventory::where(['warehouse_id' => $new_warehouse_id, 'code' => $warehouseInventory->code])->firstOrCreate([
            'code' => $warehouseInventory->code,
        ],[
            'warehouse_id' => $new_warehouse_id,
            'title' => $warehouseInventory->title,
            'code' => $warehouseInventory->code,
            'type' => $warehouseInventory->type,
            'initial_count' => 0,
            'current_count' => 0,
        ]);

        $newWarehouseInventory->current_count += $count;
        $newWarehouseInventory->save();

        $report->in_outs()->create([
            'inventory_id' => $newWarehouseInventory->id,
            'count' => $count,
        ]);
        // end create input report

        alert()->success('کالا با موفقیت به انبار مورد نظر انتقال یافت','انتقال کالا');
        return back();
    }
}

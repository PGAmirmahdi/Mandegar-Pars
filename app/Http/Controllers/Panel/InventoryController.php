<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
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

        $code = $request->code;
        $warehouse_id = $request->warehouse_id;

        if (Inventory::where(['warehouse_id' => $warehouse_id, 'code' => $code])->exists()){
            return back()->withErrors(['code' => 'این کد در انبار موجود است'])->withInput();
        }

        Inventory::create([
            'warehouse_id' => $warehouse_id,
            'title' => $request->title,
            'code' => $request->code,
            'type' => $request->type,
            'initial_count' => $request->count,
            'current_count' => $request->count,
        ]);

        alert()->success('کالا مورد نظر با موفقیت ایجاد شد','ایجاد کالا');
        return redirect()->route('inventory.index', ['warehouse_id' => $warehouse_id]);
    }

    public function show(Inventory $inventory)
    {
        $this->authorize('inventory');
    }

    public function edit(Inventory $inventory)
    {
        $this->authorize('inventory-edit');

        return view('panel.inventory.edit', compact('inventory'));
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        $this->authorize('inventory-edit');

        $code = $request->code;
        $warehouse_id = $inventory->warehouse_id;

        if ($exist = Inventory::where(['warehouse_id' => $warehouse_id, 'code' => $code])->first()){
            if ($exist->id != $inventory->id){
                return back()->withErrors(['code' => 'این کد در انبار موجود است'])->withInput();
            }
        }

        $inventory->update([
            'warehouse_id' => $warehouse_id,
            'title' => $request->title,
            'code' => $request->code,
            'type' => $request->type,
            'initial_count' => $request->count,
            'current_count' => ($inventory->current_count - $inventory->initial_count) + $request->count,
        ]);

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

        $type = $request->type == 'all' ? array_keys(Inventory::TYPE) : [$request->type];

        $warehouse_id = $request->warehouse_id;

        $data = Inventory::where('warehouse_id', $warehouse_id)
            ->whereIn('type', $type)
            ->when($request->code, function ($q) use($request){
                $q->where('code', $request->code);
            })
            ->where('title', 'like',"%$request->title%")
            ->latest()->paginate(30);

        return view('panel.inventory.index', compact('data', 'warehouse_id'));
    }

    public function excel()
    {
        $warehouse_id = \request()->warehouse_id;

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

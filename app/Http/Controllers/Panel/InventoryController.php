<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Inventory;
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

        $warehouse_id = $request->warehouse_id;

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

        if ($request->count < $inventory->current_count){
            alert()->error('موجودی اولیه نمی تواند کمتر از موجودی اولیه باشد','عدم تطابق موجودی فعلی و اولیه');
            return back();
        }

        $warehouse_id = $inventory->warehouse_id;

        $inventory->update([
            'warehouse_id' => $warehouse_id,
            'title' => $request->title,
            'code' => $request->code,
            'type' => $request->type,
            'initial_count' => $request->count,
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
}

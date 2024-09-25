<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

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

        Warehouse::create([
            'name' => $request->name
        ]);

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

        alert()->success('انبار با موفقیت ویرایش شد','ویرایش انبار');
        return redirect()->route('warehouses.index');
    }

    public function destroy(Warehouse $warehouse)
    {
        $this->authorize('warehouses-delete');

        if (!$warehouse->inventories()->exists()){
            $warehouse->delete();
            return back();
        }else{
            return response('پیش از حذف ابتدا کالاهای موجود در این انبار را انتقال دهید',500);
        }
    }
}

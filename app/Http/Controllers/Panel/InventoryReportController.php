<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryReport;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    public function index()
    {
        $this->authorize('inventory');

        $type = \request()->type;
        $reports = InventoryReport::where('type',$type)->latest()->paginate(30);
        return view('panel.inputs.index', compact('reports'));
    }

    public function create()
    {
        $this->authorize('inventory');
        $type = \request()->type;

        return view('panel.inputs.create', compact('type'));
    }

    public function store(Request $request)
    {
        $this->authorize('inventory');

        $request->validate(['person' => 'required'],['person.required' => 'فیلد طرف حساب الزامی است']);

        // alert if inventory is null
        if (!$request->inventory_id){
            alert()->error('لطفا کالاهای مربوطه جهت ورود را انتخاب کنید','عدم ثبت کالا');
            return back();
        }

        $type = $request->type;

        // create input report
        $report = InventoryReport::create([
            'type' => $request->type,
            'person' => $request->person,
            'description' => $request->description,
        ]);

        $this->createInOut($report, $request);

        alert()->success('ورودی مورد نظر با موفقیت ثبت شد','ثبت ورودی');
        return redirect()->route('inventory-reports.index', ['type' => $type]);
    }

    public function show(InventoryReport $inventoryReport)
    {
        //
    }

    public function edit(InventoryReport $inventoryReport)
    {
        $this->authorize('inventory');
        $type = \request()->type;

        return view('panel.inputs.edit', compact('type','inventoryReport'));
    }

    public function update(Request $request, InventoryReport $inventoryReport)
    {
        $this->authorize('inventory');

        $request->validate(['person' => 'required'],['person.required' => 'فیلد طرف حساب الزامی است']);

        // alert if inventory is null
        if (!$request->inventory_id){
            alert()->error('لطفا کالاهای مربوطه جهت ورود را انتخاب کنید','عدم ثبت کالا');
            return back();
        }

        $type = $request->type;

        // create input report
        $inventoryReport->update([
            'type' => $request->type,
            'person' => $request->person,
            'description' => $request->description,
        ]);

        $this->deleteInOut($inventoryReport, $request);
        $this->createInOut($inventoryReport, $request);

        alert()->success('ورودی مورد نظر با موفقیت ویرایش شد','ویرایش ورودی');
        return redirect()->route('inventory-reports.index', ['type' => $type]);
    }

    public function destroy(InventoryReport $inventoryReport)
    {
        $this->authorize('inventory');

        $inventoryReport->in_outs()->each(function ($item){
            $inventory = Inventory::find($item->inventory_id);
            $inventory->count -= $item->count;
            $inventory->save();
        });

        $inventoryReport->delete();
        return back();
    }

    private function createInOut($report, $request)
    {
        // create in-outs and increase inventory
        foreach ($request->inventory_id as $key => $inventory_id){
            $inventory = Inventory::find($inventory_id);
            $inventory->count += $request->counts[$key];
            $inventory->save();

            $report->in_outs()->create([
                'inventory_id' => $inventory_id,
                'count' => $request->counts[$key],
            ]);
        }
    }

    private function deleteInOut($report, $request)
    {
        // delete in-outs and decrease inventory
        foreach ($report->in_outs as $item){
            $inventory = Inventory::find($item->inventory_id);
            $inventory->count -= $item->count;
            $inventory->save();
        }

        $report->in_outs()->delete();
    }
}

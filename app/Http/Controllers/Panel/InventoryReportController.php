<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Factor;
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

        if ($type == 'input'){
            return view('panel.inputs.index', compact('reports'));
        }else{
            return view('panel.outputs.index', compact('reports'));
        }
    }

    public function create()
    {
        $this->authorize('inventory');
        $type = \request()->type;

        if ($type == 'input'){
            return view('panel.inputs.create', compact('type'));
        }else{
            return view('panel.outputs.create', compact('type'));
        }
    }

    public function store(Request $request)
    {
        $this->authorize('inventory');

        // alert if inventory is null
        if (!$request->inventory_id){
            alert()->error('لطفا کالاهای مربوطه جهت ورود را انتخاب کنید','عدم ثبت کالا');
            return back();
        }

        $type = $request->type;

        if ($type == 'input'){
            $type_lbl = 'ورودی';
            $request->validate(['person' => 'required'],['person.required' => 'فیلد تحویل دهنده الزامی است']);
        }else{
            $type_lbl = 'خروجی';
            $request->validate([
                'factor_id' => 'required',
                'person' => 'required'
                ], [
                    'factor_id.required' => 'انتخاب فاکتور الزامی است',
                    'person.required' => 'فیلد تحویل گیرنده الزامی است'
            ]);
        }

        // check inventory count is enough
        $this->checkInventoryCount($request);

        // create input report
        $report = InventoryReport::create([
            'factor_id' => $request->factor_id,
            'type' => $request->type,
            'person' => $request->person,
            'description' => $request->description,
        ]);

        $this->createInOut($report, $request, $type);

        alert()->success("$type_lbl مورد نظر با موفقیت ثبت شد","ثبت $type_lbl");
        return redirect()->route('inventory-reports.index', ['type' => $type]);
    }

    public function show(InventoryReport $inventoryReport)
    {
        $this->authorize('inventory');

        return view('panel.outputs.printable', compact('inventoryReport'));
    }

    public function edit(InventoryReport $inventoryReport)
    {
        $this->authorize('inventory');
        $type = \request()->type;

        if ($type == 'input'){
            return view('panel.inputs.edit', compact('type','inventoryReport'));
        }else{
            return view('panel.outputs.edit', compact('type','inventoryReport'));
        }
    }

    public function update(Request $request, InventoryReport $inventoryReport)
    {
        $this->authorize('inventory');

        // alert if inventory is null
        if (!$request->inventory_id){
            alert()->error('لطفا کالاهای مربوطه جهت ورود را انتخاب کنید','عدم ثبت کالا');
            return back();
        }

        $type = $request->type;

        if ($type == 'input'){
            $request->validate(['person' => 'required'],['person.required' => 'فیلد تحویل دهنده الزامی است']);
        }else{
            $request->validate([
                'factor_id' => 'required',
                'person' => 'required'
            ], [
                'factor_id.required' => 'انتخاب فاکتور الزامی است',
                'person.required' => 'فیلد تحویل گیرنده الزامی است'
            ]);
        }

        // check inventory count is enough
        $this->checkInventoryCount($request);

        // create input report
        $inventoryReport->update([
            'factor_id' => $request->factor_id,
            'type' => $request->type,
            'person' => $request->person,
            'description' => $request->description,
        ]);

        $this->deleteInOut($inventoryReport, $type);
        $this->createInOut($inventoryReport, $request, $type);

        alert()->success('ورودی مورد نظر با موفقیت ویرایش شد','ویرایش ورودی');
        return redirect()->route('inventory-reports.index', ['type' => $type]);
    }

    public function destroy(InventoryReport $inventoryReport)
    {
        $this->authorize('inventory');

        if ($inventoryReport->type == 'input'){
            $inventoryReport->in_outs()->each(function ($item){
                $inventory = Inventory::find($item->inventory_id);
                $inventory->current_count -= $item->count;
                $inventory->save();
            });
        }else{
            $inventoryReport->in_outs()->each(function ($item){
                $inventory = Inventory::find($item->inventory_id);
                $inventory->current_count += $item->count;
                $inventory->save();
            });
        }

        $inventoryReport->delete();
        return back();
    }

    private function createInOut($report, $request, $type)
    {
        if ($type == 'input'){
            // create in-outs
            foreach ($request->inventory_id as $key => $inventory_id){
                $inventory = Inventory::find($inventory_id);
                $inventory->current_count += $request->counts[$key];
                $inventory->save();

                $report->in_outs()->create([
                    'inventory_id' => $inventory_id,
                    'count' => $request->counts[$key],
                ]);
            }
        }else{
            // create in-outs
            foreach ($request->inventory_id as $key => $inventory_id){
                $inventory = Inventory::find($inventory_id);
                $inventory->current_count -= $request->counts[$key];
                $inventory->save();

                $report->in_outs()->create([
                    'inventory_id' => $inventory_id,
                    'count' => $request->counts[$key],
                ]);
            }
        }
    }

    private function deleteInOut($report, $type)
    {
        if ($type == 'input'){
            // delete in-outs
            foreach ($report->in_outs as $item){
                $inventory = Inventory::find($item->inventory_id);
                $inventory->current_count -= $item->count;
                $inventory->save();
            }

            $report->in_outs()->delete();
        }else{
            // delete in-outs
            foreach ($report->in_outs as $item){
                $inventory = Inventory::find($item->inventory_id);
                $inventory->current_count += $item->count;
                $inventory->save();
            }

            $report->in_outs()->delete();
        }
    }

    private function checkInventoryCount($request)
    {
        $data = [];

        foreach ($request->inventory_id as $key => $inventory_id){
            if (isset($data[$inventory_id])){
                $data[$inventory_id] += $request->counts[$key];
            }else{
                $data[$inventory_id] = (int) $request->counts[$key];
            }
        }

        $error_data = [];
        $inventory = Inventory::whereIn('id', array_keys($data))->get();

        foreach ($inventory as $item){
            if ($item->current_count < $data[$item->id]) {
                $error_data[] = $item->title;
            }
        }

        if (count($error_data)){
            session()->flash('error_data', $error_data);
            $request->validate(['inventory_count' => 'required']);
        }
    }
}

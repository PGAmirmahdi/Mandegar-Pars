<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Factor;
use App\Models\Inventory;
use App\Models\InventoryReport;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    public function index()
    {
        $type = \request()->type;
        $warehouse_id = \request()->warehouse_id;

        $reports = InventoryReport::where(['warehouse_id' => $warehouse_id, 'type' => $type])->latest()->paginate(30);

        if ($type == 'input'){
            $this->authorize('input-reports-list');

            return view('panel.inputs.index', compact('reports', 'warehouse_id'));
        }else{
            $this->authorize('output-reports-list');

            return view('panel.outputs.index', compact('reports','warehouse_id'));
        }
    }

    public function create()
    {
        $type = \request()->type;
        $warehouse_id = request()->warehouse_id;

        if ($type == 'input'){
            $this->authorize('input-reports-create');

            return view('panel.inputs.create', compact('type', 'warehouse_id'));
        }else{
            $this->authorize('output-reports-create');

            return view('panel.outputs.create', compact('type','warehouse_id'));
        }
    }

    public function store(Request $request)
    {

        // alert if inventory is null
        if (!$request->inventory_id){
            alert()->error('لطفا کالاهای مربوطه جهت ورود را انتخاب کنید','عدم ثبت کالا');
            return back();
        }

        $type = $request->type;

        if ($type == 'input'){
            $this->authorize('input-reports-create');

            $type_lbl = 'ورودی';
            $request->validate([
                'person' => 'required',
                'input_date' => 'required',
            ], [
                'person.required' => 'فیلد تحویل دهنده الزامی است',
                'input_date.required' => 'فیلد تاریخ ورود الزامی است'
            ]);

            $date = Verta::parseFormat('Y/m/d', $request->input_date)->toCarbon()->toDateTimeString();

        }else{
            $this->authorize('output-reports-create');

            $type_lbl = 'خروجی';
            $request->validate([
                'person' => 'required',
                'output_date' => 'required'
            ], [
                'person.required' => 'فیلد تحویل گیرنده الزامی است',
                'output_date.required' => 'فیلد تاریخ خروج الزامی است'
            ]);

            $date = Verta::parseFormat('Y/m/d', $request->output_date)->toCarbon()->toDateTimeString();

            // check inventory count is enough
            $this->storeCheckInventoryCount($request);
        }

        // create report
        $report = InventoryReport::create([
            'warehouse_id' => $request->warehouse_id,
            'factor_id' => $request->factor_id,
            'type' => $request->type,
            'person' => $request->person,
            'description' => $request->description,
            'date' => $date,
        ]);

        $this->createInOut($report, $request, $type);

        alert()->success("$type_lbl مورد نظر با موفقیت ثبت شد","ثبت $type_lbl");
        return redirect()->route('inventory-reports.index', ['type' => $type, 'warehouse_id' => $request->warehouse_id]);
    }

    public function show(InventoryReport $inventoryReport)
    {
        $this->authorize('output-reports-edit');

        return view('panel.outputs.printable', compact('inventoryReport'));
    }

    public function edit(InventoryReport $inventoryReport)
    {
        $type = \request()->type;
        $warehouse_id = $inventoryReport->warehouse_id;

        if ($type == 'input'){
            $this->authorize('input-reports-edit');

            return view('panel.inputs.edit', compact('type','inventoryReport', 'warehouse_id'));
        }else{
            $this->authorize('output-reports-edit');

            return view('panel.outputs.edit', compact('type','inventoryReport', 'warehouse_id'));
        }
    }

    public function update(Request $request, InventoryReport $inventoryReport)
    {
        // alert if inventory is null
        if (!$request->inventory_id){
            alert()->error('لطفا کالاهای مربوطه جهت ورود را انتخاب کنید','عدم ثبت کالا');
            return back();
        }

        $type = $request->type;

        if ($type == 'input'){
            $this->authorize('input-reports-edit');

            $type_lbl = 'ورودی';
            $request->validate([
                'person' => 'required',
                'input_date' => 'required',
            ], [
                'person.required' => 'فیلد تحویل دهنده الزامی است',
                'input_date.required' => 'فیلد تاریخ ورود الزامی است'
            ]);

            $date = Verta::parseFormat('Y/m/d', $request->input_date)->toCarbon()->toDateTimeString();
        }else{
            $this->authorize('output-reports-edit');

            $type_lbl = 'خروجی';
            $request->validate([
                'person' => 'required',
                'output_date' => 'required'
            ], [
                'person.required' => 'فیلد تحویل گیرنده الزامی است',
                'output_date.required' => 'فیلد تاریخ خروج الزامی است'
            ]);

            $date = Verta::parseFormat('Y/m/d', $request->output_date)->toCarbon()->toDateTimeString();

            // check inventory count is enough
            $this->updateCheckInventoryCount($inventoryReport ,$request);
        }

        // create input report
        $inventoryReport->update([
            'factor_id' => $request->factor_id,
            'type' => $request->type,
            'person' => $request->person,
            'description' => $request->description,
            'date' => $date,
        ]);

        $this->deleteInOut($inventoryReport, $type);
        $this->createInOut($inventoryReport, $request, $type);

        alert()->success("$type_lbl مورد نظر با موفقیت ویرایش شد","ویرایش $type_lbl");
        return redirect()->route('inventory-reports.index', ['type' => $type, 'warehouse_id' => $inventoryReport->warehouse_id]);
    }

    public function destroy(InventoryReport $inventoryReport)
    {
        if ($inventoryReport->type == 'input'){
            $this->authorize('input-reports-delete');

            $inventoryReport->in_outs()->each(function ($item){
                $inventory = Inventory::find($item->inventory_id);
                $inventory->current_count -= $item->count;
                $inventory->save();
            });
        }else{
            $this->authorize('output-reports-delete');

            $inventoryReport->in_outs()->each(function ($item){
                $inventory = Inventory::find($item->inventory_id);
                $inventory->current_count += $item->count;
                $inventory->save();
            });
        }

        $inventoryReport->delete();
        return back();
    }

    public function search(Request $request)
    {
        $type = $request->type;
        $warehouse_id = $request->warehouse_id;
        $inventory_id = $request->inventory_id == 'all' ? Inventory::where('warehouse_id',$warehouse_id)->pluck('id') : [$request->inventory_id];

        $reports = InventoryReport::where(['warehouse_id' => $warehouse_id, 'type' => $type])->whereHas('in_outs', function ($q) use($inventory_id){
            $q->whereIn('inventory_id', $inventory_id);
        })->latest()->paginate(30);

        if ($type == 'input'){
            $this->authorize('input-reports-list');

            return view('panel.inputs.index', compact('reports', 'warehouse_id'));
        }else{
            $this->authorize('output-reports-list');

            return view('panel.outputs.index', compact('reports','warehouse_id'));
        }
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

    private function storeCheckInventoryCount($request)
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

    private function updateCheckInventoryCount(InventoryReport $inventoryReport,$request)
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
            $temp_current_count = $inventoryReport->in_outs()->where('inventory_id', $item->id)->sum('count');
            if (($item->current_count + $temp_current_count) < $data[$item->id]) {
                $error_data[] = $item->title;
            }
        }

        if (count($error_data)){
            session()->flash('error_data', $error_data);
            $request->validate(['inventory_count' => 'required']);
        }
    }
}

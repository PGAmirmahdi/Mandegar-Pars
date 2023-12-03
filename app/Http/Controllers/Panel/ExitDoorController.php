<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ExitDoor;
use App\Models\InventoryReport;
use Illuminate\Http\Request;

class ExitDoorController extends Controller
{
    public function index()
    {
        $this->authorize('exit-door');

        $data = ExitDoor::latest()->paginate(30);
        return view('panel.exit-door.index', compact('data'));
    }

    public function create()
    {
        $this->authorize('exit-door');

        $inventory_reports = InventoryReport::whereType('output')->doesntHave('exit_door')->get();
        return view('panel.exit-door.create', compact('inventory_reports'));
    }

    public function store(Request $request)
    {
        $this->authorize('exit-door');

        $request->validate([
            'inventory_report_id' => 'required'
        ],[
            'inventory_report_id.required' => 'انتخاب فاکتور الزامی است'
        ]);

        ExitDoor::create([
            'inventory_report_id' => $request->inventory_report_id,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        alert()->success('ثبت خروج محموله با موفقیت انجام شد','ثبت خروج');
        return redirect()->route('exit-door.index');
    }

    public function show(ExitDoor $exitDoor)
    {
        $this->authorize('exit-door');

    }

    public function edit(ExitDoor $exitDoor)
    {
        //
    }

    public function update(Request $request, ExitDoor $exitDoor)
    {
        //
    }

    public function destroy(ExitDoor $exitDoor)
    {
        $this->authorize('exit-door');

        $exitDoor->delete();
        return back();
    }

    public function getInOuts(InventoryReport $inventoryReport)
    {
        $this->authorize('exit-door');

        $data = [
            'items' => $inventoryReport->in_outs()->with('inventory')->get(),
            'invoice_id' => $inventoryReport->factor->invoice_id
        ];

        return response()->json(['data' => $data]);
    }

    public function getDescription(ExitDoor $exitDoor)
    {
        $this->authorize('exit-door');

        return response()->json(['data' => $exitDoor->description]);
    }
}

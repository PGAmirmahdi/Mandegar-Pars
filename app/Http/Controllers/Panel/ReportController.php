<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $this->authorize('reports-list');

        if (auth()->user()->isAdmin() || auth()->user()->isCEO()){
            $reports = Report::latest()->paginate(30);
            return view('panel.reports.index', compact('reports'));
        }else{
            $reports = Report::where('user_id', auth()->id())->latest()->paginate(30);
            return view('panel.reports.index', compact('reports'));
        }
    }

    public function create()
    {
        $this->authorize('reports-create');

        return view('panel.reports.create');
    }

    public function store(Request $request)
    {
        $this->authorize('reports-create');

        if (!$request->items){
            return back()->withErrors(['item' => 'حداقل یک مورد اضافه کنید']);
        }

        $items = explode(',', $request->items);

        Report::create([
            'user_id' => auth()->id(),
            'items' => json_encode($items)
        ]);

        alert()->success('گزارش روزانه با موفقیت ثبت شد','ثبت گزارش');
        return redirect()->route('reports.index');
    }

    public function show(Report $report)
    {
        //
    }

    public function edit(Report $report)
    {
        $this->authorize('reports-edit');
        $this->authorize('edit-report', $report);

        return view('panel.reports.edit', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $this->authorize('reports-edit');

        if (!$request->items){
            return back()->withErrors(['item' => 'حداقل یک مورد اضافه کنید']);
        }

        $items = explode(',', $request->items);

        $report->update([
            'items' => json_encode($items)
        ]);

        alert()->success('گزارش روزانه با موفقیت ویرایش شد','ویرایش گزارش');
        return redirect()->route('reports.index');
    }

    public function destroy(Report $report)
    {
        $this->authorize('reports-delete');

        $report->delete();
        return back();
    }

    public function getItems(Report $report)
    {
        return response()->json(['data' => json_decode($report->items)]);
    }
}

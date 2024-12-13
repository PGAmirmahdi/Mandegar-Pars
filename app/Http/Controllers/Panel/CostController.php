<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCostRequest;
use App\Models\Activity;
use App\Models\Cost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class CostController extends Controller
{
    public function index()
    {
        $this->authorize('costs-list');

        $costs = Cost::latest()->paginate(30);
        return view('panel.costs.index', compact(['costs']));
    }


    public function create()
    {
        $this->authorize('costs-create');

        return view('panel.costs.create');
    }


    public function store(StoreCostRequest $request)
    {
        $this->authorize('costs-create');
        $additional_cost = ((int)$request->Logistic_price + (int)$request->other_price) / $request->count;
        $final_price = $additional_cost + (int)$request->price;

        $cost = new Cost();
        $cost->user_id = auth()->id();
        $cost->product = $request->product;
        $cost->count = $request->count;
        $cost->price = $request->price;
        $cost->Logistic_price = $request->Logistic_price;
        $cost->other_price = $request->other_price;
        $cost->final_price = $final_price;
        $cost->save();

        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ایجاد بهای تمام شده',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') ' . 'بهای تمام شده ایجاد کرد',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        alert()->success('بهای تمام شده با موفقیت ایجاد شد', 'ایجاد بهای تمام شده');
        return redirect()->route('costs.index');
    }


    public function edit(Cost $cost)
    {
        $this->authorize('costs-edit');
        return view('panel.costs.edit', compact(['cost']));
    }

    public function update(StoreCostRequest $request, Cost $cost)
    {
        $this->authorize('costs-edit');

        $additional_cost = ((int)$request->Logistic_price + (int)$request->other_price) / $request->count;
        $final_price = $additional_cost + (int)$request->price;

        $cost->product = $request->product;
        $cost->count = $request->count;
        $cost->price = $request->price;
        $cost->Logistic_price = $request->Logistic_price;
        $cost->other_price = $request->other_price;
        $cost->final_price = $final_price;
        $cost->save();

        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ویرایش بهای تمام شده',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') ' . ' بهای تمام شده به شناسه ' . $cost->id . ' را ویرایش کرد',
            'created_at' => now(),
        ];
        Activity::create($activityData);

        alert()->success('بهای تمام شده با موفقیت ایجاد شد', 'ویرایش بهای تمام شده');
        return redirect()->route('costs.index');
    }


    public function destroy(Cost $cost)
    {
        $this->authorize('costs-delete');
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'حذف بهای تمام شده',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') ' . 'بهای تمام شده به شناسه ' . $cost->id . ' را حذف کرد',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        $cost->delete();
        alert()->success('بهای تمام شده با موفقیت حذف شد', 'حذف بهای تمام شده');
        return back();
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new \App\Exports\CostExport, 'costs.xlsx');
    }

}

<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrinterRequest;
use App\Http\Requests\UpdatePrinterRequest;
use App\Models\Activity;
use App\Models\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrinterController extends Controller
{
    public function index()
    {
        $this->authorize('printers-list');

        $printers = Printer::latest()->paginate(30);
        return view('panel.printers.index', compact('printers'));
    }

    public function create()
    {
        $this->authorize('printers-create');

        return view('panel.printers.create');
    }

    public function store(StorePrinterRequest $request)
    {
        $this->authorize('printers-create');

        $printer = Printer::create([
            'name' => $request->name,
            'brand' => $request->brand,
            'cartridges' => $request->cartridges,
        ]);
// ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ایجاد پرینتر',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') پرینتر جدیدی به نام ' . $printer->name . ' از برند ' . $printer->brand . ' ایجاد کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ثبت فعالیت در پایگاه داده
        alert()->success('پرینتر مورد نظر با موفقیت ایجاد شد','ایجاد پرینتر');
        return redirect()->route('printers.index');
    }

    public function show(Printer $printer)
    {
        //
    }

    public function edit(Printer $printer)
    {
        $this->authorize('printers-edit');

        return view('panel.printers.edit',compact('printer'));
    }

    public function update(UpdatePrinterRequest $request, Printer $printer)
    {
        $this->authorize('printers-edit');
        $printer->update([
            'name' => $request->name,
            'brand' => $request->brand,
            'cartridges' => $request->cartridges,
        ]);
// ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ویرایش پرینتر',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') پرینتر به نام ' . $printer->name . ' از برند ' . $printer->brand . ' را ویرایش کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ثبت فعالیت در پایگاه داده
        alert()->success('پرینتر مورد نظر با موفقیت ویرایش شد','ویرایش پرینتر');
        return redirect()->route('printers.index');
    }

    public function destroy(Printer $printer)
    {
        $this->authorize('printers-delete');
        // ثبت فعالیت قبل از حذف پرینتر
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'حذف پرینتر',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') پرینتر به نام ' . $printer->name . ' از برند ' . $printer->brand . ' را حذف کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ثبت فعالیت در پایگاه داده
        $printer->delete();
        return back();
    }

    public function search(Request $request)
    {
        $this->authorize('printers-list');

        $brands = $request->brand == 'all' ? array_values(Printer::BRANDS) : [$request->brand];

        $printers = Printer::whereIn('brand',$brands)
            ->where('name','like', "%$request->name%")
            ->latest()->paginate(30);

        return view('panel.printers.index', compact('printers'));
    }
}

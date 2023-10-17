<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrinterRequest;
use App\Http\Requests\UpdatePrinterRequest;
use App\Models\Printer;
use Illuminate\Http\Request;

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
        Printer::create([
            'printer_name' => $request->printer_name,
            'printer_model' => $request->printer_model,
        ]);

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
            'printer_name' => $request->printer_name,
            'printer_model' => $request->printer_model,
        ]);

        alert()->success('پرینتر مورد نظر با موفقیت ویرایش شد','ویرایش پرینتر');
        return redirect()->route('printers.index');

    }

    public function destroy(Printer $printer)
    {
        $this->authorize('printers-delete');

        $printer->delete();
        return back();
    }
}

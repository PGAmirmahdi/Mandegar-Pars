<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleReportRequest;
use App\Http\Requests\UpdateSaleReportRequest;
use App\Models\Invoice;
use App\Models\SaleReport;
use Illuminate\Http\Request;

class SaleReportController extends Controller
{
    public function index()
    {
        $this->authorize('sale-reports-list');

        if (auth()->user()->isAdmin() || auth()->user()->isCEO()){
            $sale_reports = SaleReport::latest()->paginate(30);
        }else{
            $sale_reports = SaleReport::where('user_id', auth()->id())->latest()->paginate(30);
        }

        if (auth()->user()->isAdmin() || auth()->user()->isCEO()){
            $invoices = Invoice::with('customer')->latest()->get()->pluck('customer.name','id');
        }else{
            $invoices = Invoice::with('customer')->where('user_id', auth()->id())->latest()->get()->pluck('customer.name','id');
        }

        return view('panel.sale-reports.index', compact('sale_reports','invoices'));
    }

    public function create()
    {
        $this->authorize('sale-reports-create');

        if (auth()->user()->isAdmin() || auth()->user()->isCEO()){
            $invoices = Invoice::with('customer')->latest()->get()->pluck('customer.name','id');
        }else{
            $invoices = Invoice::with('customer')->where('user_id', auth()->id())->latest()->get()->pluck('customer.name','id');
        }

        return view('panel.sale-reports.create', compact('invoices'));
    }

    public function store(StoreSaleReportRequest $request)
    {
        $this->authorize('sale-reports-create');

        SaleReport::create([
            'user_id' => auth()->id(),
            'invoice_id' => $request->invoice,
            'person_name' => $request->person_name,
            'organ_name' => $request->organ_name,
            'national_code' => $request->national_code,
            'payment_type' => $request->payment_type,
        ]);

        alert()->success("گزارش فروش مورد نظر با موفقیت ایجاد شد","ایجاد گزارش فروش");
        return redirect()->route('sale-reports.index');
    }

    public function show(SaleReport $saleReport)
    {
        //
    }

    public function edit(SaleReport $saleReport)
    {
        $this->authorize('sale-reports-edit');

        if (auth()->user()->isAdmin() || auth()->user()->isCEO()){
            $invoices = Invoice::with('customer')->latest()->get()->pluck('customer.name','id');
        }else{
            $invoices = Invoice::with('customer')->where('user_id', auth()->id())->latest()->get()->pluck('customer.name','id');
        }

        return view('panel.sale-reports.edit', compact('saleReport','invoices'));
    }

    public function update(UpdateSaleReportRequest $request, SaleReport $saleReport)
    {
        $this->authorize('sale-reports-edit');

        $saleReport->update([
            'invoice_id' => $request->invoice,
            'person_name' => $request->person_name,
            'organ_name' => $request->organ_name,
            'national_code' => $request->national_code,
            'payment_type' => $request->payment_type,
        ]);

        alert()->success("گزارش فروش مورد نظر با موفقیت ویرایش شد","ویرایش گزارش فروش");
        return redirect()->route('sale-reports.index');
    }

    public function destroy(SaleReport $saleReport)
    {
        $this->authorize('sale-reports-delete');
        $saleReport->delete();

        return back();
    }

    public function search(Request $request)
    {
        $this->authorize('sale-reports-list');

        if (auth()->user()->isAdmin() || auth()->user()->isCEO()){
            $invoices = Invoice::with('customer')->latest()->get()->pluck('customer.name','id');
        }else{
            $invoices = Invoice::with('customer')->where('user_id', auth()->id())->latest()->get()->pluck('customer.name','id');
        }

        $invoice_id = $request->invoice_id == 'all' ? $invoices->keys() : [$request->invoice_id];

        if (auth()->user()->isAdmin() || auth()->user()->isCEO()){
            $sale_reports = SaleReport::where(function ($q) use($invoice_id, $request){
                if ($request->invoice_id == 'all'){
                    $q->whereIn('invoice_id', $invoice_id)->orWhereNull('invoice_id');
                }else{
                    $q->whereIn('invoice_id', $invoice_id);
                }
            })
                ->where('person_name', 'like', "%$request->person_name%")
                ->where('organ_name', 'like', "%$request->organ_name%")
                ->when($request->national_code, function ($q) use($request){
                    $q->where('national_code', $request->national_code);
                })
                ->latest()->paginate(30);
        }else{
            $sale_reports = SaleReport::where(function ($q) use($invoice_id, $request){
                if ($request->invoice_id == 'all'){
                    $q->whereIn('invoice_id', $invoice_id)->orWhereNull('invoice_id');
                }else{
                    $q->whereIn('invoice_id', $invoice_id);
                }
            })
                ->where('person_name', 'like', "%$request->person_name%")
                ->where('organ_name', 'like', "%$request->organ_name%")
                ->when($request->national_code, function ($q) use($request){
                    $q->where('national_code', $request->national_code);
                })
                ->where('user_id', auth()->id())
                ->latest()->paginate(30);
        }
        return view('panel.sale-reports.index', compact('sale_reports','invoices'));
    }
}

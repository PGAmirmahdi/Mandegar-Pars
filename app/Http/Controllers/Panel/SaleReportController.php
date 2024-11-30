<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleReportRequest;
use App\Http\Requests\UpdateSaleReportRequest;
use App\Models\Activity;
use App\Models\Invoice;
use App\Models\SaleReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $saleReport=SaleReport::create([
            'user_id' => auth()->id(),
            'invoice_id' => $request->invoice,
            'person_name' => $request->person_name,
            'organ_name' => $request->organ_name,
            'national_code' => $request->national_code,
            'payment_type' => $request->payment_type,
        ]);
        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ایجاد گزارش فروش',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')' . ' گزارشی فروش با شماره فاکتور ' . $saleReport->invoice_id . ' ایجاد کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
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
// ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ویرایش گزارش فروش',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')'  . ' گزارشی فروش با شماره فاکتور ' . $saleReport->invoice_id . ' را ویرایش کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        alert()->success("گزارش فروش مورد نظر با موفقیت ویرایش شد","ویرایش گزارش فروش");
        return redirect()->route('sale-reports.index');
    }

    public function destroy(SaleReport $saleReport)
    {
        $this->authorize('sale-reports-delete');
        // ثبت فعالیت قبل از حذف گزارش فروش
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'حذف گزارش فروش',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')'  . ' گزارشی فروش با شماره فاکتور ' . $saleReport->invoice_id . ' را حذف کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
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

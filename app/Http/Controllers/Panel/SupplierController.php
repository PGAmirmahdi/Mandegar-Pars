<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function index()
    {
        $this->authorize('suppliers-list');

        $suppliers = Supplier::orderByRaw('-code DESC')->paginate(30);

        return view('panel.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $this->authorize('suppliers-create');

        return view('panel.suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        $this->authorize('suppliers-create');

        Supplier::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'code' => 'SU-' . random_int(10000000, 99999999),
            'supplier_type'=> $request->supplier_type,
            'economical_number' => $request->economical_number,
            'national_number' => $request->national_number,
            'postal_code' => $request->postal_code,
            'province' => $request->province,
            'city' => $request->city,
            'phone1' => $request->phone1,
            'phone2' => $request->phone2,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'description' => $request->description,
        ]);
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ایجاد تامین کننده',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')  تامین کننده به نام ' . $request->name . ' را ایجاد کرد.',
        ]);
        alert()->success('تامین کننده مورد نظر با موفقیت ایجاد شد','ایجاد تامین کننده');
        return redirect()->route('suppliers.index');
    }

    public function show(Supplier $suppliers)
    {
        $url = \request()->url;

        return view('panel.suppliers.show', compact('suppliers','url'));
    }

    public function edit(Supplier $suppliers)
    {
        $this->authorize('suppliers-edit');

        $url = \request()->url;

        return view('panel.suppliers.edit', compact('suppliers','url'));
    }

    public function update(UpdateCustomerRequest $request, Supplier $suppliers)
    {
        $this->authorize('customers-edit');
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ایجاد مشتری',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')  مشتری ' . $suppliers->name . ' را ویرایش کرد.',
        ]);
        $suppliers->update([
            'name' => $request->name,
            'code' => Gate::allows('sales-manager') ? $request->customer_code : $suppliers->code,
            'economical_number' => $request->economical_number,
            'national_number' => $request->national_number,
            'postal_code' => $request->postal_code,
            'province' => $request->province,
            'city' => $request->city,
            'phone1' => $request->phone1,
            'phone2' => $request->phone2,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'description' => $request->description,
        ]);
        $url = $request->url;

        alert()->success('تامین کننده مورد نظر با موفقیت ویرایش شد','ویرایش تامین کننده');
        return redirect($url);
    }

    public function destroy(Customer $customer)
    {
        $this->authorize('customers-delete');
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ایجاد مشتری',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')  مشتری ' . $customer->name . ' را حذف کرد.',
        ]);
        if ($customer->invoices()->exists()){
            return response('ابتدا سفارشات این مشتری را حذف کنید',500);
        }

        $customer->delete();
        return back();
    }

    public function search(Request $request)
    {
        $type = $request->type == 'all' ? array_keys(Customer::TYPE) : [$request->type];
        $customers = Customer::when($request->code, function ($q) use($request){
            $q->where('code', $request->code);
        })
            ->when($request->name, function ($q) use($request){
                $q->where('name','like', "%$request->name%");
            })
            ->whereIn('type', $type)
            ->orderByRaw('-code DESC')->paginate(30);

        return view('panel.customers.index', compact('customers'));
    }

    public function list()
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/suppliers-list.log'),
        ])->info(\request()->ip());

        $suppliers = Supplier::paginate(30);

        return view('panel.suppliers.list', compact('suppliers'));
    }

    public function getSupplierInfo(Supplier $supplier)
    {
        return response()->json(['data' => $supplier]);
    }

    public function excel()
    {
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'خروجی اکسل از تامین کنندگان',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ' از مشتریان خروجی اکسل گرفت',
        ]);
        return Excel::download(new \App\Exports\SuppliersExport, 'suppliers.xlsx');
    }
}

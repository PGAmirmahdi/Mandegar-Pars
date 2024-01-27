<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index()
    {
        $this->authorize('customers-list');

        if (auth()->user()->isAdmin() || auth()->user()->isCEO() || auth()->user()->isSalesManager()){
            $customers = Customer::orderByRaw('-code DESC')->paginate(30);
        }else{
            $customers = Customer::where('user_id', auth()->id())->orderByRaw('-code DESC')->paginate(30);
        }

        return view('panel.customers.index', compact('customers'));
    }

    public function create()
    {
        $this->authorize('customers-create');

        return view('panel.customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $this->authorize('customers-create');

        Customer::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'code' => $request->customer_code,
            'type' => $request->type,
            'customer_type' => $request->customer_type,
            'economical_number' => $request->economical_number,
            'national_number' => $request->national_number,
            'postal_code' => $request->postal_code,
            'province' => $request->province,
            'city' => $request->city,
            'phone1' => $request->phone1,
            'phone2' => $request->phone2,
            'phone3' => $request->phone3,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'description' => $request->description,
        ]);

        alert()->success('مشتری مورد نظر با موفقیت ایجاد شد','ایجاد مشتری');
        return redirect()->route('customers.index');
    }

    public function show(Customer $customer)
    {
        //
    }

    public function edit(Customer $customer)
    {
        $this->authorize('customers-edit');

        return view('panel.customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $this->authorize('customers-edit');

        $customer->update([
            'name' => $request->name,
            'code' => Gate::allows('sales-manager') ? $request->customer_code : $customer->code,
            'type' => $request->type,
            'customer_type' => $request->customer_type,
            'economical_number' => $request->economical_number,
            'national_number' => $request->national_number,
            'postal_code' => $request->postal_code,
            'province' => $request->province,
            'city' => $request->city,
            'phone1' => $request->phone1,
            'phone2' => $request->phone2,
            'phone3' => $request->phone3,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'description' => $request->description,
        ]);

        alert()->success('مشتری مورد نظر با موفقیت ویرایش شد','ویرایش مشتری');
        return redirect()->route('customers.index');
    }

    public function destroy(Customer $customer)
    {
        $this->authorize('customers-delete');

        $customer->delete();
        return back();
    }

    public function search(Request $request)
    {
        $this->authorize('customers-list');

        $province = $request->province == 'all' ? Province::pluck('name') : [$request->province];
        $customer_type = $request->customer_type == 'all' ? array_keys(Customer::CUSTOMER_TYPE) : [$request->customer_type];

        if (auth()->user()->isAdmin() || auth()->user()->isCEO() || auth()->user()->isSalesManager()){
            $customers = Customer::when($request->code, function ($q) use($request){
                    $q->where('code', $request->code);
                })
                ->when($request->name, function ($q) use($request){
                $q->where('name','like', "%$request->name%");
            })
                ->whereIn('province', $province)
                ->whereIn('customer_type', $customer_type)
                ->orderByRaw('-code DESC')->paginate(30);
        }else{
            $customers = Customer::where('user_id', auth()->id())
                ->when($request->code, function ($q) use($request){
                    $q->where('code', $request->code);
                })
                ->when($request->name, function ($q) use($request){
                    $q->where('name','like', "%$request->name%");
                })
                ->whereIn('province', $province)
                ->whereIn('customer_type', $customer_type)
                ->orderByRaw('-code DESC')->paginate(30);
        }

        return view('panel.customers.index', compact('customers'));
    }

    public function list()
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/customers-list.log'),
        ])->info(\request()->ip());

        $customers = Customer::paginate(30);

        return view('panel.customers.list', compact('customers'));
    }

    public function getCustomerInfo(Customer $customer)
    {
        return response()->json(['data' => $customer]);
    }

    public function excel()
    {
        return Excel::download(new \App\Exports\CustomersExport, 'customers.xlsx');
    }
}

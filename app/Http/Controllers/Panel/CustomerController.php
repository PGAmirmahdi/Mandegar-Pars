<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $this->authorize('customers-list');

        $customers = Customer::latest()->paginate(30);
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
            'name' => $request->name,
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

    public function getCustomerInfo(Customer $customer)
    {
        return response()->json(['data' => $customer]);
    }
}

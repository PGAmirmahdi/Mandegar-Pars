<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Customer;
use App\Models\ForeignCustomer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ForeignCustomerController extends Controller
{
    public function index()
    {
        $this->authorize('foreign-customers-list');
        $customers = ForeignCustomer::latest()->paginate(30);

        return view('panel.foreign-customers.index', compact('customers'));
    }

    public function create()
    {
        $this->authorize('foreign-customers-create');

        return view('panel.foreign-customers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('foreign-customers-create');

        $files = [];

        if ($request->file('docs')){
            foreach ($request->docs as $doc){
                $file = upload_file($doc, 'ForeignCustomers');
                $files[] = $file;
            }
        }

        ForeignCustomer::create([
            'website' => $request->website,
            'phone' => $request->phone,
            'email' => $request->email,
            'country' => $request->country != 'null' ? $request->country : null,
            'status' => $request->status,
            'products' => $request->products,
            'description' => $request->description,
            'docs' => count($files) ? json_encode($files) : null,
        ]);

        alert()->success('مشتری مورد نظر با موفقیت ثبت شد','ثبت مشتری');
        return redirect()->route('foreign-customers.index');
    }

    public function show(ForeignCustomer $foreignCustomer)
    {
        //
    }

    public function edit(ForeignCustomer $foreignCustomer)
    {
        $this->authorize('foreign-customers-edit');

        $page = \request()->page;

        return view('panel.foreign-customers.edit', compact('foreignCustomer','page'));
    }

    public function update(Request $request, ForeignCustomer $foreignCustomer)
    {
        $this->authorize('foreign-customers-edit');

        $files = [];

        if ($request->file('docs')){
            if ($foreignCustomer->docs){
                foreach (json_decode($foreignCustomer->docs) as $item){
                    unlink(public_path($item));
                }
            }

            foreach ($request->docs as $doc){
                $file = upload_file($doc, 'ForeignCustomers');
                $files[] = $file;
            }
        }

        $foreignCustomer->update([
            'website' => $request->website,
            'phone' => $request->phone,
            'email' => $request->email,
            'country' => $request->country != 'null' ? $request->country : null,
            'status' => $request->status,
            'products' => $request->products,
            'description' => $request->description,
            'docs' => count($files) ? json_encode($files) : $foreignCustomer->docs,
        ]);

        alert()->success('مشتری مورد نظر با موفقیت ویرایش شد','ویرایش مشتری');
        return redirect()->route('foreign-customers.index', ['page' => $request->page]);
    }

    public function destroy(ForeignCustomer $foreignCustomer)
    {
        $this->authorize('foreign-customers-delete');

        if ($foreignCustomer->docs){
            foreach (json_decode($foreignCustomer->docs) as $item){
                unlink(public_path($item));
            }
        }

        $foreignCustomer->delete();
        return back();
    }

    public function search(Request $request)
    {
        $this->authorize('foreign-customers-list');

        $status = $request->status == 'all' ? array_keys(ForeignCustomer::STATUS) : [$request->status];
        $country = $request->country == 'all' ? Country::pluck('fa_name') : [$request->country];

        $customers = ForeignCustomer::where(function ($q) use($country, $request){
            if ($request->country == 'all'){
                $q->whereIn('country', $country)->orWhereNull('country');
            }else{
                $q->whereIn('country', $country);
            }
        })->whereIn('status', $status)->latest()->paginate(30);

        return view('panel.foreign-customers.index', compact('customers'));
    }

    public function excel()
    {
        return Excel::download(new \App\Exports\ForeignCustomersExport, 'foreign-customers.xlsx');
    }
}

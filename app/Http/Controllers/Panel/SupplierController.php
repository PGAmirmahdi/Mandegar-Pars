<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Activity;
use App\Models\Category;
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
        $suppliers = Supplier::with('categories')->orderByRaw('-code DESC')->paginate(30);

        return view('panel.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $this->authorize('suppliers-create');
        $categories = Category::all();
        return view('panel.suppliers.create',compact('categories'));
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
            'category' => $request->category,
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

    public function show(Supplier $supplier)
    {
        $url = request()->url;
        $categories = collect();
        if ($supplier->category) {
            $categories = \App\Models\Category::whereIn('id', $supplier->category)->get();
        }
        return view('panel.suppliers.show', compact('supplier', 'url', 'categories'));
    }

    public function edit(Supplier $supplier)
    {
        $this->authorize('suppliers-edit');

        $url = \request()->url;
        $categories = Category::all();
        return view('panel.suppliers.edit', compact('supplier','url','categories'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $this->authorize('suppliers-edit');

        $supplier->update([
            'name'              => $request->name,
            'supplier_type'     => $request->supplier_type,
            'economical_number' => $request->economical_number,
            'national_number'   => $request->national_number,
            'postal_code'       => $request->postal_code,
            'province'          => $request->province,
            'city'              => $request->city,
            'category'          => $request->category,
            'phone1'            => $request->phone1,
            'phone2'            => $request->phone2,
            'address1'          => $request->address1,
            'address2'          => $request->address2,
            'description'       => $request->description,
        ]);

        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'به روز رسانی تامین کننده',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') تامین کننده به نام ' . $request->name . ' را به روز کرد.',
        ]);

        alert()->success('تامین کننده مورد نظر با موفقیت به روز شد', 'به روز رسانی تامین کننده');

        return redirect($request->url);
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorize('suppliers-delete');
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف تامین کننده',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')  تامین کننده به نام ' . $supplier->name . ' را حذف کرد.',
        ]);
        $supplier->delete();
        return back();
    }

    public function search(Request $request)
    {
        $type = $request->supplier_type == 'all' ? array_keys(Supplier::TYPE) : [$request->supplier_type];

        $query = Supplier::query();

        if ($request->code) {
            $query->where('code', $request->code);
        }

        if ($request->supplier && $request->supplier !== 'all') {
            $query->where('name', $request->supplier);
        }

        if ($request->user && $request->user !== 'all') {
            $query->where('user_id', $request->user);
        }

        if ($request->province && $request->province !== 'all') {
            $query->where('province', $request->province);
        }

        if ($request->category && $request->category !== 'all') {
            $query->whereJsonContains('category', $request->category);
        }

        $query->whereIn('supplier_type', $type);

        $suppliers = $query->orderByDesc('id')->paginate(30);

        return view('panel.suppliers.index', compact('suppliers'));
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
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ' از تامین کنندگان خروجی اکسل گرفت',
        ]);
        return Excel::download(new \App\Exports\SuppliersExport, 'suppliers.xlsx');
    }
    public function getRelevantSuppliers(Request $request)
    {
        $suppliers = Supplier::where('name', 'like', "%$request->name%")->pluck('name');

        return response()->json(['data' => $suppliers]);
    }
}

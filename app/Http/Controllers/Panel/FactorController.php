<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFactorRequest;
use App\Models\Customer;
use App\Models\Factor;
use App\Models\Invoice;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Province;
use App\Models\Role;
use App\Models\Seller;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use function Symfony\Component\String\b;

class FactorController extends Controller
{
    public function index()
    {
        $this->authorize('factors-list');

        if (auth()->user()->isAdmin() || auth()->user()->isWareHouseKeeper() || auth()->user()->isAccountant() || auth()->user()->isCEO()){
            $factors = Factor::latest()->paginate(30);
        }else{
            $factors = Factor::whereHas('invoice', function ($q){
                $q->where('user_id', auth()->id());
            })->latest()->paginate(30);
        }

        $permissionsId = Permission::whereIn('name', ['partner-tehran-user', 'partner-other-user', 'system-user', 'single-price-user'])->pluck('id');
        $roles_id = Role::whereHas('permissions', function ($q) use($permissionsId){
            $q->whereIn('permission_id', $permissionsId);
        })->pluck('id');

        $customers = auth()->user()->isAdmin() || auth()->user()->isWareHouseKeeper() || auth()->user()->isAccountant() || auth()->user()->isCEO() ? Customer::all(['id', 'name']) : Customer::where('user_id', auth()->id())->get(['id', 'name']);

        return view('panel.factors.index', compact('factors', 'customers', 'roles_id'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Factor $factor)
    {
        //
    }

    public function edit(Factor $factor)
    {
        // access to invoices-edit permission
        $this->authorize('factors-edit');

        // edit own invoice OR is admin
        $this->authorize('edit-factor', $factor->invoice);

        return view('panel.factors.edit', compact('factor'));
    }

    public function update(UpdateFactorRequest $request, Factor $factor)
    {
        // access to invoices-edit permission
        $this->authorize('factors-edit');

        // edit own invoice OR is admin
        $this->authorize('edit-factor', $factor->invoice);

        $invoice = $factor->invoice;

        $invoice->products()->detach();

        // create products for invoice
        $this->storeInvoiceProducts($invoice, $request);

        if ($factor->deposit_doc){
            unlink(public_path($factor->deposit_doc));
        }
        $deposit_doc = $request->file('deposit_doc') ? upload_file($request->file('deposit_doc'),'DepositDocs') : null;

        $type = $request->type;

        if ($type == 'unofficial'){
            if (!$invoice->seller){
                $seller = Seller::create([
                    'name' => $request->seller_name,
                    'phone' => $request->seller_phone,
                    'province' => $request->seller_province,
                    'city' => $request->seller_city,
                    'address' => $request->seller_address,
                ]);
            }else{
                $invoice->seller()->update([
                    'name' => $request->seller_name,
                    'phone' => $request->seller_phone,
                    'province' => $request->seller_province,
                    'city' => $request->seller_city,
                    'address' => $request->seller_address,
                ]);

                $seller = $invoice->seller;
            }
        }else{
            $seller = null;
            if ($invoice->seller){
                $invoice->update(['seller_id' => null]);
                $invoice->seller->delete();
            }
        }

        $invoice->update([
            'seller_id' => $seller ? $seller->id : null,
            'type' => $type,
            'economical_number' => $request->economical_number,
            'national_number' => $request->national_number,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'address' => $request->address,
            'discount' => $request->final_discount,
            'description' => $request->description,
        ]);

        $factor->update([
            'status' => $request->status,
            'deposit_doc' => $deposit_doc
        ]);

        alert()->success('فاکتور مورد نظر با موفقیت ویرایش شد','ویرایش فاکتور');
        return redirect()->route('factors.index');
    }

    public function destroy(Factor $factor)
    {
        $this->authorize('factors-delete');

        // if this factor has output from inventory redirect it
        if ($factor->inventory_report){
            return back();
        }

        $invoice = Invoice::find($factor->invoice_id);
        $factor->delete();
        $invoice->update(['status' => 'pending']);

        return back();
    }

    public function search(Request $request)
    {
        $this->authorize('factors-list');

        $customers = auth()->user()->isAdmin() || auth()->user()->isWareHouseKeeper() || auth()->user()->isAccountant() || auth()->user()->isCEO() ? Customer::all(['id', 'name']) : Customer::where('user_id', auth()->id())->get(['id', 'name']);

        $permissionsId = Permission::whereIn('name', ['partner-tehran-user', 'partner-other-user', 'system-user', 'single-price-user'])->pluck('id');
        $roles_id = Role::whereHas('permissions', function ($q) use($permissionsId){
            $q->whereIn('permission_id', $permissionsId);
        })->pluck('id');

        $customers_id = $request->customer_id == 'all' ? $customers->pluck('id') : [$request->customer_id];
        $status = $request->status == 'all' ? ['invoiced','paid'] : [$request->status];
        $province = $request->province == 'all' ? Province::pluck('name') : [$request->province];
        $user_id = $request->user == 'all' || $request->user == null ? User::whereIn('role_id', $roles_id)->pluck('id') : [$request->user];

        if (auth()->user()->isAdmin() || auth()->user()->isWareHouseKeeper() || auth()->user()->isAccountant() || auth()->user()->isCEO()){
            $factors = Factor::whereIn('status', $status)
                ->whereHas('invoice', function ($q) use($request, $customers_id, $status, $province, $user_id){
                $q->when($request->need_no, function ($q) use($request, $user_id){
                        return $q->where('need_no', $request->need_no);
                    })
                    ->whereIn('user_id', $user_id)
                    ->whereIn('customer_id', $customers_id)
                    ->whereIn('province', $province);
            })->latest()->paginate(30);
        }else{
            $factors = Factor::whereIn('status', $status)
                ->whereHas('invoice', function ($q) use($request, $customers_id, $status, $province){
                $q->where('user_id', auth()->id())->when($request->need_no, function ($q) use($request){
                        return $q->where('need_no', $request->need_no);
                    })
                        ->whereIn('customer_id', $customers_id)
                        ->whereIn('province', $province);
            })->latest()->paginate(30);
        }


        return view('panel.factors.index', compact('factors', 'customers','roles_id'));
    }

    public function excel()
    {
        return Excel::download(new \App\Exports\FactorsExport, 'factors.xlsx');
    }

    public function changeStatus(Factor $factor)
    {
        $this->authorize('accountant');

        if ($factor->created_in == 'website'){
            return back();
        }

        if ($factor->status == 'invoiced'){
            $factor->update(['status' => 'paid']);
        }else{
            $factor->update(['status' => 'invoiced']);
        }

        return back();
    }

    private function storeInvoiceProducts(Invoice $invoice, $request)
    {
        if ($request->products) {
            foreach ($request->products as $key => $product_id){
                if ($request->status == 'paid' && $request->status != $invoice->status){
                    // decrease product counts

                    $product = Product::find($product_id);
                    $properties = json_decode($product->properties);
                    $product_exist = array_keys(array_column($properties, 'color'), $request->colors[$key]);

                    if ($product_exist){
                        $properties[$product_exist[0]]->counts -= $request->counts[$key];
                        $changed_properties = json_encode($properties);
                        $product->update(['properties' => $changed_properties]);
                    }

                    $product->update(['total_count' => $product->total_count -= $request->counts[$key]]);
                }

                $invoice->products()->attach($product_id, [
                    'color' => $request->colors[$key],
                    'count' => $request->counts[$key],
                    'unit' => $request->units[$key],
                    'price' => $request->prices[$key],
                    'total_price' => $request->total_prices[$key],
                    'discount_amount' => $request->discount_amounts[$key],
                    'extra_amount' => $request->extra_amounts[$key],
                    'tax' => $request->taxes[$key],
                    'invoice_net' => $request->invoice_nets[$key],
                ]);

            }
        }

        $invoice->other_products()->delete();

        if ($request->other_products){
            foreach ($request->other_products as $key => $product){
                $invoice->other_products()->create([
                    'title' => $product,
                    'color' => $request->other_colors[$key],
                    'count' => $request->other_counts[$key],
                    'unit' => $request->other_units[$key],
                    'price' => $request->other_prices[$key],
                    'total_price' => $request->other_total_prices[$key],
                    'discount_amount' => $request->other_discount_amounts[$key],
                    'extra_amount' => $request->other_extra_amounts[$key],
                    'tax' => $request->other_taxes[$key],
                    'invoice_net' => $request->other_invoice_nets[$key],
                ]);
            }
        }
    }
}

<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Factor;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Province;
use Illuminate\Http\Request;

class FactorController extends Controller
{
    public function index()
    {
        $this->authorize('invoices-list');

        if (auth()->user()->isAdmin()){
            $factors = Factor::latest()->paginate(30);
        }else{
            $factors = Factor::whereHas('invoice', function ($q){
                $q->where('user_id', auth()->id());
            })->latest()->paginate(30);
        }

        $customers = auth()->user()->isAdmin() ? Customer::all(['id', 'name']) : Customer::where('user_id', auth()->id())->get(['id', 'name']);

        return view('panel.factors.index', compact('factors', 'customers'));
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
        $this->authorize('invoices-edit');

        // edit own invoice OR is admin
        $this->authorize('edit-invoice', $factor->invoice);

        return view('panel.factors.edit', compact('factor'));
    }

    public function update(Request $request, Factor $factor)
    {
        // access to invoices-edit permission
        $this->authorize('invoices-edit');

        // edit own invoice OR is admin
        $this->authorize('edit-invoice', $factor->invoice);

        $invoice = $factor->invoice;

        $invoice->products()->detach();

        // create products for invoice
        $this->storeInvoiceProducts($invoice, $request);

        $invoice->update([
            'economical_number' => $request->economical_number,
            'national_number' => $request->national_number,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'address' => $request->address,
        ]);

        $factor->update(['status' => $request->status]);

        alert()->success('فاکتور مورد نظر با موفقیت ویرایش شد','ویرایش فاکتور');
        return redirect()->route('factors.index');
    }

    public function destroy(Factor $factor)
    {
        $this->authorize('invoices-delete');

        $invoice = Invoice::find($factor->invoice_id);
        $factor->delete();
        $invoice->update(['status' => 'pending']);

        return back();
    }

    public function search(Request $request)
    {
        $this->authorize('invoices-list');
        $customers = auth()->user()->isAdmin() ? Customer::all(['id', 'name']) : Customer::where('user_id', auth()->id())->get(['id', 'name']);

        $customers_id = $request->customer_id == 'all' ? $customers->pluck('id') : [$request->customer_id];
        $status = $request->status == 'all' ? ['invoiced','paid'] : [$request->status];
        $province = $request->province == 'all' ? Province::pluck('name') : [$request->province];

        if (auth()->user()->isAdmin()){
            $factors = Factor::whereIn('status', $status)
                ->whereHas('invoice', function ($q) use($request, $customers_id, $status, $province){
                $q->when($request->need_no, function ($q) use($request){
                        return $q->where('need_no', $request->need_no);
                    })
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


        return view('panel.factors.index', compact('factors', 'customers'));
    }

    private function storeInvoiceProducts(Invoice $invoice, $request)
    {
        if (!$request->products) {
            return back();
        }

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

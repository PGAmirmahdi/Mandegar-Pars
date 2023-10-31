<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Factor;
use App\Models\Invoice;
use App\Models\Product;
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

        return view('panel.factors.index', compact('factors'));
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
    }
}

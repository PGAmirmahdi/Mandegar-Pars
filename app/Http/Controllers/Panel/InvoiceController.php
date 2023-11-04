<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Coupon;
use App\Models\Factor;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    const TAX_AMOUNT = 0.09;

    public function index()
    {
        $this->authorize('invoices-list');

        if (auth()->user()->isAdmin()){
            $invoices = Invoice::where('created_in', 'automation')->where('status','!=','invoiced')->latest()->paginate(30);
        }else{
            $invoices = Invoice::where('created_in', 'automation')->where('user_id', auth()->id())->where('status','!=','invoiced')->latest()->paginate(30);
        }

        return view('panel.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $this->authorize('invoices-create');

        return view('panel.invoices.create');
    }

    public function store(StoreInvoiceRequest $request)
    {
        $this->authorize('invoices-create');

        $invoice = Invoice::create([
            'user_id' => auth()->id(),
            'customer_id' => $request->buyer_name,
            'economical_number' => $request->economical_number,
            'national_number' => $request->national_number,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'address' => $request->address,
            'created_in' => 'automation',
//            'status' => $request->status,
        ]);

        // create products for invoice
        $this->storeInvoiceProducts($invoice, $request);

        alert()->success('پیش فاکتور مورد نظر با موفقیت ایجاد شد','ایجاد پیش فاکتور');
        return redirect()->route('invoices.edit', $invoice->id);
    }

    public function show(Invoice $invoice)
    {
        // access to invoices-edit permission
        $this->authorize('invoices-edit');

        // edit own invoice OR is admin
        $this->authorize('edit-invoice', $invoice);

        $factor = \request()->type == 'factor' ? $invoice->factor : null;

        return view('panel.invoices.printable', compact('invoice','factor'));
    }

    public function edit(Invoice $invoice)
    {
        // access to invoices-edit permission
        $this->authorize('invoices-edit');

        // edit own invoice OR is admin
        $this->authorize('edit-invoice', $invoice);

        if ($invoice->created_in == 'website'){
            return back();
        }

        return view('panel.invoices.edit', compact('invoice'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        // access to invoices-edit permission
        $this->authorize('invoices-edit');

        // edit own invoice OR is admin
        $this->authorize('edit-invoice', $invoice);

        $invoice->products()->detach();

        // create products for invoice
        $this->storeInvoiceProducts($invoice, $request);

        $invoice->update([
            'user_id' => auth()->id(),
            'customer_id' => $request->buyer_name,
            'economical_number' => $request->economical_number,
            'national_number' => $request->national_number,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        // create factor
        if ($request->status == 'invoiced'){
            $invoice->factor()->updateOrCreate(['status' => 'invoiced']);
        }

        alert()->success('پیش فاکتور مورد نظر با موفقیت ویرایش شد','ویرایش پیش فاکتور');
        return redirect()->route('invoices.index');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('invoices-delete');

        $invoice->coupons()->detach();
        $invoice->delete();
        return back();
    }

    public function calcProductsInvoice(Request $request)
    {
        $usedCoupon = DB::table('coupon_invoice')->where([
            'product_id' => $request->product_id,
            'invoice_id' => $request->invoice_id,
        ])->first();

        $product = Product::find($request->product_id);
        $price = $product->getPrice();
        $total_price = $price * $request->count;

        if ($usedCoupon){
            $coupon = Coupon::find($usedCoupon->coupon_id);
            $discount_amount = $total_price * ($coupon->amount_pc / 100);
        }else{
            $discount_amount = 0;
        }

        $extra_amount = 0;
        $total_price_with_off = $total_price - ($discount_amount + $extra_amount);
        $tax = (int) ($total_price_with_off * self::TAX_AMOUNT);
        $invoice_net = $tax + $total_price_with_off;

        $data = [
            'price' => $price,
            'total_price' => $total_price,
            'discount_amount' => $discount_amount,
            'extra_amount' => $extra_amount,
            'total_price_with_off' => $total_price_with_off,
            'tax' => $tax,
            'invoice_net' => $invoice_net,
        ];

        return response()->json(['data' => $data]);
    }

    public function calcOtherProductsInvoice(Request $request)
    {
        $price = $request->price;
        $total_price = $price * $request->count;
        $discount_amount = $request->discount_amount;

        $extra_amount = 0;
        $total_price_with_off = $total_price - ($discount_amount + $extra_amount);
        $tax = (int) ($total_price_with_off * self::TAX_AMOUNT);
        $invoice_net = $tax + $total_price_with_off;

        $data = [
            'price' => $price,
            'total_price' => $total_price,
            'discount_amount' => $discount_amount,
            'extra_amount' => $extra_amount,
            'total_price_with_off' => $total_price_with_off,
            'tax' => $tax,
            'invoice_net' => $invoice_net,
        ];

        return response()->json(['data' => $data]);
    }

    public function search(Request $request)
    {
        $this->authorize('invoices-list');
        $status = $request->status == 'all' ? array_keys(Invoice::STATUS) : [$request->status];

        $invoices = Invoice::whereIn('status', $status)->latest()->paginate(30);
        return view('panel.invoices.index', compact('invoices'));
    }

    public function applyDiscount(Request $request)
    {
        $coupon = Coupon::whereCode($request->code)->first();

        if (!$coupon){
            return response()->json(['error' => 1, 'message' => 'کد وارد شده صحیح نیست']);
        }

        $usedCoupon = DB::table('coupon_invoice')->where([
            'coupon_id' => $coupon->id,
            'product_id' => $request->product_id,
            'invoice_id' => $request->invoice_id,
        ])->exists();

        if ($usedCoupon){
            return response()->json(['error' => 1, 'message' => 'این کد تخفیف قبلا برای این کالا اعمال شده است']);
        }

        DB::table('coupon_invoice')->insert([
            'user_id' => auth()->id(),
            'coupon_id' => $coupon->id,
            'product_id' => $request->product_id,
            'invoice_id' => $request->invoice_id,
            'created_at' => now(),
        ]);

        $product = Product::find($request->product_id);
        $price = $product->getPrice();
        $total_price = $price * $request->count;
        $discount_amount = $total_price * ($coupon->amount_pc / 100);
        $extra_amount = 0;
        $total_price_with_off = $total_price - ($discount_amount + $extra_amount);
        $tax = (int) ($total_price_with_off * self::TAX_AMOUNT);
        $invoice_net = $tax + $total_price_with_off;


        DB::table('invoice_product')->where([
            'invoice_id' => $request->invoice_id,
            'product_id' => $request->product_id,
        ])->update([
            'price' => $price,
            'total_price' => $total_price,
            'discount_amount' => $discount_amount,
            'extra_amount' => $extra_amount,
            'tax' => $tax,
            'invoice_net' => $invoice_net,
        ]);

        $data = [
            'price' => $price,
            'total_price' => $total_price,
            'discount_amount' => $discount_amount,
            'extra_amount' => $extra_amount,
            'total_price_with_off' => $total_price_with_off,
            'tax' => $tax,
            'invoice_net' => $invoice_net,
        ];

        return response()->json(['error' => 0, 'message' => 'کد تخفیف اعمال شد', 'data' => $data]);
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

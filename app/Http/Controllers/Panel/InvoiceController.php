<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Factor;
use App\Models\Invoice;
use App\Models\InvoiceAction;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Province;
use App\Models\Role;
use App\Models\Seller;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules\In;
use Maatwebsite\Excel\Facades\Excel;
use PDF as PDF;

class InvoiceController extends Controller
{
    const TAX_AMOUNT = 0.1;

    public function index()
    {
        $this->authorize('invoices-list');

        if (auth()->user()->isAdmin() || auth()->user()->isWareHouseKeeper() || auth()->user()->isAccountant() || auth()->user()->isCEO() || auth()->user()->isSalesManager()){
            $invoices = Invoice::latest()->paginate(30);
        }else{
            $invoices = Invoice::where('user_id', auth()->id())->latest()->paginate(30);
        }

        $permissionsId = Permission::whereIn('name', ['partner-tehran-user', 'partner-other-user', 'system-user', 'single-price-user'])->pluck('id');
        $roles_id = Role::whereHas('permissions', function ($q) use($permissionsId){
            $q->whereIn('permission_id', $permissionsId);
        })->pluck('id');

        $customers = auth()->user()->isAdmin() || auth()->user()->isAccountant() || auth()->user()->isCEO() || auth()->user()->isWareHouseKeeper() || auth()->user()->isSalesManager() ? Customer::all(['id', 'name']) : Customer::where('user_id', auth()->id())->get(['id', 'name']);

        return view('panel.invoices.index', compact('invoices','customers','roles_id'));
    }

    public function create()
    {
        $this->authorize('invoices-create');

        $seller = Seller::first();
        return view('panel.invoices.create', compact('seller'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $this->authorize('invoices-create');

        $type = $request->type;
        $req_for = $request->req_for;

        $seller = Seller::first();

        $invoice = Invoice::create([
            'user_id' => auth()->id(),
            'seller_id' => $seller ? $seller->id : null,
            'customer_id' => $request->buyer_name,
            'economical_number' => $request->economical_number,
            'national_number' => $request->national_number,
            'need_no' => $request->need_no,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'address' => $request->address,
            'created_in' => 'automation',
            'type' => $type,
            'req_for' => $req_for,
//            'status' => $request->status,
            'discount' => $request->final_discount,
            'description' => $request->description,
        ]);

        $this->send_notif_to_accountants($invoice);
        $this->send_notif_to_sales_manager($invoice);

        // create products for invoice
        $this->storeInvoiceProducts($invoice, $request);

        // create order status
        $invoice->order_status()->create(['order' => 1, 'status' => 'register']);

        alert()->success('سفارش مورد نظر با موفقیت ثبت شد','ثبت سفارش');
        return redirect()->route('invoices.edit', $invoice->id);
    }

    public function show(Invoice $invoice)
    {
        // edit own invoice OR is admin
        if (Gate::allows('edit-invoice', $invoice) || auth()->user()->isWareHouseKeeper() || auth()->user()->isExitDoor()){
            $factor = \request()->type == 'factor' ? $invoice->factor : null;

            return view('panel.invoices.printable', compact('invoice','factor'));
        }else{
            abort(403);
        }
    }

    public function edit(Invoice $invoice)
    {
        // access to invoices-edit permission
        $this->authorize('invoices-edit');

        // edit own invoice OR is admin
        $this->authorize('edit-invoice', $invoice);

        if (Gate::allows('sales-manager')){
            if ($invoice->created_in == 'website'){
                return back();
            }
        }else{
            if ($invoice->created_in == 'website' || ($invoice->status == 'invoiced' && $invoice->req_for != 'amani-invoice')){
                return back();
            }
        }

        $seller = Seller::first();

        return view('panel.invoices.edit', compact('invoice','seller'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        // access to invoices-edit permission
        $this->authorize('invoices-edit');

        // edit own invoice OR is admin
        $this->authorize('edit-invoice', $invoice);

        if (!Gate::allows('sales-manager')){
            if (($invoice->status == 'invoiced' && $invoice->req_for != 'amani-invoice')){
                return back();
            }
        }

        if ($invoice->status != 'invoiced' || Gate::allows('sales-manager')){
            $invoice->products()->detach();

            // create products for invoice
            $this->storeInvoiceProducts($invoice, $request);
        }

//        send notif to creator of the invoice
        if ($request->status != $invoice->status){
            $status = Invoice::STATUS[$request->status];
            $url = route('invoices.index');
            $message = "وضعیت سفارش شماره {$invoice->id} به '{$status}' تغییر یافت";

            Notification::send($invoice->user, new SendMessage($message, $url));
        }

        $type = $request->type;
        $req_for = $request->req_for;

        $seller = Seller::first();

        $invoice->update([
            'customer_id' => $request->buyer_name,
            'seller_id' => $seller ? $seller->id : null,
            'type' => $type,
            'req_for' => $req_for,
            'economical_number' => $request->economical_number,
            'national_number' => $request->national_number,
            'need_no' => $request->need_no,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'address' => $request->address,
            'status' => $request->status,
            'discount' => $request->final_discount ?? $invoice->discount,
            'description' => $request->description,
        ]);

        alert()->success('سفارش مورد نظر با موفقیت ویرایش شد','ویرایش سفارش');
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
        $unofficial = (bool) $request->unofficial;

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
        $tax = $unofficial ? 0 : (int) ($total_price_with_off * self::TAX_AMOUNT);
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
        $unofficial = (bool) $request->unofficial;
        $price = $request->price;
        $total_price = $price * $request->count;
        $discount_amount = $request->discount_amount;

        $extra_amount = 0;
        $total_price_with_off = $total_price - ($discount_amount + $extra_amount);
        $tax = $unofficial ? 0 : (int) ($total_price_with_off * self::TAX_AMOUNT);
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
        $customers = auth()->user()->isAdmin() || auth()->user()->isWareHouseKeeper() || auth()->user()->isAccountant() || auth()->user()->isCEO() || auth()->user()->isSalesManager() ? Customer::all(['id', 'name']) : Customer::where('user_id', auth()->id())->get(['id', 'name']);

        $permissionsId = Permission::whereIn('name', ['partner-tehran-user', 'partner-other-user', 'system-user', 'single-price-user'])->pluck('id');
        $roles_id = Role::whereHas('permissions', function ($q) use($permissionsId){
            $q->whereIn('permission_id', $permissionsId);
        })->pluck('id');

        $customers_id = $request->customer_id == 'all' ? $customers->pluck('id') : [$request->customer_id];
        $status = $request->status == 'all' ? ['pending','return','invoiced','order'] : [$request->status];
        $province = $request->province == 'all' ? Province::pluck('name') : [$request->province];
        $user_id = $request->user == 'all' || $request->user == null ? User::whereIn('role_id', $roles_id)->pluck('id') : [$request->user];

//        dd($user_id);
        if (auth()->user()->isAdmin() || auth()->user()->isWareHouseKeeper() || auth()->user()->isAccountant() || auth()->user()->isCEO() || auth()->user()->isSalesManager()){
            $invoices = Invoice::when($request->need_no, function ($q) use($request){
                    return $q->where('need_no', $request->need_no);
                })
                ->whereIn('user_id', $user_id)
                ->whereIn('customer_id', $customers_id)
                ->whereIn('status', $status)
                ->whereIn('province', $province)
                ->latest()->paginate(30);
        }else{
            $invoices = Invoice::when($request->need_no, function ($q) use($request){
                    return $q->where('need_no', $request->need_no);
                })                ->whereIn('customer_id', $customers_id)
                ->whereIn('status', $status)
                ->whereIn('province', $province)
                ->where('user_id', auth()->id())
                ->latest()->paginate(30);
        }

        return view('panel.invoices.index', compact('invoices','customers','roles_id'));
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

    public function excel()
    {
        return Excel::download(new \App\Exports\InvoicesExport, 'invoices.xlsx');
    }

    public function changeStatus(Invoice $invoice)
    {
        $this->authorize('accountant');

        if ($invoice->created_in == 'website' || $invoice->factor){
            return back();
        }

        $roles_id = Role::whereHas('permissions', function ($q){
            $q->where('name', 'sales-manager');
        })->pluck('id');
        $sales_manager = User::where('id','!=', auth()->id())->whereIn('role_id', $roles_id)->get();

        if ($invoice->status == 'pending'){
            $invoice->update(['status' => 'invoiced']);

            $status = Invoice::STATUS[$invoice->status];
            $url = route('invoices.index');
            $message = " وضعیت سفارش {$invoice->customer->name} به '{$status}' تغییر یافت";

            Notification::send($invoice->user, new SendMessage($message, $url));
            Notification::send($sales_manager, new SendMessage($message, $url));
        }else{
            $status = Invoice::STATUS['pending'];
            $url = route('invoices.index');
            $message = " وضعیت سفارش {$invoice->customer->name} به '{$status}' تغییر یافت";

            Notification::send($invoice->user, new SendMessage($message, $url));
            Notification::send($sales_manager, new SendMessage($message, $url));

            $invoice->update(['status' => 'pending']);
        }

        return back();
    }

    public function downloadPDF(Request $request)
    {
        $invoice = Invoice::find($request->invoice_id);

        $pdf = PDF::loadView('panel.pdf.invoice',['invoice' => $invoice],[], [
            'format' => 'A3',
            'orientation' => 'L',
            'margin_left' => 2,
            'margin_right' => 2,
            'margin_top' => 2,
            'margin_bottom' => 0,
        ]);

        return $pdf->stream("order.pdf");
    }

    public function action(Invoice $invoice)
    {
        if (!Gate::allows('accountant') && $invoice->action == null){
            return back();
        }

        return view('panel.invoices.action', compact('invoice'));
    }

    public function actionStore(Invoice $invoice, Request $request)
    {
        $status = $request->status;

        if ($request->has('send_to_accountant')){
            if (!$request->has('confirm')){
                alert()->error('لطفا تیک تایید پیش فاکتور را بزنید', 'عدم تایید');
                return back();
            }

            $invoice->action()->updateOrCreate([
                'invoice_id' => $invoice->id
            ], [
                'acceptor_id' => auth()->id(),
                'confirm' => 1
            ]);

            $title = 'ثبت و ارسال به حسابدار';
            $message = 'تاییدیه شما به حسابداری ارسال شد';

            //send notif to accountants
            $permissionsId = Permission::where('name', 'accountant')->pluck('id');
            $roles_id = Role::whereHas('permissions', function ($q) use($permissionsId){
                $q->whereIn('permission_id', $permissionsId);
            })->pluck('id');

            $url = route('invoice.action', $invoice->id);
            $notif_message = "پیش فاکتور سفارش {$invoice->customer->name} مورد تایید قرار گرفت";
            $accountants = User::whereIn('role_id', $roles_id)->get();
            Notification::send($accountants, new SendMessage($notif_message, $url));
            //end send notif to accountants

        }elseif ($request->has('send_to_warehouse')){
            $request->validate(['factor_file' => 'required|mimes:pdf|max:5000']);

            $file = upload_file($request->factor_file, 'Action/Factors');
            $invoice->action()->updateOrCreate([
                'invoice_id' => $invoice->id
            ], [
                'factor_file' => $file,
                'sent_to_warehouse' => 1
            ]);

            $title = 'ثبت و ارسال به انبار';
            $message = 'فاکتور مورد نظر با موفقیت به انبار ارسال شد';

            $invoice->update(['status' => 'invoiced']);

            //send notif to warehouse-keeper and sales-manager
            $permissionsId = Permission::whereIn('name', ['warehouse-keeper','sales-manager'])->pluck('id');
            $roles_id = Role::whereHas('permissions', function ($q) use($permissionsId){
                $q->whereIn('permission_id', $permissionsId);
            })->pluck('id');

            $url = route('invoices.index');
            $notif_message = "فاکتور {$invoice->customer->name} دریافت شد";
            $accountants = User::whereIn('role_id', $roles_id)->get();
            Notification::send($accountants, new SendMessage($notif_message, $url));
            //end send notif to warehouse-keeper and sales-manager
        }else{
            if ($status == 'invoice'){
                $request->validate(['invoice_file' => 'required|mimes:pdf|max:5000']);

                $file = upload_file($request->invoice_file, 'Action/Invoices');
                $invoice->action()->updateOrCreate([
                    'invoice_id' => $invoice->id
                ], [
                    'status' => $status,
                    'invoice_file' => $file
                ]);

                $title = 'ثبت و ارسال پیش فاکتور';
                $message = 'پیش فاکتور مورد نظر با موفقیت به همکار فروش ارسال شد';

                //send notif
                $roles_id = Role::whereHas('permissions', function ($q){
                    $q->where('name', 'sales-manager');
                })->pluck('id');
                $sales_manager = User::where('id','!=', auth()->id())->whereIn('role_id', $roles_id)->get();

                $url = route('invoice.action', $invoice->id);
                $notif_message = "پیش فاکتور {$invoice->customer->name} دریافت شد";
                Notification::send($invoice->user, new SendMessage($notif_message, $url));
                Notification::send($sales_manager, new SendMessage($notif_message, $url));
                //end send notif
            }else{
                $request->validate(['factor_file' => 'required|mimes:pdf|max:5000']);

                $file = upload_file($request->factor_file, 'Action/Factors');
                $invoice->action()->updateOrCreate([
                    'invoice_id' => $invoice->id
                ], [
                    'status' => $status,
                    'factor_file' => $file
                ]);

                $title = 'ثبت و ارسال فاکتور';
                $message = 'فاکتور مورد نظر با موفقیت به انبار ارسال شد';

                //send notif to warehouse-keeper and sales-manager
                $permissionsId = Permission::whereIn('name', ['warehouse-keeper','sales-manager'])->pluck('id');
                $roles_id = Role::whereHas('permissions', function ($q) use($permissionsId){
                    $q->whereIn('permission_id', $permissionsId);
                })->pluck('id');

                $url = route('invoices.index');
                $notif_message = "فاکتور {$invoice->customer->name} دریافت شد";
                $accountants = User::whereIn('role_id', $roles_id)->get();
                Notification::send($accountants, new SendMessage($notif_message, $url));
                //end send notif to warehouse-keeper and sales-manager
            }

            $status = $status == 'invoice' ? 'pending' : 'invoiced';
            $invoice->update(['status' => $status]);
        }

        alert()->success($message, $title);
        return back();
    }

    public function deleteInvoiceFile(InvoiceAction $invoiceAction)
    {
        unlink(public_path($invoiceAction->invoice_file));
        $invoiceAction->delete();

        alert()->success('فایل پیش فاکتور مورد نظر حذف شد','حذف پیش فاکتور');
        return back();
    }

    public function deleteFactorFile(InvoiceAction $invoiceAction)
    {
        unlink(public_path($invoiceAction->factor_file));
        $invoiceAction->delete();

        alert()->success('فایل فاکتور مورد نظر حذف شد','حذف فاکتور');
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

    private function send_notif_to_accountants(Invoice $invoice)
    {
        $roles_id = Role::whereHas('permissions', function ($q){
            $q->where('name', 'accountant');
        })->pluck('id');
        $accountants = User::where('id','!=', auth()->id())->whereIn('role_id', $roles_id)->get();

        $url = route('invoices.edit', $invoice->id);
        $message = "سفارش '{$invoice->customer->name}' ثبت شد";

        Notification::send($accountants, new SendMessage($message, $url));
    }

    private function send_notif_to_sales_manager(Invoice $invoice)
    {
        $roles_id = Role::whereHas('permissions', function ($q){
            $q->where('name', 'sales-manager');
        })->pluck('id');
        $managers = User::where('id','!=', auth()->id())->whereIn('role_id', $roles_id)->get();

        $url = route('invoices.edit', $invoice->id);
        $message = "سفارش '{$invoice->customer->name}' ثبت شد";

        Notification::send($managers, new SendMessage($message, $url));
    }
}

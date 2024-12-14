<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Activity;
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
use Illuminate\Support\Facades\Log;
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

//        if (auth()->user()->isAdmin() || auth()->user()->isWareHouseKeeper() || auth()->user()->isAccountant() || auth()->user()->isCEO() || auth()->user()->isSalesManager() || auth()->user()->name === 'اشکان'){
            $invoices = Invoice::latest()->paginate(30);
//        }else{
//            $invoices = Invoice::where('user_id', auth()->id())->latest()->paginate(30);
//        }

        $permissionsId = Permission::whereIn('name', ['partner-tehran-user', 'partner-other-user', 'system-user', 'single-price-user'])->pluck('id');
        $roles_id = Role::whereHas('permissions', function ($q) use($permissionsId){
            $q->whereIn('permission_id', $permissionsId);
        })->pluck('id');

        $customers = auth()->user()->isAdmin() || auth()->user()->isAccountant() || auth()->user()->isOrgan() || auth()->user()->isCEO() || auth()->user()->isWareHouseKeeper() || auth()->user()->isSalesManager() ? Customer::all(['id', 'name']) : Customer::where('user_id', auth()->id())->get(['id', 'name']);

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
            'payment_type' => $request->payment_type
        ]);
        $customer = $invoice->customer; // or get customer based on your structure
        $data = [
            'user_id' => auth()->id(),
            'action' => 'ایجاد سفارش فروش',
            'description' => 'کاربر ' . auth()->user()->family . '(' . auth()->user()->role->label . ') سفارش فروش برای مشتری ' . ($customer ? $customer->name : 'نامشخص') . ' به شماره سفارش ' . $invoice->id . ' ایجاد کرد',
        ];

        Log::info('Activity Data:', $data);

        Activity::create($data);
//        $this->send_notif_to_accountants($invoice);
//        $this->send_notif_to_sales_manager($invoice);

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
//        if (Gate::allows('edit-invoice', $invoice) || auth()->user()->isWareHouseKeeper() || auth()->user()->isExitDoor()){
            $factor = \request()->type == 'factor' ? $invoice->factor : null;

            return view('panel.invoices.printable', compact('invoice','factor'));
//        }else{
//            abort(403);
//        }
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
            'payment_type' => $request->payment_type
        ]);
        $customer = $invoice->customer; // or get customer based on your structure
        $data = [
            'user_id' => auth()->id(),
            'action' => 'ویرایش سفارش فروش',
            'description' => 'کاربر ' . auth()->user()->family . '(' . auth()->user()->role->label . ') سفارش فروش برای مشتری ' . ($customer ? $customer->name : 'نامشخص') . ' به شماره سفارش ' . $invoice->id . ' را ویرایش کرد',
        ];

        Log::info('Activity Data:', $data);

        Activity::create($data);
        alert()->success('سفارش مورد نظر با موفقیت ویرایش شد','ویرایش سفارش');
        return redirect()->route('invoices.index');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('invoices-delete');
        $customer = $invoice->customer; // or get customer based on your structure
        $data = [
            'user_id' => auth()->id(),
            'action' => 'حذف سفارش فروش',
            'description' => 'کاربر ' . auth()->user()->family . '(' . auth()->user()->role->label . ') سفارش فروش برای مشتری ' . ($customer ? $customer->name : 'نامشخص') . $invoice->id . ' را حذف کرد',
        ];

        Log::info('Activity Data:', $data);

        Activity::create($data);
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

        // اصلاح محاسبه مالیات
        $tax_percentage = self::TAX_AMOUNT; // فرض می‌کنیم این درصد مالیات است (مثلاً 9)
        $tax = $tax_percentage * $total_price_with_off;

        $invoice_net =  $tax + $total_price_with_off + $extra_amount;

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

        $customers = auth()->user()->hasAnyRole(['Admin', 'Organ', 'WareHouseKeeper', 'Accountant', 'CEO', 'SalesManager'])
            ? Customer::all(['id', 'name'])
            : Customer::where('user_id', auth()->id())->get(['id', 'name']);

        $roles_id = Role::whereHas('permissions', function ($q) {
            $q->whereIn('permission_id', Permission::whereIn('name', [
                'partner-tehran-user',
                'partner-other-user',
                'system-user',
                'single-price-user'
            ])->pluck('id'));
        })->pluck('id');

        $invoices = Invoice::query()
            ->when($request->customer_id && $request->customer_id !== 'all', function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            })
            ->when($request->type && $request->type !== 'all', function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->province && $request->province !== 'all', function ($query) use ($request) {
                $query->where('province', $request->province);
            })
            ->when($request->status && $request->status !== 'all', function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->payment_type && $request->payment_type !== 'all', function ($query) use ($request) {
                $query->where('payment_type', $request->payment_type);
            })
            ->when($request->need_no, function ($query) use ($request) {
                $query->where('need_no', $request->need_no);
            })
            ->when($request->user && $request->user !== 'all', function ($query) use ($request) {
                $query->where('user_id', $request->user);
            })
            ->latest()
            ->paginate(30);

        return view('panel.invoices.index', [
            'invoices' => $invoices,
            'customers' => $customers,
            'roles_id' => $roles_id,
        ]);
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
        $data2 = [
            'user_id' => auth()->id(),
            'action' => 'استفاده از کد تخفیف',
            'description' => 'کاربر ' . auth()->user()->family . '(' . auth()->user()->role->label . ') از کد تخفیف ' . $coupon->code . ' در سفارش ' . $request->invoice_id . ' استفاده کرد',
        ];

        Log::info('Activity Data:', $data2);
        Activity::create($data2);
        return response()->json(['error' => 0, 'message' => 'کد تخفیف اعمال شد', 'data' => $data]);
    }

    public function excel()
    {
        $data = [
            'user_id' => auth()->id(),
            'action' => 'خروجی اکسل از فاکتور سفارش فروش',
            'description' => 'کاربر ' . auth()->user()->family . '(' . auth()->user()->role->label . ') از فاکتور های سفارش فروش خروجی اکسل گرفت',
        ];

// لاگ کردن داده‌ها
        Log::info('Activity Data:', $data);

// ذخیره در دیتابیس
        Activity::create($data);
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
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'تغییر وضعیت سفارش',
                'description' => "کاربر " . auth()->user()->family . " (" . auth()->user()->role->label . ") " .
                    "وضعیت سفارش مشتری {$invoice->customer->name} " .
                    "به شماره سفارش {$invoice->id} را به وضعیت {$status} تغییر داد",
            ]);
            Notification::send($invoice->user, new SendMessage($message, $url));
            Notification::send($sales_manager, new SendMessage($message, $url));

        }else{
            $status = Invoice::STATUS['pending'];
            $url = route('invoices.index');
            $message = " وضعیت سفارش {$invoice->customer->name} به '{$status}' تغییر یافت";

            Notification::send($invoice->user, new SendMessage($message, $url));
            Notification::send($sales_manager, new SendMessage($message, $url));

            $invoice->update(['status' => 'pending']);
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'تغییر وضعیت سفارش',
                'description' => "کاربر " . auth()->user()->family . " (" . auth()->user()->role->label . ") " .
                    "وضعیت سفارش مشتری {$invoice->customer->name} " .
                    "به شماره سفارش {$invoice->id} را به وضعیت {$status} تغییر داد",
            ]);
        }

        return back();
    }

    public function downloadPDF(Request $request)
    {
        $invoice = Invoice::find($request->invoice_id);
        if (!$invoice) {
            return back()->withErrors(['message' => 'فاکتور مورد نظر یافت نشد.']);
        }
        $pdf = PDF::loadView('panel.pdf.invoice',['invoice' => $invoice],[], [
            'format' => 'A3',
            'orientation' => 'L',
            'margin_left' => 2,
            'margin_right' => 2,
            'margin_top' => 2,
            'margin_bottom' => 0,
        ]);
        $customer = $invoice->customer; // or get customer based on your structure
        // ثبت فعالیت
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'دانلود PDF',
            'description' => "کاربر " . auth()->user()->family . '(' . auth()->user()->role->label . ')' . " فایل PDF مربوط به سفارش مشتری " . ($customer ? $customer->name : 'نامشخص') . ' به شماره سفارش ' . $invoice->id . ' را دانلود کرد',
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
            // ثبت فعالیت
            $customer = $invoice->customer; // or get customer based on your structure
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'ارسال پیش فاکتور به حسابدار',
                'description' => "کاربر " . auth()->user()->family . '(' . auth()->user()->role->label . ')' . " پیش فاکتور مربوط به سفارش مشتری " . ($customer ? $customer->name : 'نامشخص') . ' به شماره سفارش ' . $invoice->id . ' به حسابداری ارسال کرد',
            ]);

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
            $customer = $invoice->customer; // or get customer based on your structure
            // ثبت فعالیت
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'ارسال فاکتور به انبار',
                'description' => "کاربر " . auth()->user()->family . '(' . auth()->user()->role->label . ')'  . " فاکتور مربوط به سفارش مشتری " . ($customer ? $customer->name : 'نامشخص') . ' به شماره سفارش ' . $invoice->id . ' را به انبار ارسال کرد',
            ]);

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

                // ثبت فعالیت
                Activity::create([
                    'user_id' => auth()->id(),
                    'action' => 'ارسال پیش فاکتور به همکار فروش',
                    'description' => "کاربر " . auth()->user()->family . " (" . auth()->user()->role->label . ") " .
                        " پیش فاکتور سفارش مشتری {$invoice->customer->name} " .
                        "به شماره سفارش {$invoice->id} را به همکار ارسال کرد ",
                ]);

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

                // ثبت فعالیت
                Activity::create([
                    'user_id' => auth()->id(),
                    'action' => 'ارسال فاکتور به انبار',
                    'description' => "کاربر " . auth()->user()->family . " (" . auth()->user()->role->label . ") " .
                        " فاکتور سفارش مشتری {$invoice->customer->name} " .
                        "به شماره سفارش {$invoice->id} را به انبار دار ارسال کرد ",
                ]);

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
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'حذف فایل پیش ‌فاکتور',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') فایل پیش ‌فاکتور مربوط به سفارش ' . $invoiceAction->invoice->customer->name . ' را حذف کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        $invoiceAction->delete();

        alert()->success('فایل پیش فاکتور مورد نظر حذف شد','حذف پیش فاکتور');
        return back();
    }

    public function deleteFactorFile(InvoiceAction $invoiceAction)
    {
        unlink(public_path($invoiceAction->factor_file));
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'حذف فایل ‌فاکتور',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') فایل ‌فاکتور مربوط به سفارش ' . $invoiceAction->invoice->customer->name . ' را حذف کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        $invoiceAction->delete();

        alert()->success('فایل فاکتور مورد نظر حذف شد','حذف فاکتور');
        return back();
    }

    private function storeInvoiceProducts(Invoice $invoice, $request)
    {
        Log::info('Products in request: ', ['products' => $request->products ?: 'No products found']);
        Log::info('other_products in request: ', ['products' => $request->other_products ?: 'No other_products found']);


        if ($request->products) {
            foreach ($request->products as $key => $product_id) {
                // بررسی داده‌های ارسالی
                logger()->info('Product ID:', ['product_id' => $product_id]);
                logger()->info('Color:', ['color' => $request->colors[$key]]);
                logger()->info('Count:', ['count' => $request->counts[$key]]);
                logger()->info('Price:', ['price' => $request->prices[$key]]);
                logger()->info('Total Price:', ['total_price' => $request->total_prices[$key]]);

                // کد کاهش موجودی و ذخیره در جدول
                $product = Product::find($product_id);
                $properties = json_decode($product->properties);
                $product_exist = array_keys(array_column($properties, 'color'), $request->colors[$key]);

                if ($product_exist) {
                    $properties[$product_exist[0]]->counts -= $request->counts[$key];
                    $changed_properties = json_encode($properties);
                    $product->update(['properties' => $changed_properties]);
                }

                $product->update(['total_count' => $product->total_count -= $request->counts[$key]]);

                // ذخیره در invoice_product
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
        } else {
            logger()->warning('No products found in the request.');
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

    public function testEvent($userId)
    {
        Log::info("testEvent called with userId: {$userId}");
        event(new SendMessage($userId, 'Test notification'));
        try {

            Log::info("Event SendMessage triggered successfully.");
        } catch (\Exception $e) {
            Log::error("Error triggering SendMessage event: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return view('test', ['message' => "Event Sent!"]);
    }

    public function getInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        return response()->json($invoice);
    }

}

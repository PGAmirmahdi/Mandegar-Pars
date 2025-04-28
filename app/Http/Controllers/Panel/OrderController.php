<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBuyOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Activity;
use App\Models\Analyse;
use App\Models\Customer;
use App\Models\CustomerOrderStatus;
use App\Models\Inventory;
use App\Models\InventoryReport;
use App\Models\Invoice;
use App\Models\InvoiceAction;
use App\Models\Order;
use App\Models\OrderAction;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\SendMessage;
use Illuminate\Support\Facades\Notification;
use Morilog\Jalali\Jalalian;

class OrderController extends Controller
{

    public function index()
    {
        $this->authorize('customer-order-list');

        $orders = Order::query();


        if ($code = request()->query('code')) {
            $orders->where('code', 'like', '%' . $code . '%');
        }

        if ($status1 = request()->query('status')) {
            $status = $status1 == 'all' ? ['pending', 'invoiced', 'orders'] : [$status1];
            $orders->whereIn('status', $status);
        }

        if ($customer = request()->query('customer_id')) {
            $customers = Customer::pluck('id');
            $customers_id = $customer == 'all' ? $customers : [$customer];
            $orders->whereIn('customer_id', $customers_id);
        }
        $orders->when(request()->query('payment_type') && request()->query('payment_type') !== 'all', function ($query) {
            $query->where('payment_type', request()->query('payment_type'));
        });

        $orders->when(request()->query('created_in') && request()->query('created_in') !== 'all', function ($query) {
            $query->where('created_in', request()->query('created_in'));
        });
        $orders = $orders->latest()->paginate(30);
        $customers = Customer::all(['id', 'name']);


        return view('panel.orders.index', compact(['orders', 'customers']));
    }


    public function create()
    {
        $this->authorize('customer-order-create');

        return view('panel.orders.create');
    }
    public function store(StoreOrderRequest $request)
    {
        \Log::info('▶︎ STORE ORDER start', ['request' => $request->all()]);

        $this->authorize('customer-order-create');
        $customer = Customer::findOrFail($request->buyer_name);
        \Log::info(' Found customer', ['id' => $customer->id]);

        $invoiceData = $this->sortData($request);
        \Log::info(' InvoiceData', $invoiceData);

        // ۱) ذخیرهٔ سفارش
        $order = new Order();
        $order->description    = $request->description;
        $order->type           = $customer->customer_type;
        $order->req_for        = $request->req_for;
        $order->payment_type   = $request->payment_type;
        $order->code           = $this->generateCode();
        $order->user_id        = auth()->id();
        $order->customer_id    = $request->buyer_name;
        $order->shipping_cost  = $request->shipping_cost;
        $order->created_in     = 'automation';
        $order->products       = json_encode($invoiceData);
        $order->save();
        \Log::info(' Order saved', ['order_id' => $order->id, 'created_at' => $order->created_at]);

        // ۲) همگام‌سازی
        $this->syncOrderToAnalyses($order, $invoiceData);

        $this->send_notif_to_sales_manager($order);

        $order->order_status()->updateOrCreate(
            ['status' => 'register'],
            ['orders' => 1, 'status' => 'register']
        );

        alert()->success('سفارش با موفقیت ثبت شد', 'ثبت سفارش');
        return redirect()->route('orders.edit', $order->id);

    }
    protected function syncOrderToAnalyses(Order $order, array $invoiceData)
    {
        $orderCarbon = $order->created_at;
        $jalaliOrder = Jalalian::fromCarbon($orderCarbon);

        foreach ($invoiceData as $i => $item) {
            $pid   = $item['products'];
            $count = $item['counts'];

            $product = \App\Models\Product::find($pid);
            $catId   = $product->category_id;
            $brandId = $product->brand_id;


            // ۲) پیدا کردن آنالیز مناسب
            $analyse = Analyse::where('category_id', $catId)
                ->where('brand_id', $brandId)
                ->get()
                ->filter(function($a) use($jalaliOrder){
                    $start = Jalalian::fromFormat('Y/m/d', $a->date)->toCarbon()->startOfDay();
                    $end   = Jalalian::fromFormat('Y/m/d', $a->to_date)->toCarbon()->endOfDay();
                    return $jalaliOrder->toCarbon()->between($start,$end);
                })
                ->first();

            if (! $analyse) {
                $date    = $jalaliOrder->format('Y/m/01');
                $to_date = $jalaliOrder->format('Y/m/t');
                $analyse = Analyse::create([
                    'date'        => $date,
                    'to_date'     => $to_date,
                    'category_id' => $catId,
                    'brand_id'    => $brandId,
                    'creator_id'  => auth()->id(),
                ]);
            }
            $exists = $analyse->products()->wherePivot('product_id',$pid)->exists();
            if ($exists) {
                $analyse->products()->updateExistingPivot($pid, [
                    'quantity' => DB::raw("quantity + {$count}")
                ]);
            } else {
                $analyse->products()->attach($pid, [
                    'quantity'      => $count,
                    'storage_count' => 0,
                    'sold_count'    => 0,
                ]);
            }
        }
    }

    public function show(Order $order)
    {
        return view('panel.orders.printable', compact(['order']));
    }


    public function edit(Order $order)
    {
        // edit own invoice OR is admin
        $this->authorize('edit-order-customer', $order);
        if (auth()->user()->isAccountant()) {
            return back();
        }
        return view('panel.orders.edit', compact('order'));
    }


    public function update(Request $request, Order $order)
    {
        // edit own invoice OR is admin
        $this->authorize('edit-order-customer', $order);


        $invoiceData = $this->sortData($request);
        $order->description = $request->description;
        $order->req_for = $request->req_for;
        $order->user_id = auth()->id();
        $order->payment_type = $request->payment_type;
        $order->shipping_cost = $request->shipping_cost;
        $order->customer_id = $request->buyer_name;
        $order->products = json_encode($invoiceData);
        $order->save();
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ویرایش سفارش',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')  سفارش مشتری ' . ($request->buyer_name) . ' را ویرایش کرد.',
        ]);
        alert()->success('سفارش مورد نظر با موفقیت ویرایش شد', 'ویرایش سفارش');
        return redirect()->route('orders.edit', $order->id);
    }


    public function destroy(Order $order)
    {
        $this->authorize('customer-order-delete');

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف سفارش',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')  سفارش مشتری ' . ($order->customer->name) . ' را حذف کرد.',
        ]);

        $order->delete();
        return back();
    }

    private function sortData($request)
    {
        $products = [];
        if (isset($request->products) && is_array($request->products)) {
            foreach ($request->products as $index => $product) {
                $products[] = [
                    'products' => $product,
                    'colors' => $request->colors[$index],
                    'counts' => $request->counts[$index],
                    'units' => $request->units[$index],
                    'prices' => $request->prices[$index],
                    'total_prices' => $request->total_prices[$index],
                ];
            }
        }

        return $products;
    }


    public function orderAction(Order $order)
    {

        if (!Gate::any(['accountant', 'PartnerCity']) && $order->action == null) {
            return back();
        }

        if (!Gate::any(['sales-manager', 'accountant', 'PartnerCity', 'Organ', 'buying_engineering', 'ceo', 'admin', 'warehouse-keeper'])) {
            return back();
        }

        return view('panel.orders.action', compact('order'));
    }

    public function actionStore(Order $invoice, Request $request)
    {
        $status = $request->status;

        if ($request->has('send_to_accountant')) {
            if (!$request->has('confirm')) {
                alert()->error('لطفا تیک تایید پیش فاکتور را بزنید', 'عدم تایید');
                return back();
            }

            $invoice->action()->updateOrCreate(
                ['order_id' => $invoice->id],
                [
                    'acceptor_id' => auth()->id(),
                    'confirm' => 1
                ]
            );

            $title = 'ثبت و ارسال به حسابدار';
            $message = 'تاییدیه شما به حسابداری ارسال شد';
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'ارسال به حسابدار',
                'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') سفارش مشتری ' . ($invoice->customer->name) . ' را به حسابدار ارسال کرد.',
            ]);

            // ارسال نوتیف به حسابداران
            $permissionsId = Permission::where('name', 'accountant')->pluck('id');
            $roles_id = Role::whereHas('permissions', function ($q) use ($permissionsId) {
                $q->whereIn('permission_id', $permissionsId);
            })->pluck('id');
            $title_message = 'تایید پیش فاکتور';
            $url = route('order.action', $invoice->id);
            $notif_message = "پیش فاکتور سفارش {$invoice->customer->name} مورد تایید قرار گرفت";
            $accountants = User::whereIn('role_id', $roles_id)->get();
            Notification::send($accountants, new SendMessage($title_message, $notif_message, $url));

            $invoice->order_status()->updateOrCreate(
                ['status' => 'awaiting_confirm_by_sales_manager'],
                ['orders' => 4, 'status' => 'awaiting_confirm_by_sales_manager']
            );
        } elseif ($request->has('send_to_warehouse')) {
            $request->validate(['factor_file' => 'required|mimes:pdf|max:5000']);

            $file = upload_file_factor($request->factor_file, 'Action/Factors');

            $invoice->action()->updateOrCreate(
                ['order_id' => $invoice->id],
                [
                    'factor_file' => $file,
                    'sent_to_warehouse' => 1
                ]
            );
            $invoice->order_status()->updateOrCreate(
                ['status' => 'send_invoice'],
                ['orders' => 8, 'status' => 'send_invoice']
            );
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'ارسال به انبار',
                'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') سفارش مشتری ' . ($invoice->customer->name) . ' را به انبار ارسال کرد.',
            ]);
            $title = 'ثبت و ارسال به انبار';
            $message = 'فاکتور مورد نظر با موفقیت به انبار ارسال شد';

            $invoice->update(['status' => 'invoiced']);

            // ارسال نوتیف به انباردار و مدیر فروش
            $permissionsId = Permission::whereIn('name', ['warehouse-keeper', 'sales-manager'])->pluck('id');
            $roles_id = Role::whereHas('permissions', function ($q) use ($permissionsId) {
                $q->whereIn('permission_id', $permissionsId);
            })->pluck('id');
            $title_message = 'دریافت پیش فاکتور';
            $url = route('invoices.index');
            $notif_message = "فاکتور {$invoice->customer->name} دریافت شد";
            $accountants = User::whereIn('role_id', $roles_id)->get();
            Notification::send($accountants, new SendMessage($title_message, $notif_message, $url));
        } elseif ($request->has('warehouse_confirm')) {
            // مرحله تایید انبار دار
            $invoice->action()->updateOrCreate(
                ['order_id' => $invoice->id],
                [
                    'status' => 'inventory',
                ]
            );
            $invoice->order_status()->updateOrCreate(
                ['status' => 'accept_inventory'],
                ['orders' => 9, 'status' => 'accept_inventory']
            );
            $invoice->update(['status' => 'inventory']);
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'تایید انبار دار',
                'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') سفارش مشتری ' . ($invoice->customer->name) . ' را تایید انبار کرد.',
            ]);
            $title = 'تایید انبار دار';
            $message = 'سفارش مورد نظر با موفقیت توسط انبار تایید شد';
        } else {
            if ($status == 'invoice') {
                $request->validate(['invoice_file' => 'required|mimes:pdf|max:5000']);

                $file = upload_file_factor($request->invoice_file, 'Action/Invoices');
                $invoice->action()->updateOrCreate(
                    ['order_id' => $invoice->id],
                    [
                        'status' => $status,
                        'invoice_file' => $file
                    ]
                );
                Activity::create([
                    'user_id' => auth()->id(),
                    'action' => 'ارسال به همکار فروش',
                    'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') سفارش مشتری ' . ($invoice->customer->name) . ' را به همکار فروش ارسال کرد.',
                ]);
                $title = 'ثبت و ارسال پیش فاکتور';
                $message = 'پیش فاکتور مورد نظر با موفقیت به همکار فروش ارسال شد';

                // ارسال نوتیف
                $roles_id = Role::whereHas('permissions', function ($q) {
                    $q->where('name', 'sales-manager');
                })->pluck('id');
                $sales_manager = User::where('id', '!=', auth()->id())->whereIn('role_id', $roles_id)->get();
                $title_message = 'دریافت پیش فاکتور';
                $url = route('order.action', $invoice->id);
                $notif_message = "پیش فاکتور {$invoice->customer->name} دریافت شد";
                Notification::send($invoice->user, new SendMessage($title_message, $notif_message, $url));
                Notification::send($sales_manager, new SendMessage($title_message, $notif_message, $url));
            } else {
                $request->validate(['factor_file' => 'required|mimes:pdf|max:5000']);

                $file = upload_file_factor($request->factor_file, 'Action/Factors');
                $invoice->action()->updateOrCreate(
                    ['order_id' => $invoice->id],
                    [
                        'status' => $status,
                        'factor_file' => $file,
                        'sent_to_warehouse' => 1
                    ]
                );
                Activity::create([
                    'user_id' => auth()->id(),
                    'action' => 'ارسال به انبار',
                    'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') سفارش مشتری ' . ($invoice->customer->name) . ' را به انبار ارسال کرد.',
                ]);
                $title = 'ثبت و ارسال فاکتور';
                $message = 'فاکتور مورد نظر با موفقیت به انبار ارسال شد';

                // ارسال نوتیف به انباردار و مدیر فروش
                $permissionsId = Permission::whereIn('name', ['warehouse-keeper', 'sales-manager'])->pluck('id');
                $roles_id = Role::whereHas('permissions', function ($q) use ($permissionsId) {
                    $q->whereIn('permission_id', $permissionsId);
                })->pluck('id');
                $title_message = 'دریافت پیش فاکتور';
                $url = route('invoices.index');
                $notif_message = "فاکتور {$invoice->customer->name} دریافت شد";
                $accountants = User::whereIn('role_id', $roles_id)->get();
                Notification::send($accountants, new SendMessage($title_message, $notif_message, $url));
            }

            $status = $status == 'invoice' ? 'pending' : 'invoiced';
            $invoice->update(['status' => $status]);
        }

        $invoice->order_status()->updateOrCreate(
            ['status' => 'processing_by_accountant_step_1'],
            ['orders' => 2, 'status' => 'processing_by_accountant_step_1']
        );
        $invoice->order_status()->updateOrCreate(
            ['status' => 'pre_invoice'],
            ['orders' => 3, 'status' => 'pre_invoice']
        );
        alert()->success($message, $title);
        return back();
    }


    public function deleteInvoiceFile(OrderAction $orderAction)
    {

        $order = Order::whereId($orderAction->order_id)->first();
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف پیش فاکتور',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')  فایل پیش فاکتور به نام ' . ($orderAction->invoice_file) . ' را حذف کرد.',
        ]);

        $order->order_status()->where('status', 'processing_by_accountant_step_1')->delete();
        $order->order_status()->where('status', 'pre_invoice')->delete();

        $order->update(['status' => 'orders']);
        unlink(public_path($orderAction->invoice_file));
        $orderAction->delete();

        alert()->success('فایل پیش فاکتور مورد نظر حذف شد', 'حذف پیش فاکتور');
        return back();
    }

    public function deleteFactorFile(OrderAction $orderAction)
    {
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف فاکتور',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')  فاکتور به نام ' . ($orderAction->factor_file) . ' راحذف کرد.',
        ]);

        unlink(public_path($orderAction->factor_file));

        $orderAction->update([
            'factor_file' => null,
            'sent_to_warehouse' => 0
        ]);

        if ($orderAction->status == 'factor') {
            $orderAction->delete();
        }

        alert()->success('فایل فاکتور مورد نظر حذف شد', 'حذف فاکتور');
        return back();
    }


    private function send_notif_to_accountants(Order $order)
    {
        $roles_id = Role::whereHas('permissions', function ($q) {
            $q->where('name', 'accountant');
        })->pluck('id');
        $accountants = User::where('id', '!=', auth()->id())->whereIn('role_id', $roles_id)->get();
        $title = "ثبت سفارش";
        $url = route('invoices.edit', $order->id);
        $message = "سفارش '{$order->customer->name}' ثبت شد";

        Notification::send($accountants, new SendMessage($title, $message, $url));
    }

    private function send_notif_to_sales_manager(Order $order)
    {
        $roles_id = Role::whereHas('permissions', function ($q) {
            $q->where('name', 'sales-manager');
        })->pluck('id');
        $managers = User::where('id', '!=', auth()->id())->whereIn('role_id', $roles_id)->get();
        $title = 'ثبت سفارش';
        $url = route('invoices.edit', $order->id);
        $message = "سفارش '{$order->customer->name}' ثبت شد";

        Notification::send($managers, new SendMessage($title, $message, $url));
    }


    public function excel()
    {
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'خروجی اکسل از سفارشات مشتری',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') از سفارشات مشتری ' . ' خروجی اکسل گرفت',
        ]);
        return Excel::download(new \App\Exports\OrderExport, 'orders.xlsx');
    }


    public function getCustomerOrderStatus($id)
    {
        $order = Order::with('order_status')->whereId($id)->first();

        if ($order->type == 'setad') {
            $statuses = CustomerOrderStatus::ORDER;
        } else {
            $statuses = CustomerOrderStatus::ORDER_OTHER;
        }

        $statusData = [];


        foreach ($statuses as $key => $status) {
            $date = optional($order->order_status()->where('status', $status)->first())->created_at ?
                verta($order->order_status()->where('status', $status)->first()->created_at)->format('H:i %Y/%m/%d') : '';

            $statusData[] = [
                'status' => $status,
                'status_label' => CustomerOrderStatus::STATUS[$status],
                'date' => $date,
                'pending' => false,
            ];
        }

        $lastDateIndex = -1;

        foreach ($statusData as $index => $statusItem) {
            if (!empty($statusItem['date'])) {
                $lastDateIndex = $index;
            }
        }
        if ($lastDateIndex !== -1 && $lastDateIndex + 1 < count($statusData)) {
            $statusData[$lastDateIndex + 1]['pending'] = true;
        }
        return response()->json($statusData);
    }


    public function generateCode()
    {
        $code = '666' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

        while (Order::where('code', $code)->lockForUpdate()->exists()) {
            $code = '666' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        }

        return $code;
    }


    public function getCustomerOrder($code)
    {
        $order = Order::where('code', $code)->first();

        $mergedProducts = [];

        if ($order) {
            $decodedProducts = json_decode($order->products, true);

            $total_price = $this->calculateTotal($decodedProducts);

            if (!empty($decodedProducts)) {
                $productIds = collect(array_column($decodedProducts, 'products')); // استخراج 'products' از JSON
                $productsFromDB = Product::whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($decodedProducts as $product) {
                    $productModel = $productsFromDB->get($product['products']); // دسترسی به 'products' به صورت آرایه
                    $mergedProducts[] = [
                        'title' => $productModel ? $productModel->title : 'Unknown Product',
                        'color' => Product::COLORS[$product['colors']] ?? 'Unknown Color', // دسترسی به 'colors' به صورت آرایه
                        'count' => $product['counts'], // دسترسی به 'counts' به صورت آرایه
                        'unit' => $product['units'], // دسترسی به 'units' به صورت آرایه
                        'price' => 0, // می‌توانید از قیمت خاص استفاده کنید یا در صورت نیاز آن را از دیتابیس بگیرید
                    ];
                }
            }

            $data = [
                'customer' => $order->customer,
                'payment_type' => $order->payment_type,
                'created_in' => $order->created_in,
                'order' => $mergedProducts,
                'total_price' => $total_price,
                'description' => $order->description,
                'shipping_cost' => $order->shipping_cost
            ];

            $response = [
                'status' => 'success',
                'data' => $data
            ];
            return response()->json($response, 200);
        }

        $response = [
            'status' => 'failed',
            'data' => null
        ];
        return response()->json($response, 200);
    }


    public function calculateTotal($products)
    {
        $sum_total_price = 0;
        if (!empty($products)) {
            foreach ($products as $product) {
                if (isset($product['total_prices'])) {
                    $sum_total_price += $product['total_prices'];
                }

                if (isset($product['other_total_prices'])) {
                    $sum_total_price += $product['other_total_prices'];
                }
            }
        }
        return $sum_total_price;
    }


    public function getUserType($user)
    {
        $type = '';
        if ($user->role->name == 'online_sales') {
            $type = 'online_sale';
        }
        if ($user->role->name == 'setad_sale') {
            $type = 'setad';
        }
        return $type;
    }

    public function docs($orderId)
    {
        // دریافت سفارش یا برگرداندن 404 در صورت عدم وجود
        $order = \App\Models\Order::findOrFail($orderId);

        // دریافت عملیات سفارش (OrderAction)
        $orderAction = \App\Models\OrderAction::where('order_id', $orderId)->first();

        // اگر عملیات سفارش وجود ندارد، پاسخ خالی برگردانید
        if (!$orderAction) {
            return response()->json([
                'files'    => [],
                'exit_url' => '#'
            ]);
        }

        // دریافت فاکتور مرتبط با سفارش
        $invoice = $order->invoice ?? \App\Models\Invoice::where('order_id', $orderId)->first();

        // دریافت گزارش انبار مرتبط (در صورت وجود)
        $inventoryReport = $invoice ? \App\Models\InventoryReport::where('invoice_id', $invoice->id)->first() : null;

        // دریافت نام فایل‌های پیش فاکتور و فاکتور از عملیات سفارش
        $invoice_file = $orderAction->invoice_file;
        $factor_file  = $orderAction->factor_file;

        // در صورتی که هیچ یک از فایل‌ها موجود نباشند
        if (!$invoice_file && !$factor_file) {
            return response()->json([
                'files'    => [],
                'exit_url' => '#'
            ]);
        }

        // آرایه‌ای جهت نگهداری اطلاعات فایل‌ها
        $files = [];

        if ($invoice_file) {
            $files[] = [
                'title'         => 'فایل پیش فاکتور',
                'file_name'     => $invoice_file,
                'download_url'  => asset($invoice_file)
            ];
        }

        if ($factor_file) {
            $files[] = [
                'title'         => 'فایل فاکتور',
                'file_name'     => $factor_file,
                'download_url'  => asset($factor_file)
            ];
        }

        // تعیین لینک خروج (در صورت وجود گزارش انبار)
        $exit_url = $inventoryReport ? route('inventory-reports.show', $inventoryReport->id) : '#';

        return response()->json([
            'files'    => $files,
            'exit_url' => $exit_url,
        ]);
    }
}

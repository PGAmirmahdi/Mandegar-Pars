<?php

namespace App\Http\Controllers\Panel;

use App\Exports\SalePriceRequestExport;
use App\Exports\SalePriceRequestSetadExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalePriceRequest;
use App\Models\Activity;
use App\Models\Analyse;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\SalePriceRequest;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;

class SalePriceRequestController extends Controller
{
    public function index()
    {
        $type = request()->get('type') ?? 'setad_sale';
        $saleprice_requests = SalePriceRequest::query();

        if (Gate::allows('ceo') || Gate::allows('admin')) {
            $saleprice_requests = $saleprice_requests->where('type', $type)->latest()->paginate(30);
        } else {
            $saleprice_requests = $saleprice_requests->where('type', $type)->where('user_id', auth()->id())->latest()->paginate(30);
        }

        return view('panel.sale-price-requests.index', compact('saleprice_requests'));
    }

    public function store(StoreSalePriceRequest $request)
    {
        $this->authorize('sale-price-requests-create');

        $items = [];

        foreach ($request->products as $key => $productId) {
            $product = Product::with('category', 'productModels')->find($productId);
            if ($product) {
                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->title,
                    'product_model' => $product->productModels->slug,
                    'category_name' => $product->category->slug,
                    'product_price' => $request->product_price[$key],
                    'count' => $request->counts[$key],
                ];
            }
        }
        $totalPrice = collect($items)->sum(function ($item) {
            return $item['product_price'] * $item['count'];
        });
        $data = [
            'user_id' => auth()->id(),
            'customer_id' => $request->customer,
            'code' => $this->generateCode(),
            'payment_type' => $request->payment_type,
            'status' => 'pending',
            'description' => $request->description,
            'products' => json_encode($items),
            'price' => $totalPrice,
            'type' => auth()->user()->role->name,
        ];
        if (auth()->user()->role->name !== 'setad_sale'){
            $data['shipping_cost'] = $request->shipping_cost;
        }
        if (auth()->user()->role->name == 'setad_sale') {
            $data['date'] = $request->date;
            $data['hour'] = $request->hour;
            $data['need_no'] = $request->need_no;
        }
        SalePriceRequest::create($data);

        $notifiables = User::where('id', '!=', auth()->id())
            ->whereHas('role', function ($role) {
                $role->whereHas('permissions', function ($q) {
                    $q->whereIn('name', ['ceo', 'sales-manager', 'admin']);
                });
            })->get();
        $customer = Customer::find($request->customer);
        $notif_title = 'درخواست ' . auth()->user()->role->label;
        $notif_message = "یک درخواست فروش " . auth()->user()->role->label . " توسط همکار فروش " . auth()->user()->family . " برای مشتری {$customer->name} " ." ثبت گردید";
        $url = route('sale_price_requests.index');
        Notification::send($notifiables, new SendMessage($notif_title, $notif_message, $url));
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ثبت درخواست ' . auth()->user()->role->label,
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') یک درخواست ' . auth()->user()->role->label . ' برای مشتری '. $customer->name .' ثبت کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        alert()->success('درخواست فروش با موفقیت ثبت شد', 'ثبت درخواست فروش');
        return redirect(url('/panel/sale_price_requests?type=' . auth()->user()->role->name));
    }

    public function create()
    {
        $this->authorize('sale-price-requests-create');
        // گرفتن همه محصولات از دیتابیس
        $products = Product::all()->where('status', '=', 'approved');
        return view('panel.sale-price-requests.create', compact('products'));
    }

    public function generateCode()
    {
        $code = '777' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

        while (Order::where('code', $code)->lockForUpdate()->exists() || SalePriceRequest::where('code', $code)->lockForUpdate()->exists()) {
            $code = '777' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function show(SalePriceRequest $sale_price_request)
    {
        $type = request()->get('type');
        // تبدیل آیتم‌ها به مجموعه و افزودن قیمت پیشنهادی سیستم
        $items = collect(json_decode($sale_price_request->items))->map(function ($item) {
            // بازیابی قیمت محصول برای فروشنده مشخص
            $price = DB::table('price_list')
                ->where('product_id', $item->product_id) // شناسه محصول
                ->where('seller_id', 4) // آیدی فروشنده (مقدار پیش‌فرض یا داینامیک)
                ->value('price');
            $item->price = $items['price'] ?? 0;
            $item->system_price = $price ?? 0; // مقدار پیش‌فرض 0 در صورت نبودن قیمت
            return $item;
        });

        // ارسال داده به ویو
        return view('panel.sale-price-requests.show', [
            'sale_price_request' => $sale_price_request->fill(['items' => $items]),
        ]);
    }

    public function edit(SalePriceRequest $sale_price_request)
    {
        $this->authorize('sale_price_request_edit');

        // بازیابی محصولات
        $products = \App\Models\Product::with(['category', 'productModels'])->where('status', '=', 'approved')->get();

        $items = collect(json_decode($sale_price_request->items))->map(function ($item) {
            $price = DB::table('price_list')
                ->where('product_id', $item->product_id)
                ->where('seller_id', 4) // مقدار پیش‌فرض یا داینامیک
                ->value('price');
            $item->price = $item->price ?? 0;
            $item->system_price = $price ?? 0; // مقدار پیش‌فرض 0 در صورت نبودن قیمت
            return $item;
        });

        // ارسال داده به ویو
        return view('panel.sale-price-requests.edit', [
            'sale_price_request' => $sale_price_request->fill(['items' => $items]),
            'products' => $products, // ارسال محصولات به ویو
        ]);
    }


    public function update(StoreSalePriceRequest $request, SalePriceRequest $sale_price_request)
    {
//        return $request->prices;
        $this->authorize('sale_price_request_edit');
        $items = [];

        foreach ($request->products as $key => $productId) {
            $product = Product::with('category', 'productModels')->find($productId);
            if ($product) {
                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->title,
                    'product_model' => $product->productModels->slug,
                    'category_name' => $product->category->slug,
                    'product_price' => $request->product_price[$key],
                    'count' => $request->counts[$key],
                ];
            }
        }
        $totalPrice = collect($items)->sum(function ($item) {
            return $item['product_price'] * $item['count'];
        });
        $sale_price_request->update([
            'products' => json_encode($items),
            'status' => 'pending',
            'description' => $request->description,
            'customer_id' => $request->customer,
            'date' => $request->date,
            'hour' => $request->hour,
            'price' => $totalPrice,
            'code' => $this->generateCode(),
            'payment_type' => $request->payment_type,
            'need_no' => $request->need_no,
            'shipping_cost' => $request->shipping_cost,
//          'type' => auth()->user()->role->name
        ]);

        // notification sent to ceo
        $notifiables = User::where('id', '!=', auth()->id())->whereHas('role', function ($role) {
            $role->whereHas('permissions', function ($q) {
                $q->whereIn('name', ['ceo', 'sales-manager', 'admin']);
            });
        })->get();

        $notif_title = 'درخواست ' . auth()->user()->role->label;
        $notif_message = 'ویرایش درخواست ' . auth()->user()->role->label . ' توسط ' . auth()->user()->family . ' انجام شد.';
        $url = route('sale_price_requests.index');
        $customer = Customer::whereId($sale_price_request->customer_id)->first();
        Notification::send($notifiables, new SendMessage($notif_title, $notif_message, $url));
        Notification::send($sale_price_request->user, new SendMessage($notif_title, $notif_message, $url));
        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'درخواست ' . auth()->user()->role->label,
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') ' . ' درخواست فروش ' . $customer->name . ' را ویرایش کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        alert()->success('درخواست فروش با موفقیت ویرایش شد', 'ویرایش درخواست فروش');
        return redirect(url('/panel/sale_price_requests?type=' . $sale_price_request->type));
    }

    public function action(SalePriceRequest $sale_price_request)
    {
        if (!Gate::allows('ceo') && !Gate::allows('admin')) {
            alert()->error('شما به این قسمت دسترسی ندارید', 'عدم دسترسی');
            return back();
        }
        $items = collect(json_decode($sale_price_request->items))->map(function ($item) {
            $price = DB::table('price_list')
                ->where('product_id', $item->product_id)
                ->where('seller_id', 4)
                ->value('price');
            $item->price = $items['price'] ?? 0;
            $item->system_price = $price ?? 0;
            return $item;
        });

        // ارسال داده به ویو
        return view('panel.sale-price-requests.action', [
            'sale_price_request' => $sale_price_request->fill(['items' => $items]),
        ]);
    }

    public function actionStore(Request $request)
    {
        if (!Gate::allows('ceo') && !Gate::allows('admin')) {
            alert()->error('شما به این قسمت دسترسی ندارید', 'عدم دسترسی');
            return back();
        }

        $sale_price_request = SalePriceRequest::findOrFail($request->sale_id);

        $items = [];
        $OrderItems = [];
        foreach (json_decode($sale_price_request->products, true) as $key => $item) {
            $product = Product::with('category', 'productModels')->find($item['product_id']);
            if (! $product) {
                continue;
            }

            $count      = (int) $request->count[$key];
            $finalPrice = (int) str_replace(',', '', $request->final_price[$key] ?? 0);

            $items[] = [
                'product_id'    => $product->id,
                'product_name'  => $product->title,
                'product_model' => $product->productModels->slug,
                'category_name' => $product->category->slug,
                'count'         => $count,
                'final_price'   => $finalPrice,
                'product_price' => (int) str_replace(',', '', $request->product_price[$key] ?? 0),
            ];

            $OrderItems[] = [
                'products'     => $product->id,
                'colors'       => 'black',
                'counts'       => $count,
                'units'        => 'number',
                'prices'       => $finalPrice,
                'total_prices' => $count * $finalPrice,
            ];
        }

        $order = $this->newOrder($request, $sale_price_request, $OrderItems);
        $this->syncSaleToAnalyses($order, $OrderItems);

        $status = $sale_price_request->type == 'setad_sale' ? 'accepted' : 'finished';
        $sale_price_request->update([
            'acceptor_id'   => auth()->id(),
            'products'      => json_encode($items),
            'status'        => $status,
            'price'         => $request->price,
            'description'   => $request->description,
            'shipping_cost' => $request->shipping_cost,
        ]);

        // ثبت فعالیت و ارسال نوتیفیکیشن
        $customer = Customer::find($sale_price_request->customer_id);
        Activity::create([
            'user_id'    => auth()->id(),
            'action'     => 'تایید درخواست ' . auth()->user()->role->label,
            'description'=> 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') درخواست فروش ' . $customer->name . ' را تایید کرد.',
            'created_at' => now(),
        ]);
        $this->notif_to_ceo($sale_price_request);

        alert()->success('درخواست فروش با موفقیت تایید شد', 'تایید درخواست فروش');
        return redirect()->route('sale_price_requests.index', ['type' => $sale_price_request->type]);
    }
    public function newOrder($request,  $sale_price_request, $OrderItems)
    {
        $order = new Order();
        $order->description = $request->description;
        $order->type = 'setad';
        $order->req_for = 'pre-invoice';
        $order->payment_type = $sale_price_request->payment_type;
        $order->code = $sale_price_request->code;
        $order->user_id = $sale_price_request->user->id;
        $order->customer_id = $sale_price_request->customer->id;
        $order->shipping_cost = $sale_price_request->shipping_cost;
        $order->created_in = 'automation';
        $order->products = json_encode($OrderItems);
        $order->save();
        $order->order_status()->updateOrCreate(
            ['status' => 'register'],
            ['orders' => 1, 'status' => 'register']
        );
        return $order;
    }

    protected function syncSaleToAnalyses(Order $order, array $orderItems)
    {
        $jalaliOrder = Jalalian::fromCarbon($order->created_at);

        foreach ($orderItems as $item) {
            $pid   = $item['products'];
            $count = $item['counts'];

            $product = Product::find($pid);
            if (! $product) {
                continue;
            }

            $catId   = $product->category_id;
            $brandId = $product->brand_id;

            // پیدا کردن analyse موجود در بازه‌ی تاریخِ سفارش
            $analyse = Analyse::where('category_id', $catId)
                ->where('brand_id', $brandId)
                ->get()
                ->filter(function($a) use($jalaliOrder) {
                    $start = Jalalian::fromFormat('Y/m/d', $a->date)->toCarbon()->startOfDay();
                    $end   = Jalalian::fromFormat('Y/m/d', $a->to_date)->toCarbon()->endOfDay();
                    return $jalaliOrder->toCarbon()->between($start, $end);
                })
                ->first();

            // اگر پیدا نشد، یک analyse جدید برای ماه جاری بساز
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

            // به‌روز رسانی یا الصاق در جدول pivot analyse_product
            $exists = $analyse->products()->wherePivot('product_id', $pid)->exists();
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
    public function notif_to_ceo($sale_price_request)
    {
        $notifiables = User::where('id','=',$sale_price_request->user_id)->get();

        $notif_title = 'تایید درخواست فروش';
        $notif_message = 'تایید درخواست فروش توسط مدیر انجام گردید';
        $url = route('sale_price_requests.index');
        Notification::send($notifiables, new SendMessage($notif_title, $notif_message, $url));
    }

    public function actionResult(Request $request, SalePriceRequest $sale_price_request)
    {
        $this->authorize('Organ');

        // اعتبارسنجی ورودی‌ها
        $request->validate([
            'row_id' => 'required|exists:sale_price_requests,id',
            'result' => 'required|in:winner,lose',
            'description' => 'nullable|string',
        ]);
        // به‌روزرسانی داده‌ها
        $sale_price_request = SalePriceRequest::findOrFail($request->row_id);
        $sale_price_request->update([
            'status' => $request->result,
            'final_result' => $request->result,
            'final_description' => $request->description,
        ]);
        // ارسال نوتیفیکیشن
        $notifiables = User::where('id', '!=', auth()->id())->whereHas('role', function ($role) {
            $role->whereHas('permissions', function ($q) {
                $q->whereIn('name', ['ceo', 'sales-manager', 'admin']);
            });
        })->get();

        $notif_title = 'ثبت نتیجه درخواست ستاد';
        $notif_message = 'نتیجه درخواست ستاد ثبت گردید';
        $url = route('sale_price_requests.index');

        Notification::send($notifiables, new SendMessage($notif_title, $notif_message, $url));
        $customer = Customer::whereId($sale_price_request->customer_id)->first();
        // ثبت فعالیت
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'نتیجه درخواست ستاد',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') نتیجه درخواست ستاد '. $customer->name . ' را بارگذاری کرد.',
            'created_at' => now(),
        ]);

        alert()->success('نتیجه نهایی با موفقیت ثبت شد', 'ثبت نتیجه');
        return redirect(url('/panel/sale_price_requests?type=' . $sale_price_request->type));
    }

    public function destroy(SalePriceRequest $sale_price_request)
    {
        $this->authorize('sale-price-requests-delete');
        $customer = Customer::whereId($sale_price_request->customer_id)->first();
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف درخواست فروش',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') درخواست فروش '. $customer->name . ' را حذف کرد.',
            'created_at' => now(),
        ]);
        $sale_price_request->delete();
        alert()->success('درخواست فروش با موفقیت حذف شد', 'حذف درخواست فروش');

        return back();
    }

    public function export()
    {
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'خروجی اکسل از درخواست های فروش',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ')از درخواست های فروش خروجی اکسل گرفت.',
            'created_at' => now(),
        ]);
        $auth = auth()->user()->role->name;
        if (in_array($auth, ['setad_sale'])) {
            return Excel::download(new SalePriceRequestSetadExport(), 'setad_sale_price_requests.xlsx');
        } else {
            return Excel::download(new SalePriceRequestExport($auth), 'sale_price_requests.xlsx');
        }
    }

}

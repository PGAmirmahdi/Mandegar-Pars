<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Order;
use App\Models\Product;
use App\Models\SalePriceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\SendMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class SalePriceRequestController extends Controller
{
    public function index()
    {
        $saleprice_requests = SalePriceRequest::query();

        if (Gate::allows('ceo') || Gate::allows('admin')) {
            $saleprice_requests = $saleprice_requests->latest()->paginate(30);
        } else {
            $saleprice_requests = $saleprice_requests->where('user_id', auth()->id())->paginate(30);
        }

        return view('panel.sale-price-requests.index', compact('saleprice_requests'));
    }

    public function create()
    {
        $this->authorize('sale-price-requests-create');
        // گرفتن همه محصولات از دیتابیس
        $products = Product::all()->where('status', '=', 'approved');
        return view('panel.sale-price-requests.create', compact('products'));
    }

    public function store(Request $request)
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
                    'price' => $request->price[$key],
                    'count' => $request->counts[$key],
                ];
            }
        }
        SalePriceRequest::create([
            'user_id' => auth()->id(),
            'customer_id' => $request->customer,
            'date' => $request->date,
            'hour' => $request->hour,
            'code' => $this->generateCode(),
            'payment_type' => $request->payment_type,
            'status' => 'pending',
            'description' => $request->description,
            'need_no' => $request->need_no,
            'products' => json_encode($items),
            'type' => auth()->user()->role->name
        ]);
        $notifiables = User::where('id', '!=', auth()->id())
            ->whereHas('role', function ($role) {
                $role->whereHas('permissions', function ($q) {
                    $q->whereIn('name', ['ceo', 'sales-manager', 'admin']);
                });
            })->get();

        $notif_title = 'درخواست ' . auth()->user()->role->label;
        $notif_message = "یک درخواست " . auth()->user()->role->label . " توسط همکار فروش " . auth()->user()->family . " ثبت گردید";
        $url = route('sale_price_requests.index');
        Notification::send($notifiables, new SendMessage($notif_title, $notif_message, $url));

        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ثبت درخواست ' . auth()->user()->role->label,
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') یک درخواست ' . auth()->user()->role->label . ' ثبت کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        alert()->success('درخواست فروش با موفقیت ثبت شد', 'ثبت درخواست فروش');
        return redirect()->route('sale_price_requests.index');
    }

    public function show(SalePriceRequest $sale_price_request)
    {
        $this->authorize('sale-price-requests-list');

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
        // بررسی مجوزهای کاربر
        $this->authorize('ceo');
        $this->authorize('admin');

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
        return view('panel.sale-price-requests.edit', [
            'sale_price_request' => $sale_price_request->fill(['items' => $items]),
        ]);
    }

    public function update(Request $request, SalePriceRequest $sale_price_request)
    {
//        return $request->prices;
        $this->authorize('ceo');
        $this->authorize('admin');
        $items = [];

        foreach (json_decode($sale_price_request->products, true) as $key => $item) {
            $product = Product::with('category', 'productModels')->find($item['product_id']);
            if ($product) {
                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->title,
                    'product_model' => $product->productModels->slug,
                    'category_name' => $product->category->slug,
                    'count' => $request->counts,
                    'price' => $request->price[$key],
                ];

            }
        }

        $sale_price_request->update([
            'acceptor_id' => auth()->id(),
            'products' => json_encode($items),
            'status' => 'accepted',
            'description' => $request->description,
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
        Notification::send($notifiables, new SendMessage($notif_title, $notif_message, $url));
        Notification::send($sale_price_request->user->id, new SendMessage($notif_title, $notif_message, $url));
        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'درخواست ' . auth()->user()->role->label,
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ' درخواست  را ویرایش کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ثبت فعالیت در پایگاه داده
        alert()->success('درخواست فروش با موفقیت ویرایش شد', 'ویرایش درخواست فروش');
        return redirect()->route('sale_price_requests.index');
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
        $sale_price_request = SalePriceRequest::findOrfail($request->sale_id);
        $items = [];
        $OrderItems = [
            'products' => [],
            'other_products' => [],
        ];
        foreach (json_decode($sale_price_request->products, true) as $key => $item) {
            $product = Product::with('category', 'productModels')->find($item['product_id']);
//            dd($product, $request->all(), $request->count[$key], $request->price[$key], str_replace(',', '', $request->final_price[$key] ?? 0));
            if ($product) {

                $finalPrice = str_replace(',', '', $request->final_price[$key] ?? 0);
//                dd($finalPrice);
                $OrderItems['other_products'][] = [
                    'products' => (integer)$product->id,
                    'colors' => 'black',
                    'counts' => (integer)$request->count[$key],
                    'units' => 'number',
                    'prices' => $finalPrice,
                    'total_prices' => (integer)$request->count[$key] * $finalPrice,
                ];
            }
        }
        $this->newOrder($request, $sale_price_request, $OrderItems);
        // تعیین وضعیت بر اساس نوع درخواست
        $status = $sale_price_request->type == 'setad_sale' ? 'accepted' : 'finished';
        $sale_price_request->update([
            'acceptor_id' => auth()->id(),
            'products' => json_encode($OrderItems),
            'status' => $status,
            'description' => $request->description,
        ]);
        // notification sent to ceo


        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'تایید درخواست ' . auth()->user()->role->label,
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') درخواست فروش را تایید کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        $this->notif_to_ceo();
        alert()->success('درخواست فروش با موفقیت تایید شد', 'تایید درخواست فروش');
        return redirect()->route('sale_price_requests.index');

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

        // ثبت فعالیت
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'نتیجه درخواست ستاد',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') نتیجه درخواست ستاد را بارگذاری کرد.',
            'created_at' => now(),
        ]);

        alert()->success('نتیجه نهایی با موفقیت ثبت شد', 'ثبت نتیجه');
        return redirect()->route('sale_price_requests.index');
    }


    public function destroy(SalePriceRequest $setad_price_request)
    {
        $this->authorize('sale-price-requests-delete');

        $setad_price_request->delete();
        alert()->success('درخواست فروش با موفقیت حذف شد', 'حذف درخواست فروش');

        return back();
    }

    public function generateCode()
    {
        $code = '777' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

        while (Order::where('code', $code)->lockForUpdate()->exists() || SalePriceRequest::where('code', $code)->lockForUpdate()->exists()) {
            $code = '777' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function newOrder($request, $setad_price_request, $OrderItems)
    {
        $order = new Order();
        $order->description = $request->description;
        $order->type = 'setad';
        $order->req_for = 'pre-invoice';
        $order->payment_type = $setad_price_request->payment_type;
        $order->code = $setad_price_request->code;
        $order->user_id = $setad_price_request->user->id;
        $order->customer_id = $setad_price_request->customer->id;
        $order->created_in = 'automation';
        $order->products = json_encode($OrderItems);
        $order->save();
        $order->order_status()->updateOrCreate(
            ['status' => 'register'],
            ['orders' => 1, 'status' => 'register']
        );
    }

    public function notif_to_ceo()
    {
        $notifiables = User::where('id', '!=', auth()->id())->whereHas('role', function ($role) {
            $role->whereHas('permissions', function ($q) {
                $q->whereIn('name', ['ceo', 'sales-manager', 'admin']);
            });
        })->get();

        $notif_title = 'تایید درخواست فروش';
        $notif_message = 'تایید درخواست فروش توسط مدیر انجام گردید';
        $url = route('sale_price_requests.index');
        Notification::send($notifiables, new SendMessage($notif_title, $notif_message, $url));
    }
}

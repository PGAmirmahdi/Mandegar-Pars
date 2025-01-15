<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Order;
use App\Models\Product;
use App\Models\SetadPriceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\SendMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SetadPriceRequestController extends Controller
{
    public function index()
    {
        $this->authorize('setad-price-requests-list');
        $setadprice_requests = SetadPriceRequest::latest()->paginate(30);

        return view('panel.setad-price-requests.index', compact('setadprice_requests'));
    }

    public function create()
    {
        $this->authorize('setad-price-requests-create');
        // گرفتن همه محصولات از دیتابیس
        $products = Product::all()->where('status', '=', 'approved');
        return view('panel.setad-price-requests.create', compact('products'));
    }

    public function store(Request $request)
    {
        $this->authorize('setad-price-requests-create');

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
        SetadPriceRequest::create([
            'user_id' => auth()->id(),
            'customer_id' => $request->customer,
            'date' => $request->date,
            'hour' => $request->hour,
            'code' => $this->generateCode(),
            'payment_type' => $request->payment_type,
            'status' => 'pending',
            'description' => $request->description,
            'need_no' => $request->need_no,
            'products' => json_encode($items)
        ]);

        $notifiables = User::where('id', '!=', auth()->id())->whereHas('role', function ($role) {
            $role->whereHas('permissions', function ($q) {
                $q->whereIn('name', ['ceo', 'sales-manager', 'admin']);
            });
        })->get();
        $notif_title = 'درخواست ستاد';
        $notif_message = 'یک درخواست ستاد توسط همکار فروش سامانه ستاد ثبت گردید';
        $url = route('setad_price_requests.index');
//        Notification::send($notifiables, new SendMessage($notif_title,$notif_message, $url));

        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ثبت درخواست ستاد',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') یک درخواست ستاد ثبت کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        alert()->success('درخواست ستاد با موفقیت ثبت شد', 'ثبت درخواست ستاد');
        return redirect()->route('setad_price_requests.index');
    }

    public function show(SetadPriceRequest $setad_price_request)
    {
        $this->authorize('setad-price-requests-list');

        $items = json_decode($setad_price_request->items, true);

        foreach ($items as $key => $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $items[$key]['market_price'] = $item['new_price'] ?? null;
            }
        }

        return view('panel.price-requests.show', compact('setad_price_request', 'items'));
    }


    public function edit(SetadPriceRequest $setad_price_request)
    {
        // بررسی مجوزهای کاربر
        $this->authorize('ceo');
        $this->authorize('admin');

        // تبدیل آیتم‌ها به مجموعه و افزودن قیمت پیشنهادی سیستم
        $items = collect(json_decode($setad_price_request->items))->map(function ($item) {
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
        return view('panel.setad-price-requests.edit', [
            'setad_price_request' => $setad_price_request->fill(['items' => $items]),
        ]);
    }

    public function update(Request $request, SetadPriceRequest $setad_price_request)
    {
//        return $request->prices;
        $this->authorize('ceo');
        $this->authorize('admin');
        $items = [];

        foreach (json_decode($setad_price_request->products, true) as $key => $item) {
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

        $setad_price_request->update([
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

        $notif_title = 'ویرایش درخواست ستاد';
        $notif_message = 'ویرایش درخواست ستاد توسط کارشناس فروش انجام گردید';
        $url = route('setad_price_requests.index');
//        Notification::send($notifiables, new SendMessage($notif_title,$notif_message, $url));
//        Notification::send($setadpriceRequest->user, new SendMessage($notif_title,$notif_message, $url));
        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ویرایش درخواست ستاد',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ')ویرایش درخواست ستاد را ایجاد کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ثبت فعالیت در پایگاه داده
        alert()->success('درخواست ستاد با موفقیت تایید شد', 'تایید درخواست ستاد');
        return redirect()->route('setad_price_requests.index');
    }

    public function action(SetadPriceRequest $setad_price_request)
    {

        // بررسی مجوزهای کاربر
        $this->authorize('ceo');
        $this->authorize('admin');

        $items = collect(json_decode($setad_price_request->items))->map(function ($item) {
            $price = DB::table('price_list')
                ->where('product_id', $item->product_id)
                ->where('seller_id', 4)
                ->value('price');
            $item->price = $items['price'] ?? 0;
            $item->system_price = $price ?? 0;
            return $item;
        });

        // ارسال داده به ویو
        return view('panel.setad-price-requests.action', [
            'setad_price_request' => $setad_price_request->fill(['items' => $items]),
        ]);
    }

    public function actionStore(Request $request)
    {

        $setad_price_request = SetadPriceRequest::findOrfail($request->setad_id);

        $this->authorize('ceo');
        $this->authorize('admin');
        $items = [];
        $OrderItems = [];
        foreach (json_decode($setad_price_request->products, true) as $key => $item) {
            $product = Product::with('category', 'productModels')->find($item['product_id']);
            if ($product) {
                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->title,
                    'product_model' => $product->productModels->slug,
                    'category_name' => $product->category->slug,
                    'count' => $request->count,
                    'final_price' => str_replace(',', '', $request->final_price[$key] ?? 0),
                    'price' => $request->price[$key],
                ];
                $OrderItems[] = [
                    'products' => (integer)$product->id,
                    'colors' => 'black',
                    'counts' => (integer)$request->count,
                    'units' => 'number',
                    'prices' => (integer)$request->price[$key],
                    'total_prices' => (integer)$request->count[$key] * (integer)$request->price[$key],
                ];
            }
        }
        $this->newOrder($request, $setad_price_request, $OrderItems);
        $setad_price_request->update([
            'acceptor_id' => auth()->id(),
            'products' => json_encode($items),
            'status' => 'accepted',
            'description' => $request->description,
        ]);
        // notification sent to ceo


        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'تایید درخواست ستاد',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') درخواست ستاد را تایید کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        $this->notif_to_ceo();
        alert()->success('درخواست ستاد با موفقیت تایید شد', 'تایید درخواست ستاد');
        return redirect()->route('setad_price_requests.index');
    }

    public function actionResult(Request $request, SetadPriceRequest $setad_price_request)
    {
        $this->authorize('Organ');
        $setad_price_request->update([
            'final_result' => $request->final_result,
            'status' => 'finished',
            'final_description' => $request->final_description,
        ]);
        // notification sent to ceo
        $notifiables = User::where('id', '!=', auth()->id())->whereHas('role', function ($role) {
            $role->whereHas('permissions', function ($q) {
                $q->whereIn('name', ['ceo', 'sales-manager', 'admin']);
            });
        })->get();
        $notif_title = 'ثبت نتیجه ستاد';
        $notif_message = 'نتیجه درخواست ستاد توسط کارشناس فروش سامانه ستاد ثبت گردید';
        $url = route('setad_price_requests.index');
//        Notification::send($notifiables, new SendMessage($notif_title,$notif_message, $url));
//        Notification::send($setadpriceRequest->user, new SendMessage($notif_title,$notif_message, $url));
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'نتیجه درخواست ستاد',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ')نتیجه درخواست ستاد را بارگذاری کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
    }

    public function destroy(SetadPriceRequest $setad_price_request)
    {
        $this->authorize('setad-price-requests-delete');

        $setad_price_request->delete();
        alert()->success('درخواست ستاد با حذف شد', 'حذف درخواست ستاد');

        return back();
    }

    public function generateCode()
    {
        $code = '777' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

        while (Order::where('code', $code)->lockForUpdate()->exists()) {
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
        $order->user_id = auth()->id();
        $order->customer_id = $setad_price_request->customer_id;
        $order->created_in = 'automation';
        $order->products = json_encode($OrderItems);
        $order->save();
    }

    public function notif_to_ceo()
    {
        $notifiables = User::where('id', '!=', auth()->id())->whereHas('role', function ($role) {
            $role->whereHas('permissions', function ($q) {
                $q->whereIn('name', ['ceo', 'sales-manager', 'admin']);
            });
        })->get();

        $notif_title = 'تایید درخواست ستاد';
        $notif_message = 'تایید درخواست ستاد توسط مدیر انجام گردید';
        $url = route('setad_price_requests.index');
        //send notificaion
    }
}

<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Product;
use App\Models\SetadPriceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\SendMessage;
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
        $products = Product::all()->where('status','=','approved');
        return view('panel.setad-price-requests.create',compact('products'));
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
                    'count' => $request->counts[$key],
                ];
            }
        }
        SetadPriceRequest::create([
            'user_id' => auth()->id(),
            'customer_id' => $request->customer_id ,
            'date' => $request->date,
            'hour' => $request->hour,
            'code' => 'STD-' . random_int(1000000, 9999999),
            'payment_type' => $request->payment_type,
            'status' => 'pending',
            'description' => $request->description ,
            'products' => json_encode($items)
        ]);

        $notifiables = User::where('id','!=',auth()->id())->whereHas('role' , function ($role) {
            $role->whereHas('permissions', function ($q) {
                $q->whereIn('name', ['ceo','sales-manager']);
            });
        })->get();
        $notif_title = 'درخواست ستاد';
        $notif_message = 'یک درخواست ستاد توسط همکار فروش سامانه ستاد ثبت گردید';
        $url = route('price-requests.index');
        Notification::send($notifiables, new SendMessage($notif_title,$notif_message, $url));

        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ثبت درخواست ستاد',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') یک درخواست ستاد ثبت کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        alert()->success('درخواست ستاد با موفقیت ثبت شد','ثبت درخواست ستاد');
        return redirect()->route('setad-price-requests.index');
    }
    public function show(SetadPriceRequest $setadpriceRequest)
    {
        $this->authorize('setad-price-requests-list');

        $items = json_decode($setadpriceRequest->items, true);

        foreach ($items as $key => $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $items[$key]['market_price'] = $item['new_price'] ?? null;
            }
        }

        return view('panel.price-requests.show', compact('setadpriceRequest', 'items'));
    }


    public function edit(SetadPriceRequest $setadpriceRequest)
    {
        $this->authorize('ceo');

        return view('panel.setad-price-requests.edit', compact('setadpriceRequest'));
    }

    public function update(Request $request, SetadPriceRequest $setadpriceRequest)
    {
        $this->authorize('ceo');

        $items = [];
        foreach (json_decode($setadpriceRequest->items, true) as $key => $item) {
            $product = Product::with('category', 'productModels')->find($item['product_id']);
            if ($product) {
                $items[] = [
                    'price' => str_replace(',', '', $request->prices[$key] ?? 0),
                ];

            }
        }

        $setadpriceRequest->update([
            'acceptor_id' => auth()->id(),
            'items' => json_encode($items),
            'status' => 'accepted',
            'description'=> $request->description,
        ]);

        // notification sent to ceo
        $notifiables = User::whereHas('role', function ($role) {
            $role->whereHas('permissions', function ($q) {
                $q->where('name', 'ceo');
            });
        })->get();

        $notif_title = 'ثبت ستاد';
        $notif_message = 'درخواست ستاد توسط مدیر ثبت گردید';
        $url = route('setad-price-requests.index');
        Notification::send($notifiables, new SendMessage($notif_title,$notif_message, $url));
        Notification::send($setadpriceRequest->user, new SendMessage($notif_title,$notif_message, $url));
        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'تایید درخواست ستاد',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') درخواست ستاد را تایید کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ثبت فعالیت در پایگاه داده
        alert()->success('درخواست ستاد با موفقیت تایید شد', 'تایید درخواست ستاد');
        return redirect()->route('price-requests.index');
    }


    public function destroy(SetadPriceRequest $setadpriceRequest)
    {
        $this->authorize('price-requests-delete');

        $setadpriceRequest->delete();
        return back();
    }
}

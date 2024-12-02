<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\PriceRequest;
use App\Models\Product;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class PriceRequestController extends Controller
{
    public function index()
    {
        $this->authorize('price-requests-list');

        $price_requests = PriceRequest::latest()->paginate(30);

        return view('panel.price-requests.index', compact('price_requests'));
    }

    public function create()
    {
        $this->authorize('price-requests-create');
        // گرفتن همه محصولات از دیتابیس
        $products = Product::all();
        return view('panel.price-requests.create',compact('products'));
    }

    public function store(Request $request)
    {
        $this->authorize('price-requests-create');

        $items = [];

        // Check if products and new_prices are provided
        if ($request->has('products') && $request->has('new_prices') && count($request->products) > 0) {
            foreach ($request->products as $key => $productId) {
                // Get product model by ID
                $product = Product::find($productId);
                if (!$product) {
                    continue;  // Skip if the product doesn't exist
                }

                // Get new price or fallback to old price if new price is not provided
                $new_price = isset($request->new_prices[$key]) && $request->new_prices[$key] != ''
                    ? str_replace(',', '', $request->new_prices[$key])  // if new price is provided
                    : $product->market_price;  // otherwise, use the current price

                $old_price = $product->market_price; // Get the current price of the product

                // Add to items array
                $items[] = [
                    'product' => $product->title,  // Save the title of the product
                    'old_price' => $old_price,
                    'new_price' => $new_price,
                    'description' => isset($request->description[$key]) ? $request->description[$key] : '',
                ];

                // Update product price if the new price is different from the old one
                if ($new_price != $old_price) {
                    $product->market_price = $new_price;  // Set new price
                    $product->save();  // Save the changes
                }
            }

            // Save PriceRequest after processing items
            PriceRequest::create([
                'user_id' => auth()->id(),
                'max_send_time' => 1,  // Default max send time, adjust as needed
                'status' => 'sent',
                'items' => json_encode($items),
            ]);

            alert()->success('درخواست قیمت با موفقیت ثبت شد و قیمت‌ها به روز شدند', 'ثبت درخواست قیمت');
            return redirect()->route('price-requests.index');
        } else {
            // Handle case where products or prices are not provided
            alert()->error('هیچ کالایی برای ثبت درخواست قیمت انتخاب نشده است.', 'خطا');
            return back();
        }
    }


    public function show(PriceRequest $priceRequest)
    {
        $this->authorize('price-requests-list');

        // برای هر محصول، اطلاعات شامل قیمت قبلی و قیمت جدید را به دست می‌آوریم
        $items = json_decode($priceRequest->items, true);

        foreach ($items as $key => $item) {
            $product = Product::find($item['product']); // اطلاعات محصول
            if ($product) {
                // اگر قیمت جدید درخواستی وجود نداشته باشد، از قیمت قبلی استفاده می‌شود
                $items[$key]['market_price'] = isset($item['new_price']) ? $item['new_price'] : $product->price;
            }
        }

        return view('panel.price-requests.show', compact('priceRequest', 'items'));
    }

    public function edit(PriceRequest $priceRequest)
    {
        $this->authorize('ceo');

        return view('panel.price-requests.edit', compact('priceRequest'));
    }

    public function update(Request $request, PriceRequest $priceRequest)
    {
        $this->authorize('ceo');

        $items = [];
        foreach (json_decode($priceRequest->items, true) as $key => $item) {
            $items[] = [
                'product' => $item['product'],
                'count' => $item['count'],
                'description' => $item['description'],
                'price' => str_replace(',', '', $request->prices[$key]),
                'vat_included' => isset($request->vat_included[$key]) ? true : false,
            ];
        }

        $priceRequest->update([
            'items' => json_encode($items),
            'status' => 'sent',
        ]);

        // notification sent to ceo
        $notifiables = User::whereHas('role', function ($role) {
            $role->whereHas('permissions', function ($q) {
                $q->where('name', 'sales-manager');
            });
        })->get();

        $notif_message = 'قیمت کالاهای درخواستی توسط مدیر ثبت گردید';
        $url = route('price-requests.index');
        Notification::send($notifiables, new SendMessage($notif_message, $url));
        Notification::send($priceRequest->user, new SendMessage($notif_message, $url));
        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'به‌روزرسانی درخواست قیمت',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') قیمت‌های درخواستی را به‌روزرسانی کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ثبت فعالیت در پایگاه داده
        alert()->success('قیمت ها با موفقیت ثبت شدند', 'ثبت قیمت');
        return redirect()->route('price-requests.index');
    }


    public function destroy(PriceRequest $priceRequest)
    {
        $this->authorize('price-requests-delete');

        $priceRequest->delete();
        return back();
    }
}

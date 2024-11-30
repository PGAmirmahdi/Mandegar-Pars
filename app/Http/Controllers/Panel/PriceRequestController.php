<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\PriceRequest;
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

        return view('panel.price-requests.create');
    }

    public function store(Request $request)
    {
        $this->authorize('price-requests-create');

        $items = [];

        foreach ($request->products as $key => $product){
            $items[] = [
                'product' => $product,
                'count' => $request->counts[$key],
                'description' => $request->description[$key],
            ];
        }
        PriceRequest::create([
            'user_id' => auth()->id(),
            'max_send_time' => $request->max_send_time,
            'description' => $request->description,
            'items' => json_encode($items)
        ]);

        // notification sent to ceo
        $notifiables = User::where('id','!=',auth()->id())->whereHas('role' , function ($role) {
            $role->whereHas('permissions', function ($q) {
                $q->whereIn('name', ['ceo','sales-manager']);
            });
        })->get();

        $notif_message = 'یک درخواست قیمت توسط همکار فروش ثبت گردید';
        $url = route('price-requests.index');
        Notification::send($notifiables, new SendMessage($notif_message, $url));
        // end notification sent to ceo
        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ثبت درخواست قیمت',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') یک درخواست قیمت برای کالاها ثبت کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ثبت فعالیت در پایگاه داده
        alert()->success('درخواست قیمت با موفقیت ثبت شد','ثبت درخواست قیمت');
        return redirect()->route('price-requests.index');
    }

    public function show(PriceRequest $priceRequest)
    {
        $this->authorize('price-requests-list');

        return view('panel.price-requests.show', compact('priceRequest'));
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

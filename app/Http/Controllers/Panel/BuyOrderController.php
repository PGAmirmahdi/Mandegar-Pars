<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBuyOrderRequest;
use App\Models\Activity;
use App\Models\BuyOrder;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BuyOrderController extends Controller
{
    public function index()
    {
        $this->authorize('buy-orders-list');

        if (Gate::any(['admin','ceo','sales-manager'])){
            $orders = BuyOrder::latest()->paginate(30);
        }else{
            $orders = BuyOrder::where('user_id', auth()->id())->latest()->paginate(30);
        }

        return view('panel.buy-orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::all();

        return view('panel.buy-orders.create',compact('products'));
    }

    public function store(StoreBuyOrderRequest $request)
    {
        $this->authorize('buy-orders-create');

        $items = [];

        $products = $request->products;
        $counts = $request->counts;
        foreach ($products as $key => $product)
        {
            $items[] = [
                'product' => $product,
                'count' => $counts[$key],
            ];
        }
        // دریافت نام مشتری
//        $customerName = Customer::find($request->customer_id)->name;
        BuyOrder::create([
            'user_id' => auth()->id(),
//            'customer_id' => $request->customer_id,
            'description' => $request->description,
            'items' => json_encode($items),
        ]);
        // ثبت فعالیت کاربر
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'اضافه کردن سفارش خرید',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') سفارش خرید '  . 'اضافه کرد.',
        ]);
        alert()->success('سفارش مورد نظر با موفقیت ثبت شد','ثبت سفارش خرید');
        return redirect()->route('buy-orders.index');
    }

    public function show(BuyOrder $buyOrder)
    {
        $this->authorize('buy-orders-list');

        return view('panel.buy-orders.show', compact('buyOrder'));
    }

    public function edit(BuyOrder $buyOrder)
    {
        $this->authorize('buy-orders-edit');
        $this->authorize('edit-buy-order', $buyOrder);

        if (Gate::allows('ceo') || $buyOrder->status == 'bought'){
            return back();
        }

        return view('panel.buy-orders.edit', compact('buyOrder'));
    }

    public function update(StoreBuyOrderRequest $request, BuyOrder $buyOrder)
    {
        $this->authorize('buy-orders-edit');

        $items = [];

        $products = $request->products;
        $counts = $request->counts;
        foreach ($products as $key => $product)
        {
            $items[] = [
                'product' => $product,
                'count' => $counts[$key],
            ];
        }

        $buyOrder->update([
//            'customer_id' => $request->customer_id,
            'description' => $request->description,
            'items' => json_encode($items),
        ]);
        // دریافت نام مشتری
//        $customerName = Customer::find($request->customer_id)->name;
// ثبت فعالیت کاربر
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ویرایش سفارش خرید',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') سفارش خرید' .  ' را ویرایش کرد.',
        ]);
        alert()->success('سفارش مورد نظر با موفقیت ویرایش شد','ویرایش سفارش خرید');
        return redirect()->route('buy-orders.index');
    }

    public function destroy(BuyOrder $buyOrder)
    {
        $this->authorize('buy-orders-delete');

        // گرفتن نام مشتری
        $customerName = $buyOrder->customer->name;

        // ثبت فعالیت
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف سفارش خرید',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') سفارش خرید برای مشتری ' . $customerName . ' را حذف کرد.',
        ]);

        if (Gate::allows('ceo') || $buyOrder->status == 'bought') {
            return back();
        }

        // حذف سفارش خرید
        $buyOrder->delete();

        return back();
    }

    public function changeStatus(BuyOrder $buyOrder)
    {
        if (!Gate::allows('ceo')){
            return back();
        }

        if ($buyOrder->status == 'bought'){
            $buyOrder->update(['status' => 'order']);
        }else{
            $buyOrder->update(['status' => 'bought']);
        }
        // گرفتن نام مشتری
        $customerName = $buyOrder->customer->name;

        // ثبت فعالیت
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ویرایش وضعیت سفارش خرید',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') وضعیت سفارش خرید برای مشتری ' . $customerName . 'را تغییر داد.',
        ]);
        alert()->success('وضعیت سفارش با موفقیت تغییر کرد','تغییر وضعیت سفارش');
        return back();
    }
}

<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBuyOrderRequest;
use App\Models\BuyOrder;
use Illuminate\Http\Request;
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
        $this->authorize('buy-orders-create');

        if (Gate::allows('ceo')){
            return back();
        }

        return view('panel.buy-orders.create');
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

        BuyOrder::create([
            'user_id' => auth()->id(),
            'customer_id' => $request->customer_id,
            'description' => $request->description,
            'items' => json_encode($items),
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
            'customer_id' => $request->customer_id,
            'description' => $request->description,
            'items' => json_encode($items),
        ]);

        alert()->success('سفارش مورد نظر با موفقیت ویرایش شد','ویرایش سفارش خرید');
        return redirect()->route('buy-orders.index');
    }

    public function destroy(BuyOrder $buyOrder)
    {
        $this->authorize('buy-orders-delete');

        if (Gate::allows('ceo') || $buyOrder->status == 'bought'){
            return back();
        }

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

        alert()->success('وضعیت سفارش با موفقیت تغییر کرد','تغییر وضعیت سفارش');
        return back();
    }
}

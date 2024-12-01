<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\BuyOrder;
use App\Models\BuyOrderComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyOrderCommentController extends Controller
{
    public function create(BuyOrder $buyOrder)
    {
        return view('panel.buy-orders.comments.create', compact('buyOrder'));
    }

    public function store(Request $request, BuyOrder $buyOrder)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment=BuyOrderComment::create([
            'buy_order_id' => $buyOrder->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ارسال پیام',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') ' . 'پیام جدید "' . $comment->comment . '" در سفارش فروش ' . $comment->buyOrder->customer->name . ' گذاشت',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        return redirect()->route('buy-orders.comments.show', $buyOrder->id)
            ->with('success', 'نظر شما با موفقیت ثبت شد.');
    }
    public function show($id)
    {
        $order = BuyOrder::findOrFail($id);
        $comments = $order->comments;  // گرفتن کامنت‌ها مربوط به این سفارش

        return view('panel.buy-orders.comments.show', compact('order', 'comments'));
    }
}

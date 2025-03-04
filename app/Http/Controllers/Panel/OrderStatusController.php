<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Invoice;
use App\Models\OrderStatus;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderStatusController extends Controller
{
    public function index(Invoice $invoice)
    {
        $this->authorize('invoices-list');

        return view('panel.invoices.order-status', compact('invoice'));
    }

    public function changeStatus(Request $request)
    {
        $this->authorize('invoices-list');

        $status = $request->status;
        $index = array_search($status, array_keys(OrderStatus::STATUS));
        $nextStatuses = array_keys(array_slice(OrderStatus::STATUS, $index + 1));
        $prevStatuses = array_keys(array_slice(OrderStatus::STATUS, $index - $index, $index));

        OrderStatus::where('invoice_id', $request->invoice_id)->whereIn('status', $nextStatuses)->delete();
//        dd($prevStatuses);

        foreach ($prevStatuses as $item){
            OrderStatus::where('invoice_id', $request->invoice_id)->where('status', $item)->firstOrCreate([
                'status' => $item,
                'invoice_id' => $request->invoice_id,
            ],[
                'status' => $item,
                'order' => array_flip(OrderStatus::ORDER)[$item],
                'invoice_id' => $request->invoice_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        OrderStatus::where('invoice_id', $request->invoice_id)->where('status', $status)->firstOrCreate([
            'status' => $status,
            'invoice_id' => $request->invoice_id,
        ],[
            'status' => $status,
            'order' => array_flip(OrderStatus::ORDER)[$status],
            'invoice_id' => $request->invoice_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        // ثبت فعالیت تغییر وضعیت فاکتور
        $invoice = Invoice::find($request->invoice_id);  // فاکتور مورد نظر را پیدا می‌کنیم
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'تغییر وضعیت فاکتور',
            'description' => 'وضعیت فاکتور با شماره ' . $invoice->id . "توسط" . auth()->user()->family .  '(' . Auth::user()->role->label . ')' .' به "' . $status . '" تغییر یافت.',
            'created_at' => now(),
        ];
        Activity::create($activityData);  // ثبت فعالیت

        return back();
    }

    public function addDescription(Request $request)
    {
        Invoice::find($request->invoice_id)->update(['order_status_desc' => $request->description]);
    }
}

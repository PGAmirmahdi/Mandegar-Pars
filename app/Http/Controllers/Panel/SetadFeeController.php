<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSetadFeeRequest;
use App\Models\Activity;
use App\Models\Order;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SetadFee;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class SetadFeeController extends Controller
{

    public function index()
    {
        $this->authorize('setad-fee-list');
        $setadFees = SetadFee::query();
        $code = request()->query('code');
        $tracking_number = request()->query('tracking_number');
        $status = request()->query('status');

        if (auth()->user()->isAdmin() || auth()->user()->isAccountant() || auth()->user()->isCEO()) {
            if (!empty($code)) {
                $setadFees->whereHas('order', function ($query) use ($code) {
                    $query->where('code', 'like', '%' . $code . '%');
                });
            }
            if (!empty($tracking_number)) {
                $setadFees->where('tracking_number', 'like', '%' . $tracking_number . '%');
            }
            if (!empty($status) && $status !== 'all') {
                $setadFees->where('status', $status);
            }
            $setadFees = $setadFees->latest()->paginate(30);
        } else {
            if (!empty($code)) {
                $setadFees->whereHas('order', function ($query) use ($code) {
                    $query->where('code', 'like', '%' . $code . '%');
                });
            }
            if (!empty($tracking_number)) {
                $setadFees->where('tracking_number', 'like', '%' . $tracking_number . '%');
            }
            if (!empty($status) && $status !== 'all') {
                $setadFees->where('status', $status);
            }
            $setadFees = $setadFees->where('user_id', auth()->id())->latest()->paginate(30);
        }

        return view('panel.setad_fee.index', compact('setadFees'));


    }


    public function create()
    {
        $this->authorize('setad-fee-create');
        return view('panel.setad_fee.create');
    }


    public function store(StoreSetadFeeRequest $request)
    {

        $this->authorize('setad-fee-create');
        $order = Order::where('code', $request->input('order'))->first();

        $setad = new SetadFee();
        $setad->order_id = $order->id;
        $setad->tracking_number = $request->input('tracking_number');
        $setad->price = $request->input('price');
        $setad->shaba_number = $request->input('shaba_number');
        $setad->description = $request->input('description');
        $setad->user_id = auth()->id();
        $setad->save();
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ایجاد کارمزد',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')  کارمزد ستاد به شماره ' . $request->input('tracking_number') . ' ثبت کرد.'
        ]);

        $order->order_status()->updateOrCreate(
            ['status' => 'awaiting_confirm_by_sales_manager'],
            ['orders' => 4, 'status' => 'awaiting_confirm_by_sales_manager']
        );

        $order->order_status()->updateOrCreate(
            ['status' => 'setad_fee'],
            ['orders' => 5, 'status' => 'setad_fee']
        );

        $this->send_notif_to_accountants($order);
        $this->send_notif_to_sales_manager($order);

        alert()->success('کارمزد با موفقیت ثبت شد.', 'موفقیت آمیز');
        return redirect(route('setad-fee.index'));


    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $this->authorize('setad-fee-edit');
        $setad = SetadFee::findOrfail($id);
        $order = Order::findOrfail($setad->order_id);
        return view('panel.setad_fee.edit', compact(['setad', 'order']));

    }


    public function update(StoreSetadFeeRequest $request, $id)
    {
        $this->authorize('setad-fee-edit');
        $order = Order::where('code', $request->input('order'))->first();
        $setad = SetadFee::findOrfail($id);
        $setad->order_id = $order->id;
        $setad->tracking_number = $request->input('tracking_number');
        $setad->price = $request->input('price');
        $setad->shaba_number = $request->input('shaba_number');
        $setad->description = $request->input('description');
        $setad->user_id = auth()->id();
        $setad->save();
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ایجاد کارمزد',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')  کارمزد ستاد به شماره ' . $request->input('tracking_number') . ' ویرایش کرد.'
        ]);

        $order->order_status()->updateOrCreate(
            ['status' => 'awaiting_confirm_by_sales_manager'],
            ['orders' => 4, 'status' => 'awaiting_confirm_by_sales_manager']
        );

        $order->order_status()->updateOrCreate(
            ['status' => 'setad_fee'],
            ['orders' => 5, 'status' => 'setad_fee']
        );

        $this->send_notif_to_accountants($order);
        $this->send_notif_to_sales_manager($order);

        alert()->success('کارمزد با موفقیت ثبت شد.', 'موفقیت آمیز');
        return redirect(route('setad-fee.index'));
    }


    public function destroy($id)
    {
        $this->authorize('setad-fee-delete');
        $setad = SetadFee::findOrfail($id);
        $order = Order::findOrfail($setad->order_id);
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ایجاد کارمزد',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')  کارمزد ستاد به شناسه ' . $setad->هی . ' را حذف کرد.'
        ]);
        $order->order_status()->where('status', 'setad_fee')->delete();
        $order->order_status()->where('status', 'processing_by_accountant_step_2')->delete();
        $order->order_status()->where('status', 'upload_setad_fee')->delete();
        $setad->delete();
        return back();









    }

    public function search($orderCode)
    {
        $order = Order::where('code', $orderCode)->first();

        if ($order) {
            $totalPrice = $this->calculateTotalPrice(json_decode($order->products));

            $data = [
                'customer' => $order->customer->name,
                'total' => number_format($totalPrice),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'data' => null,
        ], 200);
    }


    public function action($id)
    {
        $this->authorize('setad-fee-edit');
        $setad = SetadFee::findOrfail($id);
        $order = Order::findOrfail($setad->order_id);
        return view('panel.setad_fee.action', compact(['setad', 'order']));

    }

    public function actionStore(Request $request, $id)
    {
        $this->authorize('setad-fee-edit');
        $setad = SetadFee::findOrfail($id);
        $order = Order::findOrfail($setad->order_id);
        $file = upload_file($request->receipt, 'Action/receipt');
        $setad->status = 'approved';
        $setad->receipt_file_path = $file;
        $setad->upload_time = now();
        $setad->save();

        $permissionsId = Permission::whereIn('name', ['sales-manager'])->pluck('id');
        $roles_id = Role::whereHas('permissions', function ($q) use ($permissionsId) {
            $q->whereIn('permission_id', $permissionsId);
        })->pluck('id');
        $title_message= 'کارمزد ستاد';
        $url = route('invoices.index');
        $notif_message = "رسید کارمزد ستاد با شناسه {$setad->order->code} دریافت شد";
        $accountants = User::whereIn('role_id', $roles_id)->get();
        Notification::send($accountants, new SendMessage($title_message,$notif_message, $url));


        $order->order_status()->updateOrCreate(
            ['status' => 'processing_by_accountant_step_2'],
            ['orders' => 6, 'status' => 'processing_by_accountant_step_2']
        );
        $order->order_status()->updateOrCreate(
            ['status' => 'upload_setad_fee'],
            ['orders' => 7, 'status' => 'upload_setad_fee']
        );
        alert()->success('رسید کارمزد با شناسه ' . $setad->code . ' با موفقیت آپلود شد.', 'موفقیت آمیز');
        return redirect(route('setad-fee.index'));
    }


    public function deleteReceiptFile($id)
    {
        $setad = SetadFee::findOrfail($id);
        $order = Order::findOrfail($setad->order_id);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف کارمزد',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')  کارمزد ستاد به شماره سفارش ' . ($setad->order_id) . ' را حذف کرد.'
        ]);
        unlink(public_path($setad->receipt_file_path));
        $setad->update([
            'receipt_file_path' => null,
            'upload_time' => null,
            'status' => 'pending',
        ]);
        $order->order_status()->where('status', 'processing_by_accountant_step_2')->delete();
        $order->order_status()->where('status', 'upload_setad_fee')->delete();


        alert()->success('فایل رسید مورد نظر حذف شد', 'حذف فایل رسید کارمزد');
        return back();
    }




    private function calculateTotalPrice($order)
    {
        $totalPrice = 0;


        if (!empty($order->products)) {
            foreach ($order->products as $product) {
                $totalPrice += (int)$product->total_prices;
            }
        }


        if (!empty($order->other_products)) {
            foreach ($order->other_products as $otherProduct) {
                $totalPrice += (int)$otherProduct->other_total_prices;
            }
        }

        return $totalPrice;
    }


    private function send_notif_to_accountants(Order $order)
    {
        $roles_id = Role::whereHas('permissions', function ($q) {
            $q->where('name', 'accountant');
        })->pluck('id');
        $accountants = User::where('id', '!=', auth()->id())->whereIn('role_id', $roles_id)->get();
        $title='ثبت کارمزد ستاد';
        $url = route('setad-fee.edit', $order->id);
        $message = "کارمزد ستاد به شناسه سفارش '{$order->code}' ثبت شد";

        Notification::send($accountants, new SendMessage($title,$message, $url));
    }

    private function send_notif_to_sales_manager(Order $order)
    {
        $roles_id = Role::whereHas('permissions', function ($q) {
            $q->where('name', 'sales-manager');
        })->pluck('id');
        $managers = User::where('id', '!=', auth()->id())->whereIn('role_id', $roles_id)->get();
        $title='ثبت کارمزد ستاد';
        $url = route('setad-fee.edit', $order->id);
        $message = "کارمزد ستاد به شناسه سفارش '{$order->code}' ثبت شد";

        Notification::send($managers, new SendMessage($title,$message, $url));
    }
}

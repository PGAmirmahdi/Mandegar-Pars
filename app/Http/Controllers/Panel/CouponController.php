<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Activity;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function index()
    {
        $this->authorize('coupons-list');

        $coupons = Coupon::latest()->paginate(30);
        return view('panel.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $this->authorize('coupons-create');

        return view('panel.coupons.create');
    }

    public function store(StoreCouponRequest $request)
    {
        $this->authorize('coupons-create');

        Coupon::create([
            'title' => $request->title,
            'code' => $request->code,
            'amount_pc' => $request->amount_pc,
        ]);
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ثبت کد تخفیف',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') کد تخفیف ' . $request->title . ' را ثبت کرد.',
        ]);
        alert()->success('کد تخفیف مورد نظر با موفقیت ایجاد شد','ایجاد کد تخفیف');
        return redirect()->route('coupons.index');
    }

    public function show(Coupon $coupon)
    {
        //
    }

    public function edit(Coupon $coupon)
    {
        $this->authorize('coupons-edit');

        return view('panel.coupons.edit', compact('coupon'));

    }

    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $this->authorize('coupons-edit');

        $coupon->update([
            'title' => $request->title,
            'code' => $request->code,
            'amount_pc' => $request->amount_pc,
        ]);
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ویرایش کد تخفیف',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') کد تخفیف ' . $request->title . ' را ویرایش کرد.',
        ]);
        alert()->success('کد تخفیف مورد نظر با موفقیت ویرایش شد','ویرایش کد تخفیف');
        return redirect()->route('coupons.index');
    }

    public function destroy(Coupon $coupon)
    {
        $this->authorize('coupons-delete');
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ثبت کد تخفیف',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') کد تخفیف ' . $coupon->title . ' را حذف کرد.',
        ]);
        $coupon->delete();
        return back();
    }
}

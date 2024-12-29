<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Debtor;
use Illuminate\Http\Request;

class DebtorController extends Controller
{
    public function index(Request $request)
    {
        $query = Debtor::query();

        // فیلتر بر اساس کد مشتری
        if ($request->filled('customer_code')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('code', $request->customer_code);
            });
        }

        // فیلتر بر اساس نام مشتری
        if ($request->filled('customer_name')) {
            $query->where('customer_id', $request->customer_name);
        }

        // فیلتر بر اساس وضعیت
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // دریافت لیست بدهکاران
        $debtors = $query->with('customer')->paginate(10);

        // دریافت لیست مشتریان برای Select2
        $customers = Customer::all();

        return view('panel.debtors.index', compact('debtors', 'customers'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('panel.debtors.create', compact('customers'));
    }

    public function store(Request $request)
    {
        // اعتبارسنجی داده‌ها
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:unpaid,paid,partial,followed',
            'description' => 'nullable',
            'factor_number' => 'nullable' ,
            'buy_date' => 'nullable' ,
            'payment_due' => 'nullable' ,
        ]);

        // ذخیره‌سازی بدهکار
        $debtor = Debtor::create([
            'customer_id' => $request->customer_id,
            'price' => $request->price,
            'status' => $request->status,
            'description' => $request->description,
            'factor_number' => $request->factor_number,
            'buy_date' => $request->buy_date,
            'payment_due' => $request->payment_due,
        ]);

        // پیدا کردن مشتری و ثبت فعالیت
        $customer = Customer::find($request->customer_id);

        if ($customer) {
            $data = [
                'user_id' => auth()->id(),
                'action' => 'ایجاد بدهکار',
                'description' => 'کاربر ' . auth()->user()->family . '(' . auth()->user()->role->label . ') بدهکاری برای مشتری ' . $customer->name . ' ایجاد کرد',
            ];
            Activity::create($data);
        }

        // نمایش پیام موفقیت
        alert()->success('بدهکار با موفقیت ثبت شد', 'ثبت بدهکار');

        // بازگشت به لیست بدهکاران
        return redirect()->route('debtors.index');
    }


    public function show(Debtor $debtor)
    {
        return view('panel.debtors.show', compact('debtor'));
    }

    public function edit(Debtor $debtor)
    {
        $customers = Customer::all(); // لیست مشتری‌ها
        return view('panel.debtors.edit', compact('debtor', 'customers'));
    }

    public function update(Request $request, $id)
    {
        $debtor = Debtor::findOrFail($id);

        // اعتبارسنجی داده‌ها
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:unpaid,paid,partial,followed',
            'description' => 'nullable|string|max:255',
            'recipe' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:8192',
            'factor_number' => 'nullable',
            'buy_date' => 'nullable' ,
            'payment_due' => 'nullable',
        ]);

        // اگر فایل جدید ارسال شده باشد، آن را ذخیره می‌کنیم
        if ($request->hasFile('recipe')) {
            $file = $request->file('recipe');
            $path = $file->store('receipts', 'public'); // ذخیره در پوشه 'receipts' داخل public
        } else {
            $path = $debtor->recipe; // اگر فایلی ارسال نشده باشد، مسیر قبلی را نگه می‌داریم
        }

        // به‌روزرسانی بدهکار
        $debtor->update([
            'customer_id' => $request->customer_id,
            'factor_number' => $request->factor_number,
            'buy_date' => $request->buy_date,
            'payment_due' => $request->payment_due,
            'price' => $request->price,
            'status' => $request->status,
            'description' => $request->description,
            'recipe' => $path,
        ]);

        // ثبت فعالیت
        $customer = $debtor->customer; // پیدا کردن مشتری
        $data = [
            'user_id' => auth()->id(),
            'action' => 'به‌روزرسانی بدهکار',
            'description' => 'کاربر ' . auth()->user()->family . '(' . auth()->user()->role->label . ') بدهکاری برای مشتری ' . ($customer ? $customer->name : 'نامشخص') . ' به‌روزرسانی کرد',
        ];
        Activity::create($data);

        alert()->success('بدهکار با موفقیت به‌روزرسانی شد', 'بروزرسانی بدهکار');

        return redirect()->route('debtors.index');
    }

    public function destroy(Debtor $debtor)
    {
        $customer = $debtor->customer; // پیدا کردن مشتری
        $data = [
            'user_id' => auth()->id(),
            'action' => 'حذف بدهکار',
            'description' => 'کاربر ' . auth()->user()->family . '(' . auth()->user()->role->label . ') بدهکاری برای مشتری ' . ($customer ? $customer->name : 'نامشخص') . ' را حذف کرد',
        ];
        Activity::create($data);
        $debtor->delete();
        alert()->success('بدهکار با موفقیت حذف شد','حذف بدهکار');
        return redirect()->route('debtors.index');
    }
}

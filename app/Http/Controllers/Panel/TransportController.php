<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransportRequest;
use App\Models\Invoice;
use App\Models\Transport;
use App\Models\Transporter;
use App\Models\TransportItem;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    // نمایش لیست همه حمل و نقل کننده‌ها
    public function index()
    {
        $transports = Transport::paginate(10);
        return view('panel.transport.index', compact('transports'));
    }

    // نمایش فرم ایجاد حمل و نقل کننده جدید
    public function create()
    {
        return view('panel.transport.create');
    }

    // ذخیره حمل و نقل کننده جدید
    public function store(StoreTransportRequest $request)
    {
        // ذخیره اطلاعات کلی در جدول `transports`
        $transport = Transport::create([
            'invoice_id' => $request->invoice_id,
            'status' => 'level1',
            'user_id' => auth()->id(),
        ]);

        // ذخیره اطلاعات جزئی هر حمل و نقل کننده در جدول `transport_items`
        foreach ($request->transporters as $index => $transporter_id) {
            TransportItem::create([
                'transport_id' => $transport->id,
                'transporter_id' => $transporter_id,
                'price' => $request->prices[$index],
                'payment_type' => $request->payment_type[$index],
            ]);
        }
        alert()->success('حمل و نقل با موفقیت ایجاد شد','موفق');
        return redirect()->route('transports.index')->with('success', 'حمل و نقل با موفقیت ثبت شد');
    }


    // نمایش یک حمل و نقل کننده خاص
    public function show($id)
    {
        $transporter = Transporter::findOrFail($id);
        return view('panel.transport.show', compact('transporter'));
    }

    // نمایش فرم ویرایش حمل و نقل کننده
    public function edit($id)
    {
        // پیدا کردن حمل و نقل از جدول `transports` با استفاده از id
        $transport = Transport::findOrFail($id);

        // پیدا کردن فاکتور مربوط به حمل و نقل
        $invoice = $transport->invoice;

        // پیدا کردن حمل و نقل‌کننده‌ها برای این حمل و نقل خاص
        $transportItems = TransportItem::where('transport_id', $transport->id)->get();

        // ارسال داده‌ها به ویو
        return view('panel.transport.edit', compact('transport', 'invoice', 'transportItems'));
    }
// به‌روزرسانی حمل و نقل‌ها
    public function update(Request $request, $id)
    {
        $request->validate([
            'transporters' => 'required|array',
            'transporters.*' => 'exists:transporters,id',
            'prices' => 'required|array',
            'prices.*' => 'numeric|min:0',
            'payment_type' => 'required|array',
        ]);

        // ابتدا حمل و نقل کلی را بروزرسانی می‌کنیم
        $transport = Transport::findOrFail($id);

        $transport->update([
            'invoice_id' => $request->invoice_id,
            'status' => 'level1',  // می‌توانید وضعیت را طبق نیاز به روز کنید
            'user_id' => auth()->id(),
        ]);

        $transport->items()->delete();

        // سپس جزئیات حمل و نقل‌کننده‌ها را در جدول `transport_items` بروزرسانی می‌کنیم
        foreach ($request->transporters as $key => $transporter_id) {
            $transport->items()->create([
                'transporter_id' => $transporter_id,
                'price' => $request->prices[$key],
                'payment_type' => $request->payment_type[$key],
            ]);
        }

        alert()->success('حمل و نقل با موفقیت به روز رسانی شد', 'موفق');
        return redirect()->route('transports.index')->with('success', 'حمل و نقل‌ها با موفقیت به روز رسانی شدند');
    }


    // حذف یک حمل و نقل کننده
    public function destroy(Transport $transport)
    {
        $transport->delete();

        return redirect()->route('transports.index')->with('success', 'حمل و نقل کننده با موفقیت حذف شد');
    }
    public function getInvoiceInfo($invoice_id)
    {
        // دریافت فاکتور از دیتابیس با توجه به invoice_id
        $invoice = Invoice::find($invoice_id);

        if ($invoice) {
            // اگر فاکتور پیدا شد، اطلاعات مربوطه را ارسال می‌کنیم
            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $invoice->customer->name, // نام مشتری
                    'address' => $invoice->address,    // آدرس مشتری
                ]
            ]);
        } else {
            // در صورتی که فاکتور پیدا نشد
            return response()->json([
                'success' => false,
                'message' => 'فاکتور پیدا نشد.'
            ]);
        }
    }
    public function accounting($id)
    {
        $transport = Transport::findOrFail($id); // پیدا کردن حمل و نقل
        $invoice = $transport->invoice; // پیدا کردن فاکتور مرتبط
        $transportItems = TransportItem::where('transport_id', $transport->id)->get(); // پیدا کردن آیتم‌های حمل و نقل

        return view('panel.transport.accountant', compact('transport', 'invoice', 'transportItems'));
    }
    public function accountantupdate(Request $request, $id)
    {
        // پیدا کردن حمل و نقل
        $transport = Transport::findOrFail($id);

        // دریافت شناسه آیتم انتخاب‌شده از درخواست
        $selectedItemId = $request->input('selected_item'); // این شناسه آیتم انتخابی است

        // اطمینان از اینکه آیتم انتخابی معتبر است
        $request->validate([
            'selected_item' => 'required|exists:transport_items,id', // اعتبارسنجی اینکه آیتم انتخابی معتبر باشد
        ]);

        // پیدا کردن آیتم انتخاب‌شده
        $selectedItem = TransportItem::findOrFail($selectedItemId);

        $transport->status = 'level2'; // تغییر وضعیت حمل‌ونقل به 'level2'
        $transport->save();

        $selectedItem->select = 'selected'; // مثال: تغییر وضعیت آیتم انتخابی
        $selectedItem->save();

        // بازگشت به صفحه حمل و نقل‌ها با پیام موفقیت
        return redirect()->route('transports.index')->with('success', 'آیتم انتخابی با موفقیت ذخیره شد.');
    }
    public function bijak($id)
    {
        $transport = Transport::findOrFail($id); // پیدا کردن حمل و نقل
        $invoice = $transport->invoice; // پیدا کردن فاکتور مرتبط
        $transportItems = TransportItem::where('transport_id', $transport->id)->get(); // پیدا کردن آیتم‌های حمل و نقل

        return view('panel.transport.bijak', compact('transport', 'invoice', 'transportItems'));
    }
    public function storeBijak(Request $request, $id)
    {
        $transport = Transport::findOrFail($id);

        // اعتبارسنجی فایل
        $request->validate([
            'bijak' => 'required|mimes:pdf,jpeg,png,jpg|max:2048', // انواع فایل‌های مجاز
        ]);

        // ذخیره فایل با استفاده از تابع upload_file
        $filePath = upload_file($request->file('bijak'), 'bijaks');

        // ذخیره در دیتابیس
        $transport->bijak_path = $filePath;
        $transport->status = 'level3';
        $transport->description = $request->description;
        $transport->save();

        // بازگشت به صفحه با پیغام موفقیت
        return redirect()->route('transports.index')->with('success', 'ویجت با موفقیت ثبت شد.');
    }
    public function finalaccounting($id)
    {
        $transport = Transport::findOrFail($id); // پیدا کردن حمل و نقل
        $invoice = $transport->invoice; // پیدا کردن فاکتور مرتبط
        $transportItems = TransportItem::where('transport_id', $transport->id)->get(); // پیدا کردن آیتم‌های حمل و نقل
        // دریافت مسیر فایل ویجت
        $widgetPath = $transport->bijak_path;
        return view('panel.transport.finalaccept', compact('transport', 'invoice', 'transportItems','widgetPath'));
    }
    public function finalaccountantupdate(Request $request, $id)
    {
        $transport = Transport::findOrFail($id);

        if ($request->status == 'level2') {
            // تغییر وضعیت به level2 و حذف لینک فایل
            $transport->status = 'level2';
            $transport->bijak_path = null;  // یا هر کد دیگر برای حذف لینک فایل
        } elseif ($request->status == 'level4') {
            // تغییر وضعیت به level4
            $transport->status = 'level4';
        }

        $transport->save();

        return redirect()->back()->with('success', 'وضعیت با موفقیت تغییر یافت.');
    }

}

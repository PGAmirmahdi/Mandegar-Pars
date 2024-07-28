<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Sms;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SoapClient;

class SMSController extends Controller
{
    public function index()
    {
        $this->authorize('sms-list');
        $smsList = Sms::query()->paginate(10);

        return view('sms.index', compact('smsList'));
    }

    public function create()
    {
        $this->authorize('sms-create');
        return view('panel.sms.create');
    }

    public function store(Request $request)
    {
        // بررسی دسترسی کاربر
        $this->authorize('sms-create');

        // اعتبارسنجی درخواست
        $request->validate([
            'receiver_name' => 'required',
            'receiver_phone' => 'required',
            'message' => 'required',
        ]);

        // تنظیمات SOAP
        ini_set("soap.wsdl_cache_enabled", "0");

        // پیام‌های خطا
        $errorMessages = [
            '-7' => 'خطایی در شماره فرستنده رخ داده است با پشتیبانی تماس بگیرید',
            '-6' => 'خطای داخلی رخ داده است با پشتیبانی تماس بگیرید',
            '-5' => 'متن ارسالی باتوجه به متغیرهای مشخص شده در متن پیشفرض همخوانی ندارد',
            '-4' => 'کد متن ارسالی صحیح نمی‌باشد و یا توسط مدیر سامانه تأیید نشده است',
            '-3' => 'خط ارسالی در سیستم تعریف نشده است، با پشتیبانی سامانه تماس بگیرید',
            '-2' => 'محدودیت تعداد شماره، محدودیت هربار ارسال یک شماره موبایل می‌باشد',
            '-1' => 'دسترسی برای استفاده از این وبسرویس غیرفعال است. با پشتیبانی تماس بگیرید',
            '0' => 'نام کاربری یا رمزعبور صحیح نمی‌باشد',
            '2' => 'اعتبار کافی نمی‌باشد',
            '6' => 'سامانه درحال بروزرسانی می‌باشد',
            '7' => 'متن حاوی کلمه فیلتر شده می‌باشد، با واحد اداری تماس بگیرید',
            '10' => 'کاربر موردنظر فعال نمی‌باشد',
            '11' => 'ارسال نشده',
            '12' => 'مدارک کاربر کامل نمی‌باشد',
        ];

        try {
            // ارسال پیامک
            $result = sendSMS(201523, $request->receiver_phone, [$request->message]);

            // بررسی نتیجه ارسال
            $status = array_key_exists($result, $errorMessages) ? $errorMessages[$result] : 'ارسال موفقیت‌آمیز';

            if (array_key_exists($result, $errorMessages)) {
                return response()->json(['failed' => $errorMessages[$result]]);
            } else {
                // ایجاد رکورد پیامک
                Sms::create([
                    'receiver_name' => $request->receiver_name,
                    'receiver_phone' => $request->receiver_phone,
                    'user_id' => Auth::id(),
                    'message' => $request->message,
                    'status' => $status,
                ]);

                return response()->json(['success' => 'sms با موفقیت ارسال شد']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'خطایی در ارسال پیام رخ داده است: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $this->authorize('sms-show');
        // یافتن پیامک با شناسه مورد نظر
        $sms = Sms::findOrFail($id);
        // ارسال اطلاعات پیامک به ویو
        return view('show', ['sms' => $sms]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $this->authorize('sms-delete');
        // یافتن پیامک با شناسه مورد نظر
        $sms = Sms::findOrFail($id);

        // حذف پیامک
        $sms->delete();

        // بازگشت پیام موفقیت
        return redirect()->back()->with('success', 'پیامک با موفقیت حذف شد');
    }

    public function search(Request $request)
    {
        $query = Sms::query();

        if ($request->filled('receiver_name')) {
            $query->where('receiver_name', 'like', '%' . $request->receiver_name . '%');
        }

        if ($request->filled('receiver_phone')) {
            $query->where('receiver_phone', 'like', '%' . $request->receiver_phone . '%');
        }
        $smsList = $query->paginate(10);

        return view('panel.sms.index', compact('smsList'));
    }
}

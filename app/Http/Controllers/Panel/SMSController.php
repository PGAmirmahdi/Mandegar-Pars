<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Sms;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SoapClient;

class SMSController extends Controller
{
    public function index()
    {
        $this->authorize('sms-list');
        $smsList = Sms::query()->orderByDesc('id')->paginate(20);

        return view('panel.sms.index', compact('smsList'));
    }

    public function create()
    {
        $this->authorize('sms-create');
        $customers= Customer::all();
        return view('panel.sms.create',compact('customers'));
    }

    public function store(Request $request)
    {
        $this->authorize('sms-create');

        $request->validate([
            'receiver_name' => 'required',
            'receiver_phone' => 'required',
            'message' => 'nullable',
        ]);

        $successMessages = [
            1 => 'پیام با موفقیت ارسال شد',
            200 => 'ارسال شده',
        ];

        $errorMessages = [
            null => 'غیر فعال بودن دسترسی گزارش تحویل برای کاربر',
            -1 => 'ارسال نشده',
            -3 => 'نام کاربری یا رمز عبور اشتباه است',
            0 => 'ارسال شده به مخابرات',
            2 => 'نرسیده به گوشی',
            3 => 'خطای مخابراتی',
            5 => 'خطای نامشخص',
            8 => 'رسیده به مخابرات',
            16 => 'نرسیده به مخابرات',
            35 => 'لیست سیاه',
            100 => 'نامشخص',
            300 => 'فیلتر شده',
            400 => 'در لیست ارسال',
            500 => 'عدم پذیرش',
        ];

        try {
            $data = [
                'username' => '09336533433',
                'password' => '31$9#',
                'to' => $request->receiver_phone,
                'from' => '50002710033433',
                'text' => $request->message,
            ];

            $post_data = http_build_query($data);

            $handle = curl_init('https://rest.payamak-panel.com/api/SendSMS/SendSMS');
            curl_setopt($handle, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);

            $response = curl_exec($handle);

            if ($response === false) {
                throw new Exception(curl_error($handle), curl_errno($handle));
            }

            curl_close($handle);

            $responseDecoded = json_decode($response, true);

            if (isset($responseDecoded['RetStatus'])) {
                $status = $responseDecoded['RetStatus'];

                $sms = Sms::create([
                    'user_id' => auth()->id(),
                    'receiver_name' => $request->receiver_name,
                    'receiver_phone' => $request->receiver_phone,
                    'message' => $request->message,
                    'status' => $status,
                ]);
                // ثبت فعالیت در جدول activities
                Activity::create([
                    'user_id' => auth()->id(),
                    'action' => 'ارسال پیامک',
                    'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')'  . 'به فردی به نام ' . $sms->receiver_name . ' ارسال کرده است',
                    'created_at' => now(),
                ]);
                Log::info('SMS Sent', [
                    'user_id' => auth()->id(),
                    'receiver_name' => $request->receiver_name,
                    'receiver_phone' => $request->receiver_phone,
                    'message' => $request->message,
                    'status' => $status,
                ]);
                if (array_key_exists($status, $errorMessages)) {
                    alert()->error($errorMessages[$status], 'ارسال پیامک');

                } elseif (array_key_exists($status, $successMessages)) {
                    alert()->success($successMessages[$status], 'ارسال پیامک');
                } else {
                    alert()->info('وضعیت پیامک ناشناخته است.', 'ارسال پیامک');
                }

                return redirect()->route('sms.index');
            } else {
                alert()->error('پاسخ نامعتبر از سرویس پیامک دریافت شد.', 'خطا در ارسال پیامک');
                return redirect()->route('sms.index');
            }

        } catch (Exception $e) {
            alert()->error('پیامک با خطا مواجه شد: ' . $e->getMessage(), 'خطا در ارسال پیامک');
            return redirect()->route('sms.index');
        }
    }

    public function show($id)
    {
        // فرض کنید داده‌های پیامک را از دیتابیس دریافت می‌کنید
        $sms = Sms::findOrFail($id);

        return view('panel.sms.show', [
            'user' => $sms->user, // ارسال شیء کاربر به ویو
            'receiver_name' => $sms->receiver_name,
            'receiver_phone' => $sms->receiver_phone,
            'message' => $sms->message
        ]);
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
        // ثبت فعالیت در جدول activities
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف پیامک',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . optional(Auth::user()->role)->label . ') پیامک کاربر ' . $sms->receiver_name . ' را حذف کرده است.',
            'created_at' => now(),
        ]);
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

        if ($request->filled('user_name')) {
            // جستجو در جدول users بر اساس نام و نام خانوادگی
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user_name . '%')
                    ->orWhere('family', 'like', '%' . $request->user_name . '%');
            });
        }

        $smsList = $query->paginate(10);

        return view('panel.sms.index', compact('smsList'));
    }
}

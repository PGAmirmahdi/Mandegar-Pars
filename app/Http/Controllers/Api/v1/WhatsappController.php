<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Whatsapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class WhatsappController extends Controller
{
    public function index()
    {
        $whatsapps = Whatsapp::latest()->paginate(10);
        return view('panel.Whatsapp.index', compact('whatsapps'));
    }

    public function create()
    {
        $defaultMessage = 'با عرض سلام خدمت شما
خوشحالیم که شما را در جمع مشتریان ارزشمند خود داریم. برای اطلاع از جدیدترین اخبار و پیشنهادات ویژه، ما را در صفحات اجتماعی دنبال کنید:

دانلود کاتالوگ محصولات:
https://artintoner.com/folder/Catalog-v1.3.10.pdf

فروشگاه اینترنتی:
https://artintoner.com

اپلیکیشن:
https://mpsystem.ir/Discover

اینستاگرام:
www.instagram.com/artintoner.ir

شماره تماس
02165425052-54
09906424827
09014667657
09027386996
با سپاس،
';
        $customers = Customer::all();
        return view('panel.Whatsapp.create', compact('customers', 'defaultMessage'));
    }

    public function store(Request $request)
    {
        $url = 'https://wesender.ir/Send';
        $sender = env('WESENDER_SENDER');
        $key = env('WESENDER_KEY');
        $message = $request->input('description');
        $dateAdd = 1;

        // دریافت شماره‌های گیرنده به صورت آرایه از فیلد phones
        $receivers = json_decode($request->input('phones'), true);
        $receiverNames = json_decode($request->input('receiver_names'), true);  // دریافت نام‌ها

        if (!is_array($receivers) || !is_array($receiverNames)) {
            return response()->json(['error' => 'شماره‌ها و نام‌ها باید به صورت آرایه ارسال شوند.'], 400);
        }

        $responses = [];

        // بررسی تعداد نام‌ها و شماره‌ها
        if (count($receivers) !== count($receiverNames)) {
            return response()->json(['error' => 'تعداد نام‌ها و شماره‌ها باید برابر باشند.'], 400);
        }

        foreach ($receivers as $index => $receiver) {
            $receiverName = $receiverNames[$index];  // نام گیرنده از آرایه نام‌ها

            // اضافه کردن 98 به ابتدای هر شماره
            $formattedReceiver = '98' . ltrim($receiver, '0');

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode([
                    'message' => $message,
                    'dateAdd' => $dateAdd,
                ]),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    "receivers: $formattedReceiver",
                    "sender: $sender",
                    "key: $key",
                ],
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            $status = $error ? 'failed' : 'successful';
            $responses[] = [
                'receiver' => $receiver,
                'receiver_name' => $receiverName,
                'status' => $status,
                'response' => json_decode($response, true),
            ];

            // ذخیره اطلاعات پیام در مدل Whatsapp
            Whatsapp::create([
                'user_id' => auth()->id(),
                'sender_name' => auth()->user()->name . ' ' .  auth()->user()->family,
                'receiver_name' => $receiverName,  // ذخیره نام گیرنده
                'phone' => $receiver,
                'description' => $message,
                'status' => $status,
            ]);
        }
        alert()->success('پیام‌ها با موفقیت ارسال شدند.', 'موفقیت');
        return redirect()->route('whatsapp.index');
    }


    public function show($id)
    {
        // فرض کنید داده‌های پیامک را از دیتابیس دریافت می‌کنید
        $sms = Whatsapp::findOrFail($id);

        return view('panel.Whatsapp.show', [
            'user' => $sms->user, // ارسال شیء کاربر به ویو
            'receiver_name' => $sms->receiver_name,
            'receiver_phone' => $sms->receiver_phone,
            'message' => $sms->message
        ]);
    }


    // حذف پیام از دیتابیس
    public function destroy($id)
    {
        $message = Whatsapp::findOrFail($id);
        $message->delete();
        alert()->success('پیام‌ها با موفقیت حذف شدند.', 'موفق');
        return redirect()->route('whatsapp.index');
    }
}

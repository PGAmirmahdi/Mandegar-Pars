<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function index()
    {
        if (in_array(auth()->user()->role->name, ['admin', 'ceo', 'office-manager'])) {
            // نمایش گفتگوهای همه کاربران
            $messages = ChatMessage::select('chat_messages.*')
                ->whereIn('id', function($query) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('chat_messages')
                        ->groupBy('user_id');
                })
                ->with('user')
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
        } else {
            // نمایش گفتگوی کاربر فعلی (در این حالت تنها یک گفتگوی برای کاربر وجود دارد)
            $messages = ChatMessage::select('chat_messages.*')
                ->where('user_id', auth()->user()->id)
                ->whereIn('id', function($query) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('chat_messages')
                        ->where('user_id', auth()->user()->id)
                        ->groupBy('user_id');
                })
                ->with('user')
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
        }
        return view('panel.Ai.index', compact('messages'));
    }


    public function create()
    {
        $messages = ChatMessage::where('user_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();
        return view('panel.Ai.create', compact('messages'));
    }


    public function store(Request $request)
    {
        $prompt = $request->input('prompt');
        $userId = auth()->id(); // یا در صورت استفاده از سیستم احراز هویت، ID کاربر فعلی

        // ذخیره پیام کاربر در دیتابیس
        ChatMessage::create([
            'user_id' => $userId,
            'message' => $prompt,
            'is_user_message' => true,
        ]);
        // تنظیمات API Deep Seek
        $api_key = env('DEEPSEEK_API_KEY');
        $endpoint = env('DEEPSEEK_ENDPOINT');

        $url = $endpoint . '/chat/completions';

        // آماده‌سازی داده‌های ارسال شده طبق داکیومنت
        $payload = [
            "messages" => [
                [
                    "content" => "You are a kind and smart assistant working at (ماندگار پارس) company, developed by (امیرمهدی اسدی). You must help users only with Cartridges, Printers, Ribbon labels, Papers, Wax Resin, Toners, Copiers, and Scanners, and you should not answer any other questions. You should mention your company and developer only if the user asks you to introduce yourself.

If the user asks, you can introduce the company as follows:

'If you are looking for a company that produces high-quality toner and cartridges, Artin Toner aims to provide reliable and premium products in the field of toner and cartridges. Our head office is located in Tehran, and we sell our products both inside and outside Iran, in wholesale and retail markets.

    Artin Toner products are manufactured with HP design, Apex chipsets, and Artin Toner’s exclusive design and packaging. By utilizing modern technology and advanced equipment, we deliver high-quality products at competitive prices. With 30 different toner cartridge models, Artin Toner is ready to meet your various needs.

    We take pride in attracting foreign investors and collaborating with Iranian experts to produce long-lasting products that satisfy our customers. Mandegar Pars Office Machine Industries Company has had the honor of working with major brands such as HP, Canon, Brother, Epson, and more throughout its years of operation.'
    then you can mention our website www.artintoner.com and after that your developer",

                    "role" => "system"
                ],
                [
                    "content" => $prompt,
                    "role" => "user"
                ]
            ],
            "model" => "deepseek/deepseek-chat:free",
            "frequency_penalty" => 0,
            "max_tokens" => 2048,
            "presence_penalty" => 0,
            "response_format" => [
                "type" => "text"
            ],
            "stop" => null,
            "stream" => false,
            "stream_options" => null,
            "temperature" => 1,
            "top_p" => 1,
            "tools" => null,
            "tool_choice" => "none",
            "logprobs" => false,
            "top_logprobs" => null
        ];

        // تنظیم و اجرای cURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $api_key,
            ],
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => 'خطا در دریافت پاسخ از Deep Seek API: ' . $error_msg], 500);
        }

        curl_close($curl);

        $resultData = json_decode($response, true);

// استخراج محتوای پیام هوش مصنوعی
        $aiMessage = $resultData['choices'][0]['message']['content'] ?? 'پاسخی دریافت نشد';

        // ذخیره پیام هوش مصنوعی در دیتابیس
        ChatMessage::create([
            'user_id' => $userId,
            'message' => $aiMessage,
            'is_user_message' => false,
        ]);
        return response()->json($resultData);
    }
    public function show($userId)
    {
        $messages = ChatMessage::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($messages->isEmpty()) {
            return abort(404, 'چت یافت نشد');
        }

        return view('panel.Ai.show', compact('messages'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

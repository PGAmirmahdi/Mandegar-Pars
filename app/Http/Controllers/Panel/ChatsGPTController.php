<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatsGPTController extends Controller
{
    public function index()
    {
        // گرفتن آخرین پیام‌های هر کاربر
        $conversations = ChatMessage::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc') // مرتب‌سازی بر اساس آخرین زمان پیام
            ->paginate(10);
        // بررسی مجوز دسترسی
        if (auth()->user()->can('ChatGPT-list')) {
            return view('panel.ChatGPT.index', compact('conversations'));
        } else {
            return view('panel.ChatGPT.create', compact('conversations'));
        }

    }

    public function create()
    {
        $messages = ChatMessage::where('user_id', auth()->id())->get();
        return view('panel.ChatGPT.create', compact('messages'));
    }
    public function __construct(){
        $this->authorization = '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';
        $this->endpoint = 'https://api.openai.com/v1/chat/completions';
    }

    public function store(Request $request)
    {
        // اعتبارسنجی ورودی
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = auth()->user();
        $messageText = $request->input('message');

        // ذخیره پیام کاربر در دیتابیس
        $chatMessage = ChatMessage::create([
            'user_id' => $user->id,
            'message' => $messageText,
            'is_user_message' => true,
        ]);

        $chatMessage->touch();

        $data = json_encode([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a kind and helpful customer service member at a cartridge store. If the user asks how to buy, refer them to our website at https://artintoner.com/.'
                ],
                ['role' => 'user', 'content' => $messageText],
            ],
        ]);

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->authorization,
        ];

        // تنظیمات cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);

        // اضافه کردن پروکسی بدون احراز هویت
        curl_setopt($ch, CURLOPT_PROXY, 'http://104.234.46.169:3128'); // آدرس پروکسی شما

        $response = curl_exec($ch);

        // بررسی خطا
        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            curl_close($ch); // بسته شدن cURL قبل از بازگشت خطا
            return response()->json(['error' => 'cURL error: ' . $errorMessage], 500);
        }

        $gptResponse = json_decode($response, true);

        // بررسی وضعیت پاسخ
        if (isset($gptResponse['choices']) && count($gptResponse['choices']) > 0) {
            $responseMessage = $gptResponse['choices'][0]['message']['content'];

            // ذخیره پاسخ ChatGPT در دیتابیس
            ChatMessage::create([
                'user_id' => $user->id,
                'message' => $responseMessage,
                'is_user_message' => false,
            ]);

            // ارسال پاسخ به سمت فرانت‌اند
            curl_close($ch); // بسته شدن cURL قبل از بازگشت پاسخ
            return response()->json(['response' => $responseMessage]);
        } else {
            $errorResponse = isset($gptResponse['error']) ? $gptResponse['error'] : 'No response from ChatGPT.';
            curl_close($ch); // بسته شدن cURL قبل از بازگشت خطا
            return response()->json(['error' => $errorResponse], 500);
        }
    }



    public function show($userId)
    {
        // بازیابی پیام‌های یک کاربر خاص
        $messages = ChatMessage::where('user_id', $userId)->get();
        return view('panel.chat_details', compact('messages'));
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

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

    public function store(Request $request)
    {

        $request->validate([
            'prompt' => 'required|string',
        ]);

        $user = auth()->user();
        $prompt = $request->input('prompt');

        // اضافه کردن پیام کاربر به دیتابیس
        $userMessage = ChatMessage::create([
            'user_id' => $user->id,
            'message' => $prompt,
            'is_user_message' => true,
        ]);

        // فراخوانی API با استفاده از cURL و پراکسی
        $url = "https://api.openai.com/v1/chat/completions";
        $data = [
            "model" => "gpt-3.5-turbo",
            "messages" => [
                ["role" => "user", "content" => $prompt]
            ]
        ];

        // تنظیمات cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // تنظیم پراکسی
        curl_setopt($ch, CURLOPT_PROXY, '104.234.46.169:3128');

        // ارسال درخواست
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // بستن cURL
        curl_close($ch);

        // بررسی خطا
        if ($httpCode !== 200) {
            return response()->json(['error' => 'Error from OpenAI API: ' . $response], 500);
        }

        $result = json_decode($response, true);

        // بررسی نتیجه OpenAI API
        if (isset($result['choices']) && !empty($result['choices'][0]['message']['content'])) {
            $gptMessage = $result['choices'][0]['message']['content'];

            // ذخیره پیام GPT در دیتابیس
            ChatMessage::create([
                'user_id' => $user->id,
                'message' => $gptMessage,
                'is_user_message' => false,
            ]);

            return response()->json(['choices' => [['message' => ['content' => $gptMessage]]]]);
        } else {
            return response()->json(['error' => 'Invalid response from OpenAI API'], 500);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

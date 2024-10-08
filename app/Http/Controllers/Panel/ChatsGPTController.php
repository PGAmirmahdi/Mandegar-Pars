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
        
        // اعتبارسنجی ورودی
        $request->validate([
            'message' => 'required|string|max:1000', // اعتبارسنجی برای پیام
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

        // ارسال درخواست به ChatGPT
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'), // کلید API شما
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are ChatGPT'],
                ['role' => 'user', 'content' => $messageText],
            ],
        ]);

        // بررسی وضعیت پاسخ
        if ($response->successful()) {
            $gptResponse = $response->json();

            // اطمینان از وجود کلید choices
            if (isset($gptResponse['choices']) && count($gptResponse['choices']) > 0) {
                $responseMessage = $gptResponse['choices'][0]['message']['content'];

                // ذخیره پاسخ ChatGPT در دیتابیس
                ChatMessage::create([
                    'user_id' => $user->id,
                    'message' => $responseMessage,
                    'is_user_message' => false,
                ]);

                // ارسال پاسخ به سمت فرانت‌اند
                return response()->json(['response' => $responseMessage]);
            } else {
                // اگر کلید choices وجود ندارد
                return response()->json(['error' => 'No response from ChatGPT.'], 500);
            }
        } else {
            // اگر پاسخ از API موفقیت‌آمیز نیست
            return response()->json(['error' => 'Error from OpenAI API: ' . $response->body()], 500);
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

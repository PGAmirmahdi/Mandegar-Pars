<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        // اعتبارسنجی درخواست
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = auth()->user();
        $messageText = $request->input('message');

        // ذخیره پیام کاربر در دیتابیس
        ChatMessage::create([
            'user_id' => $user->id,
            'message' => $messageText,
            'is_user_message' => true,
        ]);

        // داده‌های JSON مورد نیاز برای ارسال به API گوگل جمینی
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $messageText]
                    ]
                ]
            ]
        ];

        // ارسال درخواست به API گوگل جمینی
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent', $data, [
            'key' => env('GOOGLE_GEMINI_API_KEY'),
        ]);

        // بررسی پاسخ API
        if ($response->failed()) {
            Log::error('API request failed', ['response' => $response->body()]);
            return response()->json(['error' => 'API request failed.'], 500);
        }

        $apiResponse = $response->json();

        if (isset($apiResponse['contents'][0]['parts'][0]['text'])) {
            $responseMessage = $apiResponse['contents'][0]['parts'][0]['text'];

            // ذخیره پیام پاسخ در دیتابیس
            ChatMessage::create([
                'user_id' => $user->id,
                'message' => $responseMessage,
                'is_user_message' => false,
            ]);

            return response()->json(['response' => $responseMessage]);
        } else {
            Log::error('No valid response from API', ['apiResponse' => $apiResponse]);
            return response()->json(['error' => 'No valid response from API.'], 500);
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

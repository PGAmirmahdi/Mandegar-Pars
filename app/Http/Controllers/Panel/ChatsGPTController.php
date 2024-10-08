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
        $this->authorize('ChatGPT-list');

        // گرفتن آخرین پیام‌های هر کاربر
        $conversations = ChatMessage::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc') // مرتب‌سازی بر اساس آخرین زمان پیام
            ->get();
        if(auth()->user()->role == 'admin'){return view('panel.ChatGPT.index', compact('conversations'));}
        else{return view('panel.ChatGPT.create', compact('conversations'));}

    }

    public function create()
    {
        $messages = ChatMessage::where('user_id', auth()->id())->get();
        return view('panel.ChatGPT', compact('messages'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $prompt = $request->input('prompt');

        // ذخیره پیام کاربر در دیتابیس
        $chatMessage = ChatMessage::create([
            'user_id' => auth()->id(),
            'message' => $request->input('message'),
            'is_user_message' => true, // مشخص کردن که پیام از سمت کاربر است
        ]);
        $chatMessage->touch(); // به‌روزرسانی updated_at
        // ارسال درخواست به ChatGPT
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'), // کلید API شما
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are ChatGPT'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        // دریافت پاسخ ChatGPT
        $gptResponse = $response->json()['choices'][0]['message']['content'];

        // ذخیره پاسخ ChatGPT در دیتابیس
        ChatMessage::create([
            'user_id' => $user->id,
            'message' => $gptResponse,
            'is_user_message' => false,
        ]);

        // ارسال پاسخ به سمت فرانت‌اند
        return response()->json(['response' => $gptResponse]);
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

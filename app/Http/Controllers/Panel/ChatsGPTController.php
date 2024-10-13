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
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = auth()->user();
        $messageText = $request->input('message');

        ChatMessage::create([
            'user_id' => $user->id,
            'message' => $messageText,
            'is_user_message' => true,
        ]);

        $data = json_encode([
            'text' => $messageText,
        ]);

        $headers = [
            'Authorization: Token ' . env('NLP_CLOUD_API_KEY'),
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.nlpcloud.io/v1/gpt-j/generate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            curl_close($ch);
            return response()->json(['error' => 'cURL error: ' . $errorMessage], 500);
        }

        $apiResponse = json_decode($response, true);

        if (isset($apiResponse['generated_text'])) {
            $responseMessage = $apiResponse['generated_text'];

            ChatMessage::create([
                'user_id' => $user->id,
                'message' => $responseMessage,
                'is_user_message' => false,
            ]);

            curl_close($ch);
            return response()->json(['response' => $responseMessage]);
        } else {
            curl_close($ch);
            return response()->json(['error' => 'No response from API.'], 500);
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

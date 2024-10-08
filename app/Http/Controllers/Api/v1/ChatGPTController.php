<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\ChatGPTService;
use Illuminate\Http\Request;

class ChatGPTController extends Controller
{
    protected $chatGPT;

    public function __construct(ChatGPTService $chatGPT)
    {
        $this->chatGPT = $chatGPT;
    }

    public function askChatGPT(Request $request)
    {
        $response = $this->chatGPT->ask($request->input('prompt'));
        return response()->json($response);
    }
}

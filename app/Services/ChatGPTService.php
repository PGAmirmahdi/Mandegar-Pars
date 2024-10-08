<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class ChatGPTService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
    }

    public function ask($prompt)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',  // می‌توانید از مدل gpt-3.5-turbo هم استفاده کنید
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 150, // تعداد کلمات مورد نیاز شما
        ]);

        return $response->json();
    }
}

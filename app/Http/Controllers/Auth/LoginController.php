<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\UserVisit;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/panel';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'phone';
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'frc-captcha-response' => 'required|string',
        ], [
            'frc-captcha-response.required' => 'کد امنیتی ضروری است.',
        ]);

        $captchaSolution = $request->input('frc-captcha-response');

        // cURL initialization
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.friendlycaptcha.com/api/v1/siteverify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . env('FRIENDLY_CAPTCHA_API_KEY'),
            'Content-Type: application/json',
        ]);

        // JSON payload for the request
        $data = json_encode([
            'solution' => $captchaSolution,
            'site_key' => env('FRIENDLY_CAPTCHA_SITEKEY'),
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, true);

        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Check for errors
        if ($httpCode !== 200) {
            return back()->withErrors(['captcha_code' => 'خطا در ارتباط با کد امنیتی.']);
        }

        // Decode the response
        $responseData = json_decode($response, true);

        if (!$responseData['success']) {
            return back()->withErrors(['captcha_code' => 'کد امنیتی وارد شده صحیح نیست']);
        }
    }

    protected function authenticated(Request $request, $user)
    {
        UserVisit::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);
    }
}

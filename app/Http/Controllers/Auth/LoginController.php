<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\UserVisit; // وارد کردن مدل UserVisit

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
            // 'captcha_code' => 'required|captcha',
        ], [
            // 'captcha_code.captcha' => 'کد امنیتی وارد شده صحیح نیست'
        ]);
    }

    /**
     * این متد پس از لاگین موفقیت‌آمیز اجرا می‌شود
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return void
     */
    protected function authenticated(Request $request, $user)
    {
        // ثبت بازدید جدید
        UserVisit::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);
    }
}

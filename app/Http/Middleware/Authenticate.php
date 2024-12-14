<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // اگر درخواست انتظار JSON دارد، به جای هدایت به صفحه ورود، پیام خطا بازگردانید
        if ($request->expectsJson()) {
            abort(401, 'Unauthorized.');
        }

        // در غیر این صورت، کاربر را به صفحه ورود هدایت کنید
        return route('login');
    }
}

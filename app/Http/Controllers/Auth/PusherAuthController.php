<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

class PusherAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        // اطمینان از احراز هویت کاربر
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true
            ]
        );

        $channelName = $request->input('channel_name');
        $socketId = $request->input('socket_id');

        if ($channelName && $socketId) {
            // احراز هویت کانال
            $auth = $pusher->socket_auth($channelName, $socketId);
            return response($auth, 200)->header('Content-Type', 'application/javascript');
        }

        return response()->json(['message' => 'Invalid request'], 400);
    }
}

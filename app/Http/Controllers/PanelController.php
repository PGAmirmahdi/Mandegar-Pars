<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    public function index()
    {
        return view('panel.index');
    }

    public function readNotification($notification = null)
    {
        if ($notification == null){
            auth()->user()->unreadNotifications->markAsRead();
            return back();
        }

        $notif = auth()->user()->unreadNotifications()->whereId($notification)->first();
        $notif->markAsRead();
        return redirect()->to($notif->data['url']);
    }
}

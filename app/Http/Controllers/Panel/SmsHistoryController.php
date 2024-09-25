<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\SmsHistory;
use Illuminate\Http\Request;

class SmsHistoryController extends Controller
{
    public function index()
    {
        $this->authorize('sms-histories');

        if (auth()->user()->isAdmin() || auth()->user()->isCEO()){
            $sms_histories = SmsHistory::latest()->paginate(30);
        }else{
            $sms_histories = SmsHistory::where('user_id', auth()->id())->latest()->paginate(30);
        }

        return view('panel.sms-histories.index', compact('sms_histories'));
    }

    public function show(SmsHistory $smsHistory)
    {
        $this->authorize('sms-histories');

        $this->authorize('show-sms-history', $smsHistory);

        return view('panel.sms-histories.show', compact('smsHistory'));
    }
}

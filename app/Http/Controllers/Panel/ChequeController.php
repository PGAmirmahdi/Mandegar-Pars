<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Cheque;
use App\Models\PriceRequest;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;


class ChequeController extends Controller
{
    public function index()
    {
        $this->authorize('cheque-check-list');

        $cheque = Cheque::latest()->paginate(30);

        return view('panel.cheque-check.index', compact('cheque'));
    }

    public function create()
    {
        $this->authorize('create-cheque-check');

        return view('panel.cheque-check.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create-cheque-check');

        $items = [];

        foreach ($request->title as $key => $title){
            $items[] = [
                'title' => $title,
                'code' => $request->code[$key],
            ];
        }

        Cheque::create([
            'user_id' => auth()->id(),
            'max_send_time' => $request->max_send_time,
            'items' => json_encode($items)
        ]);

        // notification sent to ceo
        $notifiables = User::where('id','!=',auth()->id())->whereHas('role' , function ($role) {
            $role->whereHas('permissions', function ($q) {
                $q->whereIn('name', ['ceo','sales-manager']);
            });
        })->get();

        $notif_message = 'یک درخواست وضعیت چک توسط همکار فروش ثبت گردید';
        $url = route('cheque.index');
        Notification::send($notifiables, new SendMessage($notif_message, $url));
        // end notification sent to ceo

        alert()->success('وضعیت چک با موفقیت ثبت شد','وضعیت چک ثبت شد');
        return redirect()->route('cheque.index');
    }

    public function show(Cheque $cheque)
    {
        $this->authorize('cheque-check-list');

        return view('panel.cheque-check.show', compact('cheque'));
    }
    public function edit(Cheque $cheque)
    {
        $this->authorize('edit-cheque-check');

        return view('panel.cheque-check.edit', compact('cheque'));
    }


    public function update(Request $request, Cheque $cheque)
    {
        $this->authorize('edit-cheque-check');

        $items = [];
        foreach (json_decode($cheque->items) as $key => $item){
            $items[] = [
                'title' => $item->title,
                'code' => $item->code,
                'stats' => str_replace(',','',$request->stats[$key]),
            ];
        }

        $cheque->update([
            'items' => json_encode($items),
            'status' => 'sent',
        ]);

        // notification sent to ceo
        $notifiables = User::whereHas('role' , function ($role) {
            $role->whereHas('permissions', function ($q) {
                $q->where('name', 'sales-manager');
            });
        })->get();

        $notif_message = 'وضعیت چک های درخواستی توسط مدیر ثبت گردید توسط مدیر ثبت گردید';
        $url = route('cheque.index');
        Notification::send($notifiables, new SendMessage($notif_message, $url));
        Notification::send($cheque->user, new SendMessage($notif_message, $url));
        // end notification sent to ceo

        alert()->success(' وضعیت چک ها با موفقیت ثبت شدند','وضعیت چک تغییر یافته');
        return redirect()->route('cheque.index');
    }

    public function destroy(Cheque $cheque)
    {
        $this->authorize('delete-cheque-check');

        $cheque->delete();
        return back();
    }
}

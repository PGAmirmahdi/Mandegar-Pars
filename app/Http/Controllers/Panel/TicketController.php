<?php

namespace App\Http\Controllers\Panel;

use App\Events\MessageReadEvent;
use App\Events\NewMessageEvent;
use App\Events\TypingEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Activity;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class TicketController extends Controller
{
    public function index()
    {
        $this->authorize('tickets-list');

        if (auth()->user()->isAdmin()){
            $tickets = Ticket::withCount(['messages as unread_count' => function($query) {
                $query->where('user_id', '!=', auth()->id())
                    ->whereNull('read_at');
            }])->latest()->paginate(30);
        } else {
            $tickets = Ticket::withCount(['messages as unread_count' => function($query) {
                $query->where('user_id', '!=', auth()->id())
                    ->whereNull('read_at');
            }])
                ->where(function($q) {
                    $q->where('sender_id', auth()->id())
                        ->orWhere('receiver_id', auth()->id());
                })
                ->latest()
                ->paginate(30);
        }

        return view('panel.tickets.inner-tickets.index', compact('tickets'));
    }

    public function create()
    {
        $this->authorize('tickets-create');
                                    $accountants = \App\Models\User::whereHas('role', function ($role) {
                                        $role->whereHas('permissions', function ($q) {
                                            $q->where('name', 'accountant');
                                        });
                                    })->pluck('id');
        return view('panel.tickets.inner-tickets.create', compact('accountants'));
    }

    public function store(StoreTicketRequest $request)
    {
        $this->authorize('tickets-create');

        $ticket = Ticket::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver,
            'title' => $request->title,
            'code' => $this->generateCode(),
        ]);
        $activityData = [
            'user_id' => auth()->id(),
            'description' => 'کاربر ' . auth()->user()->family  .  '(' . Auth::user()->role->label . ')' . ' تیکتی به کاربر ' . $ticket->receiver->family . " با عنوان " . $ticket->title . ' ایجاد کرد',
            'action' => 'ارسال تیکت',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        if ($request->file) {
            $file_info = [
                'name' => $request->file('file')->getClientOriginalName(),
                'type' => $request->file('file')->getClientOriginalExtension(),
                'size' => $request->file('file')->getSize(),
            ];

            $file = upload_file($request->file, 'Messages');
            $file_info['path'] = $file; // اطمینان حاصل کنید که فایل آپلود شده به درستی ذخیره می‌شود
        }

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'text' => $request->text,
            'file' => isset($file) ? json_encode($file_info) : null,
        ]);

        $title='تیکت';
        $message = 'تیکتی با عنوان "' . $ticket->title . '" به شما ارسال شده است';
        $url = route('tickets.edit', $ticket->id);

        // اطمینان حاصل کنید که $url یک رشته است و به درستی به SendMessage ارسال می‌شود
        Notification::send($ticket->receiver, new SendMessage($title,$message, $url));



        return redirect()->route('tickets.edit', $ticket->id);
    }


    public function show(Ticket $ticket)
    {
        //
    }

//    صفحه چت
    public function edit(Ticket $ticket)
    {
        $this->authorize('tickets-create');

        $updated = $ticket->messages()
            ->whereNull('read_at')
            ->where('user_id','!=',auth()->id())
            ->update(['read_at' => now()]);

        $readMessages = $ticket->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNotNull('read_at')
            ->pluck('id')
            ->toArray();
        if ($updated && !empty($readMessages)) {
            event(new MessageReadEvent($ticket->id, $readMessages));
        }

        return view('panel.tickets.inner-tickets.edit', compact('ticket'));
    }

//  گفت و گو
    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('tickets-create');

        // به‌روزرسانی وضعیت تیکت
        $ticket->update(['status' => 'pending', 'updated_at' => now()]);
        $updated = $ticket->messages()
            ->whereNull('read_at')
            ->where('user_id','!=',auth()->id())
            ->update(['read_at' => now()]);

        $readMessages = $ticket->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNotNull('read_at')
            ->pluck('id')
            ->toArray();
        if ($updated && !empty($readMessages)) {
            event(new MessageReadEvent($ticket->id, $readMessages));
        }
        // ارسال نوتیفیکیشن به طرف مقابل (در صورت نیاز)
        $first_message = $ticket->messages()->orderBy('created_at', 'desc')->first();
        if ($first_message !== null && $first_message->user_id != auth()->id()) {
            $title = 'تیکت';
            $messageNotification = 'پاسخی برای تیکت "' . $ticket->title . '" ثبت شده است';
            $url = route('tickets.edit', $ticket->id);
            $receiver = auth()->id() == $ticket->sender_id ? $ticket->receiver : $ticket->sender;
            Notification::send($receiver, new SendMessage($title, $messageNotification, $url));
        }

        // پردازش فایل در صورت وجود
        if ($request->hasFile('file')) {
            $file_info = [
                'name' => $request->file('file')->getClientOriginalName(),
                'type' => $request->file('file')->getClientOriginalExtension(),
                'size' => $request->file('file')->getSize(),
            ];
            $file = upload_file($request->file, 'Messages');
            $file_info['path'] = $file;
        }
        // ایجاد پیام جدید
        $message = $ticket->messages()->create([
            'user_id' => auth()->id(),
            'text'    => $request->text,
            'file'    => isset($file) ? json_encode($file_info) : null,
        ]);
        broadcast(new NewMessageEvent($message))->toOthers();

        $activityData = [
            'user_id'     => auth()->id(),
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') پاسخی به تیکت "' . $ticket->title . '" ارسال کرد',
            'action'      => 'پاسخ به تیکت',
            'created_at'  => now(),
        ];
        Activity::create($activityData);
        if ($request->expectsJson()) {
            $receiverId = auth()->id();
            $message_html = view('panel.tickets.inner-tickets.single-message', compact('message'))->render();
            return response()->json(['message_html' => $message_html, 'receiverId' => $receiverId]);
        }
        return back();
    }
//    دریافت پیام ها
//    public function getNewMessages(Ticket $ticket)
//    {
//        $lastMessageTime = session('last_message_time', now());
//
//        // فقط پیام‌هایی که از طرف سایر کاربران ارسال شده‌اند دریافت می‌شود
//        $newMessages = $ticket->messages()
//            ->where('user_id', '!=', auth()->id())
//            ->where('created_at', '>', $lastMessageTime)
//            ->get();
//
//        // به‌روزرسانی وضعیت خوانده شدن برای پیام‌های دریافتی
//        $ticket->messages()
//            ->whereIn('id', $newMessages->pluck('id'))
//            ->where('user_id', '!=', auth()->id())
//            ->whereNull('read_at')
//            ->update(['read_at' => now()]);
//
//        session(['last_message_time' => now()]);
//
//        if ($newMessages->isEmpty()) {
//            return response()->json(['new_messages' => '']);
//        }
//
//        $messagesHtml = '';
//        foreach ($newMessages as $message) {
//            $messagesHtml .= view('panel.tickets.inner-tickets.single-message', compact('message'))->render();
//        }
//
//        return response()->json(['new_messages' => $messagesHtml]);
//    }
    public function typing(Request $request)
    {
        // dispatch event with ticket_id and current user id
        event(new TypingEvent($request->ticket_id, auth()->id()));
        return response()->json(['status' => 'ok']);
    }
    public function destroy(Ticket $ticket)
    {
        $this->authorize('tickets-delete');

        foreach ($ticket->messages as $message){
            if ($message->file){
                unlink(public_path(json_decode($message->file)->path));
            }
        }
// ذخیره فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') تیکت با عنوان "' . $ticket->title . '" را حذف کرد',
            'action' => 'حذف تیکت',
            'created_at' => now(),
        ];
        Activity::create($activityData);

        $ticket->delete();
        return back();
    }
    public function changeStatus(Ticket $ticket)
    {
        if ($ticket->sender_id == auth()->id() || $ticket->receiver_id == auth()->id()){
            if ($ticket->status == 'closed'){
                $ticket->update(['status' => 'pending']);
            }else{
                $ticket->update(['status' => 'closed']);
            }

            // send notif
            $status = Ticket::STATUS[$ticket->status];
            $title='تیکت';
            $message = "وضعیت تیکت '$ticket->title' به '$status' تغییر یافت";
            $url = route('tickets.index');
            $receiver = auth()->id() == $ticket->sender_id ? $ticket->receiver : $ticket->sender;
            Notification::send($receiver, new SendMessage($title,$message, $url));
            // end send notif

            // ذخیره فعالیت
            $activityData = [
                'user_id' => auth()->id(),
                'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') وضعیت تیکت "' . $ticket->title . '" را به "' . $status . '" تغییر داد',
                'action' => 'تغییر وضعیت تیکت',
                'created_at' => now(),
            ];
            Activity::create($activityData);

            alert()->success('وضعیت تیکت با موفقیت تغییر یافت','تغییر وضعیت');
            return back();

        }else{
            abort(403);
        }
    }

    private function generateCode()
    {
        $last_ticket = Ticket::latest()->first();
        $newCode = $last_ticket->code++;

        if ($last_ticket){
            while (Ticket::where('code', $newCode)->exists()) {
                $newCode++;
            }
            return $newCode;
        }else{
            $year = verta()->year;
            $month = verta()->month;
            $day = verta()->day;
            $hour = verta()->hour;
            $minute = verta()->minute;
            $second = verta()->second;

            $code = $year.$month.$day.$hour.$minute.$second;
            return $code;
        }
    }
}

<?php

namespace App\Http\Controllers\Panel;

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
use Pusher\Pusher;

class TicketController extends Controller
{
    public function index()
    {
        $this->authorize('tickets-list');

        if (auth()->user()->isAdmin()){
            $tickets = Ticket::latest()->paginate(30);
        }else{
            $tickets = Ticket::where('sender_id', auth()->id())->orWhere('receiver_id', auth()->id())
                ->latest()->paginate(30);
        }

        return view('panel.tickets.index', compact('tickets'));
    }

    public function create()
    {
        $this->authorize('tickets-create');

        return view('panel.tickets.create');
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

//        $message = 'تیکتی با عنوان "' . $ticket->title . '" به شما ارسال شده است';
//        $url = route('tickets.edit', $ticket->id);

        // اطمینان حاصل کنید که $url یک رشته است و به درستی به SendMessage ارسال می‌شود
//        Notification::send($ticket->receiver, new SendMessage($message, $url));



        return redirect()->route('tickets.edit', $ticket->id);
    }



    public function show(Ticket $ticket)
    {
        //
    }

    public function edit(Ticket $ticket)
    {
        $this->authorize('tickets-create');

        $ticket->messages()->whereNull('read_at')->where('user_id','!=',auth()->id())->update(['read_at' => now()]);

        return view('panel.tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('tickets-create');

        $ticket->update(['status' => 'pending']);

        // prevent from send sequence notification
        $first_message = $ticket->messages()->orderBy('created_at', 'desc')->first();
//        if ($first_message != null && $first_message->user_id != auth()->id()){
//            $message = 'پاسخی برای تیکت "'.$ticket->title.'" ثبت شده است';
//            $url = route('tickets.edit', $ticket->id);
//            $receiver = auth()->id() == $ticket->sender_id ? $ticket->receiver : $ticket->sender;
//            Notification::send($receiver, new SendMessage($message, $url));
//        }
        // end prevent from send sequence notification

        if ($request->file){
            $file_info = [
                'name' => $request->file('file')->getClientOriginalName(),
                'type' => $request->file('file')->getClientOriginalExtension(),
                'size' => $request->file('file')->getSize(),
            ];

            $file = upload_file($request->file, 'Messages');

            $file_info['path'] = $file;
        }

        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'text' => $request->text,
            'file' => isset($file) ? json_encode($file_info) : null,
        ]);
// ذخیره فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') پاسخی به تیکت "' . $ticket->title . '" ارسال کرد',
            'action' => 'پاسخ به تیکت',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        return back();
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
            $message = "وضعیت تیکت '$ticket->title' به '$status' تغییر یافت";
            $url = route('tickets.index');
            $receiver = auth()->id() == $ticket->sender_id ? $ticket->receiver : $ticket->sender;
//            Notification::send($receiver, new SendMessage($message, $url));
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

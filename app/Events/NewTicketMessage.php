<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTicketMessage
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $message_html;

    public function __construct(Message $message)
    {
        $this->message = $message;
        // رندر کردن ویو پیام تک تک جهت ارسال به کلاینت
        $this->message_html = view('panel.tickets.inner-tickets.single-message', ['message' => $message])->render();
    }

    public function broadcastOn()
    {
        // اطمینان حاصل کنید که مدل Message دارای فیلد ticket_id هست
        return new Channel('ticket.' . $this->message->ticket_id);
    }

    public function broadcastWith()
    {
        return [
            'id'           => $this->message->id,
            'text'         => $this->message->text,
            'file'         => $this->message->file,
            'user_id'      => $this->message->user_id,
            'created_at'   => $this->message->created_at->toDateTimeString(),
            'message_html' => $this->message_html,
        ];
    }
}

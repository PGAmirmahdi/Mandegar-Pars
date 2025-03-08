<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * ایجاد ایونت جدید برای پیام
     *
     * @param  \App\Models\Message  $message
     * @return void
     */
    public function __construct($message)
    {
        $this->message =$message;

    }

    /**
     * داده‌هایی که هنگام broadcast ارسال می‌شوند
     */
    public function broadcastWith()
    {
        $formattedDate = verta($this->message->created_at)->format('H:i - Y/m/d');
        $message_html = view('panel.tickets.inner-tickets.single-message', [
            'message' => $this->message
        ])->render();

        return [
            'message_html'    => $message_html,
            'message'         => $this->message,
            'formatted_date'  => $formattedDate,
        ];
    }



    /**
     * تعیین کانال انتشار بر اساس ticket_id پیام
     */
    public function broadcastOn()
    {
        return new Channel('ticket.' . $this->message->ticket_id);
    }

    /**
     * (اختیاری) نام سفارشی ایونت برای سمت کلاینت
     */
    public function broadcastAs()
    {
        return 'NewMessageEvent';
    }
}

<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * ایجاد نمونه جدید از رویداد.
     *
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * کانال‌هایی که رویداد در آن‌ها پخش می‌شود.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('ticket.' . $this->message->ticket_id);
    }

    /**
     * نام رویداد broadcast شده که در سمت کلاینت گوش داده می‌شود.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'NewMessageEvent';
    }

    /**
     * داده‌های ارسال شده همراه رویداد.
     *
     * @return array
     */
    public function broadcastWith()
    {
        // به عنوان نمونه، HTML رندر شده پیام را ارسال می‌کنیم.
        // توجه داشته باشید که برای این کار باید view مربوط به پیام (panel.partials.message) را ایجاد کنید.
        $html = view('panel.tickets.inner-tickets.single-message', ['message' => $this->message])->render();

        return [
            'message' => [
                'id'   => $this->message->id,
                'html' => $html,
            ],
        ];
    }
}

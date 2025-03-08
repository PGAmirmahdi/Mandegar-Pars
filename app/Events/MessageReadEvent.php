<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReadEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticketId;
    public $readMessages;

    public function __construct($ticketId, $readMessages)
    {
        $this->ticketId = $ticketId;
        $this->readMessages = $readMessages; // آرایه‌ای از آی‌دی پیام‌های خوانده‌شده
    }

    public function broadcastOn()
    {
        return new Channel('ticket.' . $this->ticketId);
    }

    public function broadcastWith()
    {
        return [
            'read_messages' => $this->readMessages,
        ];
    }

    public function broadcastAs()
    {
        return 'MessageReadEvent';
    }
}

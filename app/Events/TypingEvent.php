<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TypingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket_id;
    public $user_id;

    public function __construct($ticket_id, $user_id)
    {
        $this->ticket_id = $ticket_id;
        $this->user_id = $user_id;
    }

    public function broadcastOn()
    {
        return new Channel('ticket.' . $this->ticket_id);
    }

    public function broadcastAs()
    {
        return 'TypingEvent';
    }
}

<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $data;

    /**
     * Create a new event instance.
     *
     * @param int $userId
     * @param array $data
     */
    public function __construct($userId, $data)
    {
        $this->userId = $userId;
        $this->data = $data;
    }


    public function broadcastOn()
    {
        // استفاده از PrivateChannel برای ارسال به کاربر خاص
        return new PrivateChannel('notification.'.$this->userId);
    }
}

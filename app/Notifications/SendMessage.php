<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Events\SendMessage as SendMessageEvent;

class SendMessage extends Notification
{
    use Queueable;

    private $message;
    private $url;

    public function __construct(string $message, string $url)
    {
        $this->message = $message;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $data = [
            'id' => $this->id,
            'message' => $this->message,
            'url' => $this->url,
        ];

        event(new SendMessageEvent($notifiable->id, $this->message, $this->url));

        return $data;
    }
}


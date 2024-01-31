<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Events\SendMessage as SendMessageEvent;
use Illuminate\Support\Facades\Log;

class SendMessage extends Notification
{
    use Queueable;

    private $message;
    private $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $message, string $url)
    {
        $this->message = $message;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $data = [
            'id' => $this->id,
            'message' => $this->message,
            'url' => $this->url,
        ];

        if ($notifiable->najva_token){
            $this->send_najva_notificaion($this->message, $this->url, $notifiable->najva_token);
        }

        event(new SendMessageEvent($notifiable->id, $data));

        return $data;
    }

    private function send_najva_notificaion($message, $url, $token)
    {
        // najva push notification
        $data = [
            "title" => $this->message,
            "body" => ".",
            "url" => $this->url,
            "icon" => "https://mpsystem.ir/assets/media/image/logo.png",
            "utm" => [],
            "light_up_screen" => false,
            "sent_time" => now()->addSeconds(3)->format("Y-m-d\TH:i:s"),
            "included_segments" => [],
            "excluded_segments" => [],
            "buttons" => [],
            "subscribers" => [
                $token
            ]
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://app.najva.com/api/v2/notification/management/send-campaign/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $headers = array();
        $headers[] = 'Authorization: Token f565da417ab6ef8ec57bab4a2a090955d5ee227e';
        $headers[] = 'X-Api-Key: 1faec3c1-6f27-4881-b219-5f5b5737f31b';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        // najva push notification
    }
}

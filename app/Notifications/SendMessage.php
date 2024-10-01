<?php

namespace App\Notifications;

use App\Models\User;
use Exception;
use GuzzleHttp\Client;
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

        // ارسال نوتیفیکیشن به Firebase
        if ($notifiable->fcm_token){
            $this->send_firebase_notification($this->message, $this->url, $notifiable->fcm_token);
        }

        // ارسال نوتیفیکیشن به Najva
        if ($notifiable->najva_token) {  // فرض بر این است که `najva_token` در مدل User وجود دارد
            $this->send_najva_notification($this->message, $this->url, $notifiable->najva_token);
        }

        // ارسال رویداد به Pusher
        event(new SendMessageEvent($notifiable->id, $data));

        return $data;
    }


    private function send_firebase_notification($message, $url, $token)
    {
        $firebaseToken = [$token];

        $SERVER_API_KEY = 'AAAAAqqjtGY:APA91bGqBtuYddBnAnliS0HOL1PBuf8cbWgdkNWMpOJCMFuWPVq2nCZoLTZIcxDQMJf8OwAsWRYYan5BpXC6qFdoIpyWW91OCUOu-eDOggSmBv-Oi5ebT2FWdSRid7OV1iP02_9rGftS';

        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $message,
                "body" => '',
                "priority" => "high",
            ],
            "webpush" => [
                "headers" => [
                    "image" => "https://mpsystem.ir/assets/media/image/logo.png",
                ]
            ]
        ];

        try {
            $client = new Client();
            $response = $client->post('https://fcm.googleapis.com/fcm/send', [
                'headers' => [
                    'Authorization' => 'key=' . $SERVER_API_KEY,
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
            ]);
            $body = $response->getBody();
            Log::info('Firebase notification response: ' . $body);
        } catch (Exception $e) {
            Log::error('Error sending Firebase notification: ' . $e->getMessage());
        }
    }
    private function send_najva_notification($message, $url, $token)
    {
        $data = [
            "title" => $message,
            "body" => ".",
            "url" => $url,
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

        try {
            $client = new Client();
            $response = $client->post('https://app.najva.com/api/v2/notification/management/send-campaign/', [
                'headers' => [
                    'Authorization' => 'Token f565da417ab6ef8ec57bab4a2a090955d5ee227e',
                    'X-Api-Key' => '1faec3c1-6f27-4881-b219-5f5b5737f31b',
                    'Cache-Control' => 'no-cache',
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
            ]);
            $body = $response->getBody();
            Log::info('Najva notification response: ' . $body);
        } catch (Exception $e) {
            Log::error('Error sending Najva notification: ' . $e->getMessage());
        }
    }

}

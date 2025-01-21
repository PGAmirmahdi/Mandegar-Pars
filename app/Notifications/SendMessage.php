<?php

namespace App\Notifications;

use App\Models\User;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Events\SendMessage as SendMessageEvent;
use Illuminate\Support\Facades\Log;
use Google\Auth\HttpHandler\HttpHandlerFactory;

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
    public function __construct(string $title,string $message, string $url)
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {

        $data = [
            'id' => $this->id,
            'title'=>$this->title,
            'message' => $this->message,
            'url' => $this->url,
        ];

        if ($notifiable->fcm_token) {
            $this->send_firebase_notification($this->title,$this->message, $this->url, $notifiable->fcm_token);
        }

        event(new SendMessageEvent($notifiable->id, $data));

        return $data;
    }

    // the new method
    private function send_firebase_notification($title,$message, $url, $firebaseToken)
    {

        $credential = new ServiceAccountCredentials(
                "https://www.googleapis.com/auth/firebase.messaging",
            json_decode(file_get_contents(public_path('firebase-private-key.json')), true)
        );
        $token = $credential->fetchAuthToken(HttpHandlerFactory::build());
        $ch = curl_init("https://fcm.googleapis.com/v1/projects/mandegarpars2-9e7d9/messages:send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['access_token']
        ]);

        $payload = [
            "message" => [
                "token" => $firebaseToken,
                "webpush" => [
                    "notification" => [
                        "title" => $title,
                        "body" => $message,
                        "icon" => asset('/assets/media/image/logo.png')
                    ],
                ]
            ]
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "post");
        curl_exec($ch);
        curl_close($ch);

    }

    // the old method
//    private function send_firebase_notification($message, $url, $token)
//    {
//        $firebaseToken = [$token];
//
//        $SERVER_API_KEY = 'AAAAAqqjtGY:APA91bGqBtuYddBnAnliS0HOL1PBuf8cbWgdkNWMpOJCMFuWPVq2nCZoLTZIcxDQMJf8OwAsWRYYan5BpXC6qFdoIpyWW91OCUOu-eDOggSmBv-Oi5ebT2FWdSRid7OV1iP02_9rGftS';
//
//        $data = [
//            "registration_ids" => $firebaseToken,
//            "notification" => [
//                "title" => $message,
//                "body" => '',
////                "image" => 'https://mpsystem.ir/assets/media/image/logo.png',
////                "content_available" => true,
//                "priority" => "high",
//            ],
//            "webpush" => [
//                "headers" => [
//                    "image" => "https://mpsystem.ir/assets/media/image/logo.png",
//                ]
//            ]
//        ];
//        $dataString = json_encode($data);
//
//        $headers = [
//            'Authorization: key=' . $SERVER_API_KEY,
//            'Content-Type: application/json',
//        ];
//
//        $ch = curl_init();
//
//        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
//
//        $response = curl_exec($ch);
//    }
}

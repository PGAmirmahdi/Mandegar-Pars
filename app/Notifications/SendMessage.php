<?php

namespace App\Notifications;

use App\Models\User;
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
        Log::info("Notification SendMessage triggered.", [
            'notifiable_id' => $notifiable->id,
        ]);
        $data = [
            'id' => $this->id,
            'message' => $this->message,
            'url' => $this->url,
        ];

        if ($notifiable->fcm_token) {
            $this->send_firebase_notification($this->message, $this->url, $notifiable->fcm_token);
        }

        // ارسال فقط پیام به رویداد
        event(new SendMessage($notifiable->id, $this->message));

        return $data;
    }


    private function send_firebase_notification($message, $url, $token)
    {
        $firebaseToken = [$token];
        $SERVER_API_KEY = env('FireBase_Key');

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

        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        try {
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($ch, CURLOPT_PROXY, "181.41.194.86:3128"); // آدرس و پورت پروکسی
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, "username:password"); // اگر پروکسی نیاز به احراز هویت دارد

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                // لاگ کردن خطاهای CURL
                Log::error('CURL Error: ' . curl_error($ch));
            } else {
                $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($httpStatusCode !== 200) {
                    // لاگ کردن وضعیت HTTP غیرموفق
                    Log::error("FCM Notification failed with status code: $httpStatusCode", [
                        'response' => $response,
                        'data' => $dataString,
                    ]);
                } else {
                    Log::info("FCM Notification sent successfully", [
                        'response' => $response,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // لاگ کردن استثناها
            Log::error('Exception while sending FCM Notification: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        } finally {
            curl_close($ch);
        }
    }
    private function send_najva_notificaion($message, $url, $token)
    {
        // najva push notification
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

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://app.najva.com/api/v2/notification/management/send-campaign/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_PROXY, "181.41.194.86:3128"); // آدرس و پورت پروکسی

        $headers = array();
        $headers[] = 'Authorization: Token f565da417ab6ef8ec57bab4a2a090955d5ee227e';
        $headers[] = 'X-Api-Key: 1faec3c1-6f27-4881-b219-5f5b5737f31b';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('CURL Error: ' . curl_error($ch));
            return 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        Log::info($result);
        // najva push notification
    }
}

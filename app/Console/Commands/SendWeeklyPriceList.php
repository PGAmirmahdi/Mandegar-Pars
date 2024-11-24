<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\PriceHistory;
use App\Models\Whatsapp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWeeklyPriceList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:weekly-price-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(){
        $url = 'https://wesender.ir/Send';
        $sender = env('WESENDER_SENDER');
        $key = env('WESENDER_KEY');
        $dateAdd = 1;

        $groupId = 'GdDNQHQoVAMJ5pX29jV0f1';

//        // دریافت لیست قیمت‌ها
        $priceHistories = PriceHistory::with('product')->distinct()->get();

        // محصولات مورد نظر برای ارسال
        $targetProducts = [
            'کارتریج تونر 80X برند آرتین تونر',
            'کارتریج تونر 49A برند آرتین تونر',
            'کارتریج تونر 83A برند آرتین تونر',
            'کارتریج تونر 59A برند آرتین تونر',
            'کارتریج تونر 26X برند آرتین تونر',
            'کارتریج تونر 26A برند آرتین تونر',
            'کارتریج تونر 05A برند آرتین تونر',
            'کارتریج تونر 85A برند آرتین تونر',
            'کارتریج تونر 78A برند آرتین تونر',
        ];

            $message = "صبح بخیر مشتریان عزیز،\n";
            $message .= "لیست قیمت امروز ماندگار پارس به شرح زیر میباشد:\n";

            $sentProducts = [];
            Log::info("Message: {$message}");

            foreach ($priceHistories as $price) {
                // فیلتر کردن بر اساس نام محصول و اطمینان از اینکه قیمت بازار بیشتر از صفر است
                if (in_array($price->product->title, $targetProducts) && $price->product->market_price > 0) {
                    if (!in_array($price->product->title, $sentProducts)) {
                        $formattedPrice = number_format($price->product->market_price); // جدا کردن سه رقم، سه رقم
                        $message .= " محصول: {$price->product->title}\n";
                        $message .= " قیمت بازار: {$formattedPrice} ريال \n";

                        // اضافه کردن محصول به لیست ارسال شده‌ها
                        $sentProducts[] = $price->product->title;
                    }
                }
            }

            Log::info("Complete message: {$message}");

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'message' => $message,
                'dateAdd' => $dateAdd,
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "groupId: $groupId",
                "sender: $sender",
                "key: $key",
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        $status = $error ? 'failed' : 'successful';

        // ذخیره اطلاعات پیام در مدل Whatsapp
        Whatsapp::create([
            'user_id' => 173,
            'sender_name' =>  'سیستم',
            'receiver_name' => 'گروه',  // نام گیرنده به عنوان گروه
            'phone' => $groupId,
            'description' => $message,
            'status' => $status,
        ]);

        if ($error) {
            Log::error("Failed to send message: $error");
            return Command::FAILURE;
        }

        Log::info("Message sent successfully.");
        return Command::SUCCESS;

    }
}

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
    public function handle()
    {
        $url = 'https://wesender.ir/Send';
        $sender = env('WESENDER_SENDER');
        $key = env('WESENDER_KEY');
        $dateAdd = 1;

        // دریافت لیست مشتریان
        $customers = Customer::all();

        // دریافت لیست قیمت‌ها
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

        foreach ($customers as $customer) {
            $message = "صبح بخیر مشتری عزیز {$customer->name}،\n";
            $message .= "لیست قیمت این هفته ماندگار پارس به شرح زیر میباشد:\n";

            $sentProducts = [];
            Log::info("Message for customer {$customer->name}: {$message}");

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

            Log::info("Complete message for customer {$customer->name}: {$message}");

            // اضافه کردن 98 به ابتدای هر شماره
            $formattedPhone = '98' . ltrim($customer->phone1, '0');

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
                    "receivers: $formattedPhone",
                    "sender: $sender",
                    "key: $key",
                ],
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            // لاگ کردن پاسخ cURL و خطا (در صورت وجود)
            Log::info("cURL response for customer {$customer->name}: " . ($response ? $response : 'No response'));
            Log::info("cURL error for customer {$customer->name}: " . ($error ? $error : 'No error'));

            $status = $error ? 'failed' : 'successful';
            Log::info("Sending message to {$customer->name} with phone {$customer->phone1}");

            // ذخیره وضعیت ارسال پیام
            Whatsapp::create([
                'user_id' => 173,
                'sender_name' => 'سیستم',
                'receiver_name' => $customer->name,
                'phone' => $customer->phone1,
                'description' => $message,
                'status' => $status,
            ]);
        }
    }
}

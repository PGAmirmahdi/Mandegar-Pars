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

        $groupId = 'KIShrprMf7x59CGPLDW5Qk';

        // محصولات مورد نظر برای ارسال
        $targetProducts = [
            'کارتریج اچ پی مدل مشکی Toner-Cartridge-80X | 80X',
            'کارتریج اچ پی مدل مشکی Toner-Cartridge-49A | 49A',
            'کارتریج اچ پی مدل مشکی Toner-Cartridge-83A | 83A',
            'کارتریج اچ پی مدل مشکی Toner-Cartridge-59A | 59A',
            'کارتریج اچ پی مدل مشکی Toner-Cartridge-26X | 26X',
            'کارتریج اچ پی مدل مشکی Toner-Cartridge-26A | 26A',
            'کارتریج اچ پی مدل مشکی Toner-Cartridge-05A | 05A',
            'کارتریج اچ پی مدل مشکی Toner-Cartridge-85A | 85A',
            'کارتریج اچ پی مدل مشکی Toner-Cartridge-78A | 78A',
        ];

        // واکشی لیست قیمت‌ها
        $priceHistories = PriceHistory::with('product')->distinct()->get();

        if ($priceHistories->isEmpty()) {
            Log::warning("No price histories found.");
            return Command::FAILURE;
        }

        Log::info("Fetched price histories count: " . $priceHistories->count());

        $message = "صبح بخیر مشتریان عزیز،\n";
        $message .= "لیست قیمت امروز ماندگار پارس به شرح زیر میباشد:\n";

        $sentProducts = [];
        foreach ($priceHistories as $price) {
            // بررسی وجود محصول
            if (!$price->product) {
                Log::warning("Price history ID {$price->id} does not have an associated product.");
                continue;
            }

            Log::info("Checking product: {$price->product->title}");

            // فیلتر کردن محصولات هدف
            if (in_array($price->product->title, $targetProducts) && $price->product->market_price > 0) {
                if (!in_array($price->product->title, $sentProducts)) {
                    $formattedPrice = number_format($price->product->market_price); // جدا کردن سه رقم، سه رقم
                    $message .= " محصول: {$price->product->title}\n";
                    $message .= " قیمت بازار: {$formattedPrice} ريال \n";

                    // اضافه کردن محصول به لیست ارسال شده‌ها
                    $sentProducts[] = $price->product->title;

                    Log::info("Added product to message: {$price->product->title}");
                }
            } else {
                Log::info("Product not in target or market price <= 0: {$price->product->title}, Market Price: {$price->product->market_price}");
            }
        }

        if (empty($sentProducts)) {
            Log::warning("No target products found with valid market prices.");
            return Command::FAILURE;
        }

        Log::info("Complete message: {$message}");

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'message' => $message . "شماره تماس:02165425052\n",
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

        Whatsapp::create([
            'user_id' => 173,
            'sender_name' => 'سیستم',
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

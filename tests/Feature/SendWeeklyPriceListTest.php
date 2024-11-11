<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SendWeeklyPriceListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_send_weekly_price_list_command()
    {
        // اجرای کامند
        Artisan::call('send:weekly-price-list');

        // چک کردن نتایج
        $this->assertDatabaseHas('whatsapps', [
            'status' => 'successful',
        ]);

        // بررسی پیام‌ها برای همه مشتریان
        $customers = Customer::all();
        foreach ($customers as $customer) {
            $this->assertDatabaseHas('whatsapps', [
                'receiver_name' => $customer->name,
                'phone' => $customer->phone1,
            ]);
        }
    }
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

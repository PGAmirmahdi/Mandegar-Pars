<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PDO;

class ArtinController extends Controller
{
    public function products()
    {
        $this->authorize('artin-products-list');

        $response = Http::withHeaders([
            'x-api-key' => env('ARTIN_API_KEY'),
        ])->get('https://artintoner.com/wp-json/custom-api/v1/products');

        $products = $response->object()->products;

        return view('panel.artin.products', compact('products'));
    }


    public function updatePrice(Request $request)
    {
        $this->authorize('artin-products-edit');

        $product_id = $request->product_id;
        $price = $request->price;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://artintoner.com/wp-json/custom-api/v1/update-price');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "product_id=$product_id&price=$price");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key:' . env('ARTIN_API_KEY'),
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        // ثبت فعالیت کاربر
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ویرایش قیمت محصول سایت',
            'description' => 'کاربر ' . auth()->user()->family . ' قیمت محصول "' . $product_id . '" را ویرایش کرد.',
        ]);

        $price_text = number_format($price) . ' تومان';
        return response()->json([
            'success' => 'قیمت محصول با موفقیت به‌روزرسانی شد.',
            'price' => $price,
            'price_text' => $price_text,
            'product_id' => $product_id
        ]);
    }
    public function site()
    {
        $wpApiUrl = 'http://artintoner.com/wp-json/my-api/v1/stats';

        // ارسال درخواست GET به API وردپرس
        $response = Http::get($wpApiUrl);
        $products = Http::withHeaders([ 'x-api-key' => env('ARTIN_API_KEY')])->get('https://artintoner.com/wp-json/custom-api/v1/products')->object()->products;
        if ($response->successful()) {
            $data = $response->json();
            // انتقال داده‌ها به view به نام wp-stats
            return view('panel.artin.site', compact('data','products'));
        } else {
            return "خطا در دریافت اطلاعات از وردپرس.";
        }
    }

    public function orders()
    {
        $wpApiUrl = 'http://artintoner.com/wp-json/my-api/v1/stats';

        // ارسال درخواست GET به API وردپرس
        $response = Http::get($wpApiUrl);
        if ($response->successful()) {
            $data = $response->json();
            // انتقال داده‌ها به view به نام wp-stats
            return view('panel.artin.orders', compact('data'));
        } else {
            return "خطا در دریافت اطلاعات از وردپرس.";
        }

    }

    public function registered()
    {
        $wpApiUrl = 'http://artintoner.com/wp-json/my-api/v1/stats';

        // ارسال درخواست GET به API وردپرس
        $response = Http::get($wpApiUrl);
        if ($response->successful()) {
            $data = $response->json();
            // انتقال داده‌ها به view به نام wp-stats
            return view('panel.artin.registered', compact('data'));
        } else {
            return "خطا در دریافت اطلاعات از وردپرس.";
        }
    }
}

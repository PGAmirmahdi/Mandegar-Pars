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

        $products = Http::get('https://artintoner.com/wp-json/custom-api/v1/products')->object()->products;

        return view('panel.artin.products', compact('products'));
    }

    public function updatePrice(Request $request)
    {
        $this->authorize('artin-products-edit');

        $product_id = $request->product_id;
        $price = $request->price;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://artintoner.com/wp-json/custom-api/v1/update-price');
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, "product_id=$product_id&price=$price");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

//        dd($result);

        // ثبت فعالیت کاربر
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ویرایش قیمت محصول سایت',
            'description' => 'کاربر ' . auth()->user()->family . ' قیمت محصول "' . $product_id . '" را ویرایش کرد.',
        ]);

        $price_text = number_format($price) . ' تومان';
        return response()->json(['success' => 'قیمت محصول با موفقیت به‌روزرسانی شد.', 'price' => $price, 'price_text' => $price_text, 'product_id' => $product_id]);

    }
}

<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class PriceController extends Controller
{
    public function index()
    {
        $this->authorize('prices-list');

        return view('panel.prices.list');
    }

    public function otherList()
    {
        $this->authorize('prices-list');

        if (auth()->user()->isCEO() || auth()->user()->isAdmin() || auth()->user()->isOrgan()){
            return view('panel.prices.other-list');
        }else{
            return view('panel.prices.other-list-printable');
        }
    }

    public function updatePrice(Request $request)
    {
        $this->authorize('prices-list');

        $items = json_decode($request->items, true);
        foreach ($items as $item) {
            $price = trim(str_replace('-',null,$item['price']));
            $price = trim(str_replace(',',null,$price));
            $price = $price == '' ? null : $price;

            if ($price){
                DB::table('price_list')->where([
                    'seller_id' => $item['seller_id'],
                    'model_id' => $item['model_id']
                ])->updateOrInsert([
                    'seller_id' => $item['seller_id'],
                    'model_id' => $item['model_id'],
                ],[
                    'seller_id' => $item['seller_id'],
                    'model_id' => $item['model_id'],
                    'price' => $price
                ]);
            }else{
                DB::table('price_list')->where(['seller_id' => $item['seller_id'], 'model_id' => $item['model_id']])->delete();
            }
        }

        return 'ok';
    }
    public function updatePrice2(Request $request)
    {
        $this->authorize('price-list-mandegar');

        $items = json_decode($request->items, true);

        foreach ($items as $item) {
            $price = trim(str_replace(['-', ','], '', $item['price']));

            // در نظر گرفتن مقدار 0 به عنوان مقدار معتبر
            $price = is_numeric($price) ? (float)$price : null;

            // به‌روزرسانی قیمت‌ها در جدول محصولات
            DB::table('products')->where('id', $item['id'])->update([$item['field'] => $price]);

            // ذخیره تغییرات در تاریخچه قیمت‌ها
            \App\Models\PriceHistory::updateOrCreate(
                [
                    'product_id' => $item['id'],
                    'price_field' => $item['field'],
                ],
                [
                    'price_amount_from' => $price,  // تغییر این قسمت با توجه به نیاز
                    'price_amount_to' => $price,    // تغییر این قسمت با توجه به نیاز
                ]
            );
        }

        return response()->json(['status' => 'ok']);
    }


    public function priceList($type)
    {
        $this->authorize('prices-list');

        ini_set('memory_limit', '64M');
        $backPath = public_path('/public_html/assets/media/image/prices/background.png');
        $data = \App\Models\Product::all();

        $pdf = PDF::loadView('panel.pdf.prices', ['data' => $data, 'type' => $type], [], [
            'margin_top' => 50,
            'margin_bottom' => 20,
            'watermark_image_alpha' => 1,
            'default_font_size' => 15,
            'show_watermark_image' => true,
            'watermarkImgBehind' => true,
            'watermark_image_path' => $backPath,
            'default_font' => 'vazir' // specify the font here
        ]);


        $name = 'لیست '.Product::PRICE_TYPE[$type];

        return $pdf->stream("$name.pdf");
    }

    public function addModel(Request $request)
    {
        $this->authorize('prices-list');

        if (!DB::table('price_list_models')->where('name', $request->name)->exists()){
            DB::table('price_list_models')->insert(['name' => $request->name]);
            return back();
        }else{
            return response()->json(['data' => ['message' => 'این مدل موجود می باشد']]);
        }
    }

    public function addSeller(Request $request)
    {
        $this->authorize('prices-list');

        if (!DB::table('price_list_sellers')->where('name', $request->name)->exists()){
            DB::table('price_list_sellers')->insert(['name' => $request->name]);
            return back();
        }else{
            return response()->json(['data' => ['message' => 'این تامین کننده موجود می باشد']]);
        }
    }

    public function removeSeller(Request $request)
    {
        $this->authorize('prices-list');

        $seller = DB::table('price_list_sellers')->where('name', $request->name)->first();
        DB::table('price_list')->where('seller_id', $seller->id)->delete();
        DB::table('price_list_sellers')->where('name', $request->name)->delete();

        return back();
    }

    public function removeModel(Request $request)
    {
        $this->authorize('prices-list');

        $model = DB::table('price_list_models')->where('name', $request->name)->first();
        DB::table('price_list')->where('model_id', $model->id)->delete();
        DB::table('price_list_models')->where('name', $request->name)->delete();

        return back();
    }
}

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

        if (auth()->user()->isCEO() || auth()->user()->isAdmin()){
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

    public function priceList($type)
    {
        $this->authorize('prices-list');

        ini_set('memory_limit', '64M');

        $backPath = public_path('/assets/media/image/prices/background.png');
        $data = \App\Models\Product::all();

        $pdf = PDF::loadView('panel.pdf.prices',['data' => $data, 'type' => $type],[], [
            'margin_top' => 50,
            'margin_bottom' => 20,
            'watermark_image_alpha' => 1,
            'default_font_size' => 15,
            'show_watermark_image' => true,
            'watermarkImgBehind' => true,
            'watermark_image_path' => $backPath
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

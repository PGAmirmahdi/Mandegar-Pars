<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class PriceController extends Controller
{
    public function index()
    {
        $this->authorize('prices-list');
        return view('panel.prices.list');
    }

    public function otherList(Request $request)
    {
        $this->authorize('prices-list');

        if (auth()->user()->isCEO() || auth()->user()->isAdmin() || auth()->user()->isOrgan()){
            $sellers = DB::table('price_list_sellers')->get();
            $products = Product::query()
                ->when($request->category && $request->category !== 'all', function ($query) use ($request) {
                    $query->where('category_id', $request->category);
                })
                ->when($request->product_id && $request->product_id !== 'all', function ($query) use ($request) {
                    $query->where('id', $request->product_id);
                })
                ->latest()->get();

            return view('panel.prices.other-list',compact('sellers','products'));
        }else{
            return view('panel.prices.other-list-printable');
        }
    }

    public function updatePrice(Request $request)
    {
        $this->authorize('prices-list');

        $items = json_decode($request->items, true);
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'بروزرسانی قیمت‌ها',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')' . ' قیمت‌ها را برای '. ' کالاها بروزرسانی کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);

        foreach ($items as $item) {
            $price = trim(str_replace('-', null, $item['price']));
            $price = trim(str_replace(',', null, $price));
            $price = $price == '' ? null : $price;

            if ($price) {
                DB::table('price_list')->where([
                    'seller_id' => $item['seller_id'],
                    'product_id' => $item['product_id'] // تغییر از model_id به product_id
                ])->updateOrInsert([
                    'seller_id' => $item['seller_id'],
                    'product_id' => $item['product_id'] // تغییر از model_id به product_id
                ], [
                    'seller_id' => $item['seller_id'],
                    'product_id' => $item['product_id'], // تغییر از model_id به product_id
                    'price' => $price
                ]);
            } else {
                DB::table('price_list')->where([
                    'seller_id' => $item['seller_id'],
                    'product_id' => $item['product_id'] // تغییر از model_id به product_id
                ])->delete();
            }
        }

        return 'ok';
    }

    public function updatePrice2(Request $request)
    {
        $this->authorize('price-list-mandegar');

        $items = json_decode($request->items, true);

        // ثبت فعالیت قبل از اعمال تغییرات
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'بروزرسانی قیمت‌ها',
            'description' => 'کاربر ' . auth()->user()->family .'(' . Auth::user()->role->label . ')' . ' قیمت‌ها را برای ' . ' محصولات ماندگار بروزرسانی کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);  // ثبت فعالیت
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
// ثبت فعالیت مربوط به دریافت لیست قیمت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'دریافت لیست قیمت',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')' . ' لیست قیمت ' . Product::PRICE_TYPE[$type] . ' را دریافت کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);  // ثبت فعالیت

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
            $activityDescription = ' مدل با نام "' . $request->name . ' توسط کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')' . '" با موفقیت اضافه شد.';
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'افزودن مدل جدید',
                'description' => $activityDescription,
                'created_at' => now(),
            ]);
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
            // ثبت موفقیت‌آمیز در فعالیت‌ها
            $activityDescription = ' تامین‌کننده با نام "'  . $request->name . ' توسط کاربر ' . auth()->user()->family .'(' . Auth::user()->role->label . ')' . '" با موفقیت اضافه شد.';

            // ثبت فعالیت
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'افزودن تامین‌کننده جدید',
                'description' => $activityDescription,
                'created_at' => now(),
            ]);
            return back();
        }else{
            return response()->json(['data' => ['message' => 'این تامین کننده موجود می باشد']]);
        }
    }

    public function removeSeller(Request $request)
    {
        $this->authorize('prices-list');

        // دریافت seller_id از درخواست
        $sellerId = $request->input('seller_id');

        // چک کردن وجود فروشنده با شناسه
        $seller = DB::table('price_list_sellers')->where('id', $sellerId)->first();

        if (!$seller) {
            return response()->json([
                'status' => 'error',
                'message' => 'فروشنده پیدا نشد',
            ], 404);
        }

        // ثبت فعالیت برای حذف تامین‌کننده
        $activityDescription = 'کاربر ' . auth()->user()->family .'(' . Auth::user()->role->label . ')' . ' اقدام به حذف تامین‌کننده با نام "' . $seller->name . '" از لیست تامین‌کنندگان نمود.';

        // حذف قیمت‌های مربوطه به تامین‌کننده
        DB::table('price_list')->where('seller_id', $seller->id)->delete();
        DB::table('price_list_sellers')->where('id', $seller->id)->delete();

        // ثبت فعالیت حذف تامین‌کننده
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف تامین‌کننده',
            'description' => $activityDescription,
            'created_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'فروشنده با موفقیت حذف شد',
        ]);
    }


    public function removeModel(Request $request)
    {
        $this->authorize('prices-list');
        // ثبت فعالیت برای حذف مدل
        $activityDescription = 'کاربر ' . auth()->user()->family .'(' . Auth::user()->role->label . ')' . ' اقدام به حذف مدل با نام "' . $request->name . '" از لیست مدل‌ها نمود.';
        $model = DB::table('price_list_models')->where('name', $request->name)->first();
        DB::table('price_list')->where('model_id', $model->id)->delete();
        DB::table('price_list_models')->where('name', $request->name)->delete();
        // ثبت فعالیت حذف مدل
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'حذف مدل',
            'description' => $activityDescription,
            'created_at' => now(),
        ]);
        return back();
    }
}

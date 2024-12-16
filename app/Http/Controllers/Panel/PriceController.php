<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\PriceHistory;
use App\Models\PriceListSeller;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\Seller;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;
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

        if (auth()->user()->isCEO() || auth()->user()->isAdmin() || auth()->user()->isOrgan()) {
            // فیلتر دسته‌بندی روی فروشنده‌ها
            $sellers = DB::table('price_list_sellers')
                ->when($request->category && $request->category !== 'all', function ($query) use ($request) {
                    $query->where('category_id', $request->category);
                })
                ->when($request->seller && $request->seller !== 'all', function ($query) use ($request) {
                    $query->where('name', $request->seller);
                })
                ->get();
            $products = Product::query()
                ->when($request->category && $request->category !== 'all', function ($query) use ($request) {
                    $query->where('category_id', $request->category);
                })
                ->when($request->model && $request->model !== 'all', function ($query) use ($request) {
                    // فیلتر بر اساس مدل
                    $query->where('brand_id', $request->model);
                })
                ->when($request->product_id && $request->product_id !== 'all', function ($query) use ($request) {
                    $query->where('id', $request->product_id);
                })
                ->orderByDesc('brand_id')->get();
            $models = ProductModel::all();
            return view('panel.prices.other-list', compact('sellers', 'products','models'));
        } else {
            // فیلتر دسته‌بندی روی فروشنده‌ها
            $sellers = DB::table('price_list_sellers')
                ->when($request->category && $request->category !== 'all', function ($query) use ($request) {
                    $query->where('category_id', $request->category);
                })
                ->get();
            $products = Product::query()
                ->when($request->category && $request->category !== 'all', function ($query) use ($request) {
                    $query->where('category_id', $request->category);
                })
                ->when($request->product_id && $request->product_id !== 'all', function ($query) use ($request) {
                    $query->where('id', $request->product_id);
                })
                ->orderByDesc('brand_id')->get();
            return view('panel.prices.other-list-printable', compact('sellers', 'products'));
        }
    }

    public function updatePrice(Request $request)
    {
        $this->authorize('prices-list');

        $items = json_decode($request->items, true);

        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'بروزرسانی قیمت‌ها',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') قیمت‌ها را برای کالاها بروزرسانی کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);

        foreach ($items as $item) {
            try {
                // حذف فرمت‌های اضافی از قیمت
                $price = trim(str_replace(['-', ','], '', $item['price']));
                $price = $price === '' ? null : $price;

                if ($price) {
                    // دریافت قیمت قبلی
                    $previousPrice = DB::table('price_list')
                        ->where(['seller_id' => $item['seller_id'], 'product_id' => $item['product_id']])
                        ->value('price');

                    // درج یا بروزرسانی قیمت در جدول price_list
                    DB::table('price_list')->updateOrInsert(
                        [
                            'seller_id' => $item['seller_id'],
                            'product_id' => $item['product_id']
                        ],
                        [
                            'price' => $price,
                            'updated_at' => now(),
                        ]
                    );
                    if ($previousPrice == $price) {
                        continue;
                    } else(
                    \App\Models\PriceHistory::create([
                        'user_id' => auth()->id(),
                        'product_id' => $item['product_id'],
                        'price_field' => \App\Models\PriceListSeller::find($item['seller_id'])->name, // دریافت نام فروشنده
                        'price_amount_from' => $previousPrice ?? 0, // مقدار قبلی
                        'price_amount_to' => $price, // مقدار جدید
                    ]));


                    // به‌روزرسانی فیلد market_price در جدول محصولات
                    Product::where('id', $item['product_id'])->update(['market_price' => $price]);
                } else {
                    // حذف رکورد اگر قیمت null است
                    DB::table('price_list')->where([
                        'seller_id' => $item['seller_id'],
                        'product_id' => $item['product_id']
                    ])->delete();
                }
            } catch (\Exception $e) {
                // ثبت خطا در لاگ
                Log::error('خطا در بروزرسانی قیمت', [
                    'seller_id' => $item['seller_id'],
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json(['message' => 'قیمت‌ها با موفقیت به‌روزرسانی شدند'], 200);
    }

    public function updatePrice2(Request $request)
    {
        $this->authorize('price-list-mandegar');

        $items = json_decode($request->items, true);

        // ثبت فعالیت قبل از اعمال تغییرات
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'بروزرسانی قیمت‌ها',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')' . ' قیمت‌ها را برای ' . ' محصولات ماندگار بروزرسانی کرد.',
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
            \App\Models\PriceHistory::create(
                [
                    'product_id' => $item['id'],
                    'price_field' => $item['field'],
                ],
                [
                    'price_amount_from' => $price,
                    'price_amount_to' => $price,
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


        $name = 'لیست ' . Product::PRICE_TYPE[$type];

        return $pdf->stream("$name.pdf");
    }

    public function addModel(Request $request)
    {
        $this->authorize('prices-list');

        if (!DB::table('price_list_models')->where('name', $request->name)->exists()) {
            DB::table('price_list_models')->insert(['name' => $request->name]);
            $activityDescription = ' مدل با نام "' . $request->name . ' توسط کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')' . '" با موفقیت اضافه شد.';
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'افزودن مدل جدید',
                'description' => $activityDescription,
                'created_at' => now(),
            ]);
            return back();
        } else {
            return response()->json(['data' => ['message' => 'این مدل موجود می باشد']]);
        }
    }

    public function addSeller(Request $request)
    {
        $this->authorize('prices-list');

        // اعتبارسنجی داده‌های ورودی
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|exists:categories,id',
        ], [
            'name.required' => 'نام فروشنده الزامی است.',
            'category.required' => 'دسته‌بندی الزامی است.',
            'category.exists' => 'دسته‌بندی انتخاب شده معتبر نیست.',
        ]);

        // بررسی وجود فروشنده
        if (!DB::table('price_list_sellers')->where('name', $validated['name'])->exists()) {
            DB::table('price_list_sellers')->insert([
                'name' => $validated['name'],
                'category_id' => $validated['category'],
            ]);

            // ثبت فعالیت
            $activityDescription = 'تامین‌کننده با نام "' . $validated['name'] . '" توسط کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')' . '" با موفقیت اضافه شد.';
            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'افزودن تامین‌کننده جدید',
                'description' => $activityDescription,
                'created_at' => now(),
            ]);

            return response()->json(['data' => ['seller_id' => DB::getPdo()->lastInsertId()]]);
        } else {
            return response()->json(['data' => null, 'message' => 'این تامین‌کننده موجود است.']);
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
        $activityDescription = 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')' . ' اقدام به حذف تامین‌کننده با نام "' . $seller->name . '" از لیست تامین‌کنندگان نمود.';

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
        $activityDescription = 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ')' . ' اقدام به حذف مدل با نام "' . $request->name . '" از لیست مدل‌ها نمود.';
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
    public function getPriceChartData(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'seller_id' => 'nullable|exists:price_list_sellers,id', // اعتبارسنجی فروشنده
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // تبدیل تاریخ‌ها به فرمت میلادی
        $startDate = Verta::parse($request->start_date)->datetime();
        $endDate = Verta::parse($request->end_date)->datetime();

        // شروع کوئری برای دریافت تاریخچه قیمت‌ها
        $query = PriceHistory::where('product_id', $request->product_id)
            ->whereBetween('created_at', [$startDate, $endDate]);

        // فیلتر بر اساس نام فروشنده
        if ($request->filled('seller_id')) {
            // گرفتن نام فروشنده از آیدی
            $sellerName = PriceListSeller::find($request->seller_id)->name; // فرض بر این است که نام فروشنده در فیلد 'name' ذخیره شده است
            $query->where('price_field', $sellerName); // فیلتر بر اساس نام فروشنده
        }

        // دریافت تاریخچه قیمت‌ها
        $prices = $query->orderBy('created_at')->get();

        if ($prices->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'داده‌ای برای این بازه وجود ندارد.']);
        }

        // آماده‌سازی داده‌ها برای نمودار
        $labels = $prices->map(function ($price) {
            return verta($price->created_at)->format('Y/m/d');
        });

        $pricesData = $prices->map(function ($price) {
            return $price->price_amount_to;
        });

        // دریافت نام محصول
        $product = Product::find($request->product_id);

        // دریافت نام فروشنده (فقط اولین نام فروشنده را می‌گیرد، اگر چند فروشنده وجود داشته باشد)
        $sellerNames = $prices->pluck('price_field')->unique()->toArray();

        // بازگشت پاسخ
        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'prices' => $pricesData,
                'productName' => $product->title,
                'sellerNames' => $sellerNames, // لیست نام فروشنده‌ها
            ]
        ]);
    }

}

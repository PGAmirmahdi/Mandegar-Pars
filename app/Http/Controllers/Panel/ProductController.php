<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Activity;
use App\Models\Category;
use App\Models\PriceHistory;
use App\Models\PriceListSeller;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('products-list');

        $category_id = $request->query('category');

        $products = Product::query()
            ->where('status', 'approved')
            ->when($category_id, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->paginate(30);

        $highlightedProductId = $request->query('highlighted_product_id');

        return view('panel.products.index', compact('products', 'highlightedProductId'));
    }


    public function request(Request $request)
    {
        $this->authorize('products-list');

        $query = Product::query()
            ->when($request->product && $request->product !== 'all', function ($query) use ($request) {
                $query->where('id', $request->product);
            })
            ->when($request->code, function ($query) use ($request) {
                return $query->where('code', $request->code);
            })
            ->when($request->category && $request->category !== 'all', function ($query) use ($request) {
                $query->where('category_id', $request->category);
            })
            ->when($request->brand && $request->brand !== 'all', function ($query) use ($request) {
                $query->where('brand_id', $request->brand);
            })->when($request->status && $request->status !== 'all', function ($query) use ($request) {
                $query->where('status', $request->status);
            });

        if (auth()->user()->isAdmin() || auth()->user()->isOfficeManager()) {
            $products = $query->whereIn('status', ['pending', 'rejected'])->latest()->paginate(30);
        } else {
            $products = $query->whereIn('status', ['pending', 'rejected']) // اضافه کردن وضعیت rejected
            ->where('creator_id', auth()->user()->id) // فیلتر بر اساس ID کاربر
            ->latest()->paginate(30);
        }
        return view('panel.products.ProductAccept.index', compact('products'));
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorize('products-create');

        // جستجوی محصولات مشابه با استفاده از LIKE
        $similarProduct = Product::where('category_id', $request->category)
            ->where('title', 'LIKE', '%' . $request->title . '%')
            ->first();

        if ($similarProduct) {
            return back()->withErrors([
                'title' => 'محصولی مشابه با عنوان "' . $similarProduct->title . '" قبلاً ثبت شده است.'
            ])->withInput();
        }

        // آپلود تصویر
        $image = null;
        if ($request->has('image')) {
            $image = upload_file($request->image, 'Products');
        }

        // تنظیمات محصول
        $properties = $this->json_properties($request);
        $total_count = array_sum($request->counts);
        $status = auth()->user()->isAdmin() || auth()->user()->isOfficeManager() ? $request->status : 'pending';

        // ایجاد محصول
        $product = Product::create([
            'title' => $request->title,
            'code' => 'MP' . random_int(10000, 99999),
            'image' => $image,
            'category_id' => $request->category,
            'brand_id' => $request->brand,
            'properties' => $properties,
            'description' => $request->description,
            'system_price' => $request->system_price,
            'partner_price_tehran' => $request->partner_price_tehran,
            'partner_price_other' => $request->partner_price_other,
            'single_price' => $request->single_price,
            'creator_id' => auth()->id(),
            'total_count' => $total_count,
            'status' => $status,
        ]);

        // ارسال اعلان برای ادمین‌ها
        if (!auth()->user()->isAdmin()) {
            $title = 'ثبت کالا';
            $message = "یک درخواست ثبت کالا توسط " . auth()->user()->family . " ایجاد شد.";
            $url = route('products.index');

            $admins = User::whereHas('role', function ($query) {
                $query->where('name', 'admin');
            })->get();

            Notification::send($admins, new SendMessage($title, $message, $url));
        }

        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ایجاد درخواست کالا',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') درخواست ثبت کالای جدیدی به نام ' . $request->title . ' ایجاد کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);

        alert()->success('درخواست ثبت کالا مورد نظر با موفقیت ایجاد شد', 'ایجاد  درخواست ثبت کالا');
        return redirect()->route('products.index');
    }
    public function checkDuplicate(Request $request)
    {
        $title = $request->input('title');
        $category_id = $request->input('category');


        $product = Product::where('title', $title)
            ->where('category_id', $category_id)
            ->first();

        if ($product) {

            $totalProductsInCategory = Product::where('category_id', $category_id)->count();


            $rowNumber = Product::where('category_id', $category_id)
                ->where('id', '<=', $product->id)
                ->orderBy('id')
                ->count();


            $perPage = 30;
            $page = ceil($rowNumber / $perPage);


            return response()->json([
                'success' => true,
                'message' => 'محصول مورد نظر یافت شد.',
                'product_id' => $product->id,
                'product_url' => route('products.index', [
                    'page' => $page,
                    'category' => $category_id,
                    'highlighted_product_id' => $product->id
                ]),
                'row_number' => $rowNumber,
                'page' => $page,
                'total_products' => $totalProductsInCategory
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'محصولی با این مشخصات یافت نشد.',
        ]);
    }
    private function json_properties($request)
    {
        $items = [];
        foreach ($request->colors as $key => $color) {
            $items[] = [
                'color' => $color,
                'print_count' => $request->print_count[$key],
                'counts' => $request->counts[$key],
            ];
        }
        return json_encode($items);
    }

    public function create()
    {
        $this->authorize('products-create');

        $categories = Category::all();
        return view('panel.products.create', compact('categories'));
    }

    public function show(Product $product)
    {
        //
    }

    public function edit(Product $product)
    {
        $this->authorize('products-edit');

        return view('panel.products.edit', compact('product'));
    }

    public function destroy(Product $product)
    {
        $this->authorize('products-delete');

        // بررسی وجود محصول در سفارشات
        if ($product->invoices()->exists()) {
            return response('این کالا در سفارشاتی موجود است', 500);
        }

        // بررسی وجود محصول در سفارشات
        if ($product->inventories()->exists()) {
            return response('این کالا در انبار موجود است', 500);
        }

        // ثبت فعالیت قبل از حذف محصول
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'حذف کالا',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') کالا ' . $product->title . ' را حذف کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ذخیره فعالیت

        $product->delete();
        return back();
    }

    public function search(Request $request)
    {
        $this->authorize('products-list');

        $products = Product::query()
            ->when($request->product && $request->product !== 'all', function ($query) use ($request) {
                $query->where('id', $request->product);
            })
            ->when($request->code, function ($query) use ($request) {
                return $query->where('code', $request->code);
            })
            ->when($request->category && $request->category !== 'all', function ($query) use ($request) {
                $query->where('category_id', $request->category);
            })
            ->when($request->brand && $request->brand !== 'all', function ($query) use ($request) {
                $query->where('brand_id', $request->brand);
            })->latest()->paginate(30);
        return view('panel.products.index', compact('products'));
    }

    public function search2(Request $request)
    {
        $this->authorize('products-list');

        $products = Product::query()
            ->when(!auth()->user()->isAdmin(), function ($query) {
                $query->where('creator_id', auth()->id());
            })
            ->when($request->product && $request->product !== 'all', function ($query) use ($request) {
                $query->where('id', $request->product);
            })
            ->when($request->code, function ($query) use ($request) {
                return $query->where('code', $request->code);
            })
            ->when($request->category && $request->category !== 'all', function ($query) use ($request) {
                $query->where('category_id', $request->category);
            })
            ->when($request->model && $request->model !== 'all', function ($query) use ($request) {
                $query->where('brand_id', $request->model);
            })
            ->latest()
            ->paginate(30);

        return view('panel.products.ProductAccept.index', compact('products'));
    }

    public function pricesHistory(Request $request)
    {
        $this->authorize('price-history');
        $pricesHistory = PriceHistory::query()
            ->when($request->category && $request->category !== 'all', function ($query) use ($request) {
                // فیلتر بر اساس دسته‌بندی
                $query->whereHas('product', function ($query) use ($request) {
                    $query->where('category_id', $request->category);
                });
            })
            ->when($request->model && $request->model !== 'all', function ($query) use ($request) {
                // فیلتر بر اساس مدل
                $query->whereHas('product', function ($query) use ($request) {
                    $query->where('brand_id', $request->model);
                });
            })
            ->when($request->product && $request->product !== 'all', function ($query) use ($request) {
                // فیلتر بر اساس محصول
                $query->where('product_id', $request->product);
            })
            ->when($request->seller && $request->seller !== 'all', function ($query) use ($request) {
                // فیلتر بر اساس نام فروشنده
                $query->where('price_field', $request->seller);
            })
            ->orderByDesc('updated_at')
            ->paginate(30);

        // ارسال داده‌ها به ویو
        $categories = Category::all();
        $models = ProductModel::all();
        $products = Product::all();
        $sellers = PriceListSeller::all();
        return view('panel.prices.history', compact('pricesHistory', 'products', 'sellers', 'categories', 'models'));
    }

    public function excel()
    {// ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'دانلود فایل کالاها',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') ' . 'اکسل کالاها را دانلود کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        return Excel::download(new \App\Exports\ProductsExport, 'products.xlsx');
    }

    public function getModelsByCategory(Request $request)
    {
        $models = ProductModel::where('category_id', $request->category_id)->get();
        return response()->json($models);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('products-edit');

        // Handle image upload
        $image = $product->image; // Default to the current image
        if ($request->hasFile('image')) {
            $image = upload_file($request->image, 'Products');
        }

        // Update product properties
        $properties = $this->json_properties($request);
        $total_count = array_sum($request->counts);
        $status = auth()->user()->isAdmin() || auth()->user()->isOfficeManager()? $request->status : 'pending';
        // Update product details
        $product->update([
            'title' => $request->title,
            'image' => $image,
            'category_id' => $request->category,
            'brand_id' => $request->brand,
            'properties' => $properties,
            'description' => $request->description,
            'system_price' => $request->system_price,
            'partner_price_tehran' => $request->partner_price_tehran,
            'partner_price_other' => $request->partner_price_other,
            'single_price' => $request->single_price,
            'total_count' => $total_count,
            'status' => $status,
            'reject_message' => $request->reject_message
        ]);
        $title = $status === 'approved' ? 'تایید درخواست ثبت کالا' : 'رد درخواست ثبت کالا';
        $message = $status === 'approved'
            ? "درخواست ثبت کالای شما به عنوان {$request->title} تایید شد."
            : "درخواست ثبت کالا به نام {$request->title} رد شد.";
        $url = route('products.index');
        Notification::send($product->creator, new SendMessage($title, $message, $url));
        // Log activity
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'ویرایش کالا',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . auth()->user()->role->label . ') کالا ' . $product->title . ' را ویرایش کرد.',
            'created_at' => now(),
        ]);

        alert()->success('کالا با موفقیت ویرایش شد', 'ویرایش کالا');
        return redirect()->route('products.request');
    }

    private function priceHistory($product, $request)
    {
        if ($request->system_price != $product->system_price) {
            $product->histories()->create([
                'price_field' => 'system_price',
                'price_amount_from' => $product->system_price,
                'price_amount_to' => $request->system_price,
            ]);
        }
        if ($request->partner_price_tehran != $product->partner_price_tehran) {
            $product->histories()->create([
                'price_field' => 'partner_price_tehran',
                'price_amount_from' => $product->partner_price_tehran,
                'price_amount_to' => $request->partner_price_tehran,
            ]);
        }
        if ($request->partner_price_other != $product->partner_price_other) {
            $product->histories()->create([
                'price_field' => 'partner_price_other',
                'price_amount_from' => $product->partner_price_other,
                'price_amount_to' => $request->partner_price_other,
            ]);
        }
        if ($request->single_price != $product->single_price) {
            $product->histories()->create([
                'price_field' => 'single_price',
                'price_amount_from' => $product->single_price,
                'price_amount_to' => $request->single_price,
            ]);
        }
    }

}

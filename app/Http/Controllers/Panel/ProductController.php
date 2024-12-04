<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Activity;
use App\Models\Category;
use App\Models\PriceHistory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ProductController extends Controller
{
    public function index()
    {
        $this->authorize('products-list');

        $products = Product::latest()->paginate(30);
        return view('panel.products.index', compact('products'));
    }

    public function create()
    {
        $this->authorize('products-create');

        $categories = Category::all();
        return view('panel.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorize('products-create');

        $image = null; // مقدار پیش‌فرض برای تصویر
        if ($request->has('image'))
        {
            $image = upload_file($request->image, 'Products');
        }

        // product properties
        $properties = $this->json_properties($request);
        $total_count = array_sum($request->counts);

        // create product
        Product::create([
            'title' => $request->title,
//            'slug' => make_slug($request->title)
            'code' => $request->code,
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
        ]);
        // ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ایجاد محصول',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') محصول جدیدی به نام ' . $request->title . ' ایجاد کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ذخیره فعالیت
        alert()->success('محصول مورد نظر با موفقیت ایجاد شد', 'ایجاد محصول');
        return redirect()->route('products.index');
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

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('products-edit');
        $image = null; // مقدار پیش‌فرض برای تصویر
        if ($request->has('image'))
        {
            $image = upload_file($request->image, 'Products');
        }

        // price history
        $this->priceHistory($product, $request);

        // product properties
        $properties = $this->json_properties($request);
        $total_count = array_sum($request->counts);

        // create product
        $product->update([
            'title' => $request->title,
//            'slug' => make_slug($request->slug),
            'code' => $request->code,
            'image' => $image ?? $product->image,
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
        ]);
// ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'ویرایش محصول',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') محصول ' . $product->title . ' را ویرایش کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ذخیره فعالیت
        alert()->success('محصول مورد نظر با موفقیت ویرایش شد','ویرایش محصول');
        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
        $this->authorize('products-delete');

        // بررسی وجود محصول در سفارشات
        if ($product->invoices()->exists()) {
            return response('این محصول در سفارشاتی موجود است', 500);
        }

        // ثبت فعالیت قبل از حذف محصول
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'حذف محصول',
            'description' => 'کاربر ' . auth()->user()->family . ' (' . Auth::user()->role->label . ') محصول ' . $product->title . ' را حذف کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData); // ذخیره فعالیت

        $product->delete();
        return back();
    }

    public function search(Request $request)
    {
        $this->authorize('products-list');

        $products = Product::where('title', 'like', "%$request->title%")->when($request->code, function ($query) use ($request) {
            return $query->where('code', $request->code);
        })->latest()->paginate(30);

        return view('panel.products.index', compact('products'));
    }

    public function pricesHistory()
    {
        $this->authorize('price-history');

        $pricesHistory = PriceHistory::latest()->paginate(30);
        return view('panel.prices.history', compact('pricesHistory'));
    }

    public function pricesHistorySearch(Request $request)
    {
        $this->authorize('price-history');

        $products_id = Product::where('title','like', "%$request->title%")->pluck('id');
        $pricesHistory = PriceHistory::whereIn('product_id', $products_id)->latest()->paginate(30);

        return view('panel.prices.history', compact('pricesHistory'));
    }

    public function excel()
    {// ثبت فعالیت
        $activityData = [
            'user_id' => auth()->id(),
            'action' => 'دانلود فایل محصولات',
            'description' => 'کاربر ' . auth()->user()->family . '(' . Auth::user()->role->label . ') ' . 'اکسل محصولات را دانلود کرد.',
            'created_at' => now(),
        ];
        Activity::create($activityData);
        return Excel::download(new \App\Exports\ProductsExport, 'products.xlsx');
    }

    private function json_properties($request){
        $items = [];
        foreach ($request->colors as $key => $color){
            $items[] = [
                'color' => $color,
                'print_count' => $request->print_count[$key],
                'counts' => $request->counts[$key],
            ];
        }
        return json_encode($items);
    }

    private function priceHistory($product, $request)
    {
        if ($request->system_price != $product->system_price){
            $product->histories()->create([
                'price_field' => 'system_price',
                'price_amount_from' => $product->system_price,
                'price_amount_to' => $request->system_price,
            ]);
        }
        if ($request->partner_price_tehran != $product->partner_price_tehran){
            $product->histories()->create([
                'price_field' => 'partner_price_tehran',
                'price_amount_from' => $product->partner_price_tehran,
                'price_amount_to' => $request->partner_price_tehran,
            ]);
        }
        if ($request->partner_price_other != $product->partner_price_other){
            $product->histories()->create([
                'price_field' => 'partner_price_other',
                'price_amount_from' => $product->partner_price_other,
                'price_amount_to' => $request->partner_price_other,
            ]);
        }
        if ($request->single_price != $product->single_price){
            $product->histories()->create([
                'price_field' => 'single_price',
                'price_amount_from' => $product->single_price,
                'price_amount_to' => $request->single_price,
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Analyse;
use App\Models\AnalyseProducts;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductModel;
use Illuminate\Http\Request;

class AnalyseController extends Controller
{
    public function index(Request $request)
    {
        // ساخت کوئری برای جدول `Analyse`
        $analyses = Analyse::query()
            // فیلتر تاریخ بین `start_date` و `end_date`
            ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            })
            // فیلتر بر اساس دسته‌بندی (category)
            ->when($request->category && $request->category !== 'all', function ($query) use ($request) {
                $query->where('category_id', $request->category);
            })
            // فیلتر بر اساس مدل (brand/model)
            ->when($request->model && $request->model !== 'all', function ($query) use ($request) {
                $query->where('brand_id', $request->model);
            })
            // مرتب‌سازی جدیدترین‌ها
            ->latest()
            // صفحه‌بندی نتایج (30 نتیجه در هر صفحه)
            ->paginate(30);
        $categories = Category::all();
        $models = ProductModel::all();
        // ارسال داده‌ها به ویو
        return view('panel.analyse.index', compact('analyses', 'categories', 'models'));
    }

    public function show(Request $request, $id)
    {
        // دریافت آنالیز و محصولات مرتبط
        $analyse = Analyse::with(['products' => function ($query) use ($request) {
            $query->select('products.id', 'products.title', 'analyse_products.quantity');

            // اگر محصول خاصی انتخاب شده باشد
            if ($request->has('product') && $request->product != 'all') {
                // فیلتر کردن بر اساس محصول انتخاب شده
                $query->where('products.id', $request->product);
            }
        }])->findOrFail($id);

        // دریافت لیست تمام محصولات برای نمایش در فرم
        $allProducts = Product::all();  // می‌توانید لیست تمام محصولات را به همین شکل بارگذاری کنید.

        // ارسال اطلاعات به ویو
        return view('panel.analyse.show', [
            'analyse' => $analyse,
            'products' => $analyse->products, // محصولات مرتبط با مقدار quantity
            'allProducts' => $allProducts,     // همه محصولات برای نمایش در فرم جستجو
            'selectedProduct' => $request->product, // محصول انتخاب‌شده در جستجو
        ]);
    }

    public function create()
    {
        $categories=Category::all();
        $brands=ProductModel::all();
        $products = []; // متغیر خالی برای محصولات
        return view('panel.analyse.create',compact('categories','brands','products'));
    }
    public function store(Request $request)
    {
        $analyse = Analyse::create([
            'date' =>$request->date ,
            'category_id' =>$request->category_id,
            'brand_id' => $request->brand_id,
            'creator_id' => auth()->id(),
        ]);
        foreach ($request->products as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $analyse->products()->attach($productId, [
                    'quantity' => $quantity,
                ]);
            }
        }
        alert()->success('آنالیز با موفقیت ثبت شد','ثبت آنالیز');
        return redirect()->route('analyse.index');
    }
    public function destroy(Analyse $analyse)
    {
        $analyse->delete();
        alert()->success('آنالیز با موفقیت حذف شد','حذف آنالیز');
        return back();
    }
    public function getProducts(Request $request)
    {
        $category_id = $request->input('category_id');
        $brand_id = $request->input('brand_id');

        if (!$category_id || !$brand_id) {
            return response()->json(['products' => []]); // اگر ورودی معتبر نیست، آرایه خالی برگردانید
        }

        $products = Product::where('category_id', $category_id)
            ->where('brand_id', $brand_id)
            ->get();
        // اضافه کردن مقدار quantity برای هر محصول
        $productsWithQuantity = $products->map(function ($product) {
            // پیدا کردن quantity مربوط به هر محصول
            $quantity = AnalyseProducts::where('product_id', $product->id)->select('quantity')->first();
            // افزودن quantity به هر محصول
            $product->quantity = $quantity ? $quantity->quantity : 0; // اگر quantity یافت نشد، 0 را تنظیم می‌کنیم
            return $product;
        });
        return response()->json(['products' => $productsWithQuantity]);
    }

}

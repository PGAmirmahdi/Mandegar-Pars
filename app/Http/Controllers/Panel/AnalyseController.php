<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnalyseRequest;
use App\Models\Analyse;
use App\Models\AnalyseProducts;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyseController extends Controller
{


    public function index(Request $request)
    {
        $analyses = Analyse::query()
            ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    $subQuery->where('date', '<=', $request->end_date)
                        ->where('to_date', '>=', $request->start_date);
                });
            })
            ->when($request->category && $request->category !== 'all', function ($query) use ($request) {
                $query->where('category_id', $request->category);
            })
            ->when($request->model && $request->model !== 'all', function ($query) use ($request) {
                $query->where('brand_id', $request->model);
            })
            ->get()
            ->groupBy(function ($analyse) {
                return Carbon::parse($analyse->date)->month; // گروه‌بندی بر اساس ماه
            })
            ->sortKeysDesc(); // مرتب‌سازی نزولی بر اساس کلید ماه

// آرایه نام ماه‌های فارسی
        $monthNames = [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];
        $categories = Category::all();
        $models = ProductModel::all();

        return view('panel.analyse.index', compact('analyses', 'categories', 'models','monthNames'));
    }

    public function show(Request $request, $id)
    {
        // دریافت آنالیز و محصولات مرتبط
        $analyse = Analyse::with(['products' => function ($query) use ($request) {
            $query->select('products.id', 'products.title','products.total_count', 'analyse_products.quantity','analyse_products.storage_count','analyse_products.sold_count')->orderBy('analyse_products.quantity', 'desc');

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
        $products = [];
        return view('panel.analyse.create',compact('categories','brands','products'));
    }

    public function edit(Request $request,$id)
    {
        $this->authorize('analyse-edit');
        $categories=Category::all();
        $brands=ProductModel::all();
        $analyse = Analyse::with(['products' => function ($query) use ($request) {
            $query->select('products.id', 'products.title','products.total_count', 'analyse_products.quantity','analyse_products.storage_count','analyse_products.sold_count')->orderBy('analyse_products.quantity', 'desc');

            // اگر محصول خاصی انتخاب شده باشد
            if ($request->has('product') && $request->product != 'all') {
                // فیلتر کردن بر اساس محصول انتخاب شده
                $query->where('products.id', $request->product);
            }
        }])->findOrFail($id);
        $products = [];
        return view('panel.analyse.edit', compact('analyse','categories','brands','products'));
    }

    public function update(StoreAnalyseRequest $request, Analyse $analyse)
    {
        $this->authorize('analyse-edit');

        $analyse->update([
            'date' => $request->date,
            'to_date' => $request->to_date,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
        ]);

        $analyse->products()->detach();

        // افزودن یا به‌روزرسانی ارتباط محصولات جدید
        foreach ($request->products as $productId => $productData) {
            $product = Product::find($productId);
            if ($product) {
                $analyse->products()->attach($productId, [
                    'quantity' => $productData['quantity'], // مقدار تعداد از فرم
                    'storage_count' => $productData['storage_count'], // مقدار موجودی انبار از فرم
                    'sold_count' => $productData['sold_count'],
                ]);
            }
        }

        alert()->success('آنالیز با موفقیت به‌روزرسانی شد', 'ویرایش آنالیز');
        return redirect()->route('analyse.index');
    }

    public function store(StoreAnalyseRequest $request)
    {
        $analyse = Analyse::create([
            'date' => $request->date,
            'to_date' => $request->to_date,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'creator_id' => auth()->id(),
        ]);
        foreach ($request->products as $productId => $productData) {

            $product = Product::find($productId);
            if ($product) {
                $analyse->products()->attach($productId, [
                    'quantity' => $productData['quantity'], // مقدار تعداد از فرم
                    'storage_count' => $productData['storage_count'], // مقدار موجودی انبار از فرم
                    'sold_count' => $productData['sold_count'],
                ]);
            }
        }

        alert()->success('آنالیز با موفقیت ثبت شد', 'ثبت آنالیز');
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
            return response()->json(['products' => []]);
        }

        $products = Product::where('category_id', $category_id)
            ->where('brand_id', $brand_id)
            ->get();

        $productsWithQuantity = $products->map(function ($product) {
            $lastQuantity = AnalyseProducts::where('product_id', $product->id)
                ->latest('id')
                ->value('quantity');

            $storageCount = $product->total_count ?? 0 ;
            $product->quantity = $lastQuantity ?? 0;
            $product->storage_count = $storageCount;
return $product;
        });

        return response()->json(['products' => $productsWithQuantity]);
    }

}

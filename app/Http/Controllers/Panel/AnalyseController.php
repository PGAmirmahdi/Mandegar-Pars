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
        $query = AnalyseProducts::query()
            ->selectRaw('product_id, SUM(quantity) as total_quantity, MAX(analyse_id) as last_analyse_id')
            ->groupBy('product_id');

        // اعمال فیلتر تاریخ (در صورت نیاز)
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereHas('analyse', function ($subQuery) use ($request) {
                $subQuery->whereBetween('date', [$request->start_date, $request->end_date]);
            });
        }

        // دریافت نتایج
        $groupedProducts = $query->get();

        return view('panel.analyse.index', compact('groupedProducts'));
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

        return response()->json(['products' => $products]);
    }

}

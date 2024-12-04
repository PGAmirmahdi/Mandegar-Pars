<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Analyse;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductModel;
use Illuminate\Http\Request;

class AnalyseController extends Controller
{
    public function step1(Request $request)
    {
        $query = Analyse::query();

        // فیلتر تاریخ از و تا
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        // دریافت آنالیزها
        $analyses = $query->get();

        return view('panel.analyse.step1', compact('analyses')); // ارسال متغیر analyses به ویو
    }

    public function postStep1(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        session(['analyse.date' => $request->date]); // ذخیره تاریخ در سشن
        return redirect()->route('analyse.step2'); // انتقال به مرحله بعد
    }

    public function step2()
    {
        $categories = Category::all(); // دریافت دسته‌بندی‌ها
        return view('panel.analyse.step2', compact('categories'));
    }

    public function postStep2(Request $request)
    {
        $request->validate(['category_id' => 'required|exists:categories,id']);
        session(['analyse.category_id' => $request->category_id]); // ذخیره دسته‌بندی در سشن
        return redirect()->route('analyse.step3');
    }

    public function step3()
    {
        $categoryId = session('analyse.category_id');
        $brands = ProductModel::where('category_id', $categoryId)->get();
        return view('panel.analyse.step3', compact('brands'));
    }

    public function postStep3(Request $request)
    {
        $request->validate(['brand_id' => 'required|exists:product_models,id']);
        session(['analyse.brand_id' => $request->brand_id]); // ذخیره برند در سشن
        return redirect()->route('analyse.step4');
    }

    public function step4()
    {
        $categoryId = session('analyse.category_id');
        $brandId = session('analyse.brand_id');
        $products = Product::where('category_id', $categoryId)
            ->where('brand_id', $brandId)
            ->get();
        return view('panel.analyse.step4', compact('products'));
    }

    public function submit(Request $request)
    {
        $request->validate(['products' => 'required|array', 'products.*' => 'numeric|min:1']);

        $invoice = Analyse::create([
            'date' => session('analyse.date'),
            'category_id' => session('analyse.category_id'),
            'brand_id' => session('analyse.brand_id'),
            'creator_id' => auth()->id(),
        ]);

        foreach ($request->products as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $invoice->products()->attach($productId, [
                    'quantity' => $quantity,
                ]);
            }
        }

        session()->forget('analyse'); // پاک کردن اطلاعات سشن
        return redirect()->route('analyse.step1')->with('success', 'پیش‌فاکتور ثبت شد!');
    }

}

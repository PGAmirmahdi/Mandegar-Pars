<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\MandegarPrice;
use App\Models\Product;
use Illuminate\Http\Request;
use PDF;

class MandegarPriceController extends Controller
{
    public function index()
    {
        $mandegar_price = MandegarPrice::with('product.category', 'product.productModels')
            ->orderBy('order')
            ->get();
        $existingProductIds = $mandegar_price->pluck('product_id')->toArray();
        $allProducts = Product::whereNotIn('id', $existingProductIds)->get();

        return view('panel.prices.mandegarprice', compact('mandegar_price', 'allProducts'));
    }

    public function MandegarPriceUpdate(Request $request)
    {
        $request->validate([
            'items' => 'required|json',
        ]);

        $items = json_decode($request->items, true);

        foreach ($items as $item) {
            MandegarPrice::updateOrCreate(
                ['product_id' => $item['id']],
                ['price' => $item['price']]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'قیمت‌ها با موفقیت به‌روزرسانی شدند!',
        ]);
    }

    public function MandegarPriceDelete(Request $request)
    {
        $productId = $request->product_id;
        MandegarPrice::where('product_id', $productId)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'محصول حذف شد.',
        ]);
    }
    public function getDetails($id)
    {
        $product = Product::with(['category', 'productModels'])->find($id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'category' => $product->category->name ?? '--',
                'brand' => $product->productModels->name ?? '--'
            ]
        ]);
    }

    public function updateOrder(Request $request)
    {
        try {
            $items = $request->input('items');

            foreach ($items as $item) {
                \App\Models\MandegarPrice::where('product_id', $item['id'])
                    ->update(['order' => $item['order']]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function downloadPDF(Request $request)
    {
        $products = MandegarPrice::with('product.category', 'product.productModels')->orderBy('order')->get();

        if ($products->isEmpty()) {
            return back()->withErrors(['message' => 'محصولی برای دانلود PDF یافت نشد.']);
        }

        $pdf = PDF::loadView('panel.pdf.prices', ['data' => $products], [], [
            'format' => 'A3',
            'orientation' => 'L',
            'margin_left' => 2,
            'margin_right' => 2,
            'margin_top' => 2,
            'margin_bottom' => 0,
        ]);

        // ثبت فعالیت
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'دانلود PDF',
            'description' => "کاربر " . auth()->user()->family . '(' . auth()->user()->role->label . ')' . " فایل PDF مربوط به لیست قیمت‌های ماندگار پارس را دانلود کرد",
        ]);

        return $pdf->stream("mandegar_prices.pdf");
    }
}

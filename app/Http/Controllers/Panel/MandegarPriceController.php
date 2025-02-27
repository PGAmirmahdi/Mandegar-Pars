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
            ->paginate(15);
        $existingProductIds = $mandegar_price->pluck('product_id')->toArray();
        $allProducts = Product::whereNotIn('id', $existingProductIds)->get();

        // ثبت فعالیت مشاهده لیست قیمت‌ها
        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'مشاهده لیست قیمت',
            'description' => "کاربر " . auth()->user()->family . '(' . auth()->user()->role->label . ')' . " لیست قیمت‌های ماندگار پارس را مشاهده کرد",
        ]);

        return view('panel.prices.mandegarprice', compact('mandegar_price', 'allProducts'));
    }

    public function MandegarPriceUpdate(Request $request)
    {
        $request->validate([
            'items' => 'required|json',
        ]);

        $items = json_decode($request->items, true);

        foreach ($items as $item) {
            // دریافت قیمت قبلی (در صورت وجود)
            $existing = MandegarPrice::where('product_id', $item['id'])->first();
            $previousPrice = $existing ? $existing->price : 0;
            $price = $item['price'];

            // به‌روزرسانی یا ایجاد رکورد قیمت
            MandegarPrice::updateOrCreate(
                ['product_id' => $item['id']],
                ['price'      => $price]
            );

            // ثبت تاریخچه تغییر قیمت
            \App\Models\PriceHistory::create([
                'user_id'           => auth()->id(),
                'product_id'        => $item['id'],
                'price_field'       => 'لیست قیمت ماندگار',
                'price_amount_from' => $previousPrice,
                'price_amount_to'   => $price,
            ]);
        }

        // ثبت فعالیت به‌روزرسانی قیمت
        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'به‌روزرسانی قیمت',
            'description' => "کاربر " . auth()->user()->family . '(' . auth()->user()->role->label . ')' . " قیمت(های) محصولات ماندگار پارس را به‌روزرسانی کرد",
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'قیمت‌ها با موفقیت به‌روزرسانی شدند!',
        ]);
    }


    public function MandegarPriceDelete(Request $request)
    {
        $productId = $request->product_id;
        MandegarPrice::where('product_id', $productId)->delete();

        // ثبت فعالیت حذف محصول
        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'حذف محصول',
            'description' => "کاربر " . auth()->user()->family . '(' . auth()->user()->role->label . ')' . " محصول با شناسه {$productId} را از لیست قیمت‌های ماندگار پارس حذف کرد",
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'محصول حذف شد.',
        ]);
    }

    public function getDetails($id)
    {
        $product = Product::with(['category', 'productModels'])->find($id);

        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'مشاهده جزئیات محصول',
            'description' => "کاربر " . auth()->user()->family . '(' . auth()->user()->role->label . ')' . " جزئیات محصول با شناسه {$id} را مشاهده کرد",
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'category' => $product->category->name ?? '--',
                'brand'    => $product->productModels->name ?? '--'
            ]
        ]);
    }

    public function updateOrder(Request $request)
    {
        $items = $request->input('items');
        foreach ($items as $item) {
            MandegarPrice::where('product_id', $item['id'])
                ->update(['order' => $item['order']]);
        }

        // ثبت فعالیت به‌روزرسانی ترتیب
        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'آپدیت ترتیب کالا',
            'description' => "کاربر " . auth()->user()->family . '(' . auth()->user()->role->label . ')' . " ترتیب کالای محصولات ماندگار پارس را به‌روزرسانی کرد",
        ]);

        return response()->json(['status' => 'success']);
    }

    public function downloadPDF(Request $request)
    {
        $products = MandegarPrice::with('product.category', 'product.productModels')
            ->orderBy('order')
            ->get();

        if ($products->isEmpty()) {
            return back()->withErrors(['message' => 'محصولی برای دانلود PDF یافت نشد.']);
        }

        $pdf = PDF::loadView('panel.pdf.prices', ['data' => $products], [], [
            'format'         => 'A3',
            'orientation'    => 'L',
            'margin_left'    => 2,
            'margin_right'   => 2,
            'margin_top'     => 2,
            'margin_bottom'  => 0,
        ]);

        // ثبت فعالیت دانلود PDF
        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'دانلود PDF',
            'description' => "کاربر " . auth()->user()->family . '(' . auth()->user()->role->label . ')' . " فایل PDF مربوط به لیست قیمت‌های ماندگار پارس را دانلود کرد",
        ]);

        return $pdf->stream("mandegar_prices.pdf");
    }

    public function search(Request $request)
    {
        $query = MandegarPrice::query();

        // فیلتر بر اساس دسته‌بندی
        if ($request->filled('category')) {
            $query->whereHas('product.category', function ($q) use ($request) {
                $q->where('id', $request->category);
            });
        }

        // فیلتر بر اساس برند
        if ($request->filled('brand')) {
            $query->whereHas('product.productModels', function ($q) use ($request) {
                $q->where('id', $request->brand);
            });
        }

        // فیلتر بر اساس محصول (در صورتی که انتخاب 'all' نباشد)
        if ($request->filled('product') && $request->product !== 'all') {
            $query->where('product_id', $request->product);
        }

        // تعداد رکورد در هر صفحه
        $perPage = $request->get('per_page', 10);
        $mandegar_price = $query->paginate($perPage);

        // ثبت فعالیت جستجو
        Activity::create([
            'user_id'     => auth()->id(),
            'action'      => 'جستجو',
            'description' => "کاربر " . auth()->user()->family . '(' . auth()->user()->role->label . ')' . " جستجویی در لیست قیمت‌های ماندگار پارس انجام داد",
        ]);

        return view('panel.prices.mandegarprice', compact('mandegar_price'));
    }
}

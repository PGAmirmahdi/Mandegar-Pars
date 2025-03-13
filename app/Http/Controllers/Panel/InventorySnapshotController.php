<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\InventorySnapshot;
use Illuminate\Http\Request;
use Verta;

class InventorySnapshotController extends Controller
{
    public function index()
    {
        // دریافت snapshot های انبار (بدون فیلتر)
        $snap_shots = InventorySnapshot::paginate(30);
        $total_count = InventorySnapshot::query()->sum('stock_count');

        $snap_shots_grouped = $snap_shots->groupBy(function ($snapshot) {
            return Verta::parse($snapshot->snapshot_date)->format('n'); // 'n' عدد ماه بدون صفر
        })->sortKeysDesc();

        $monthNames = [
            1  => 'فروردین',
            2  => 'اردیبهشت',
            3  => 'خرداد',
            4  => 'تیر',
            5  => 'مرداد',
            6  => 'شهریور',
            7  => 'مهر',
            8  => 'آبان',
            9  => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];

        // داده‌های نمودار: گروه‌بندی موجودی‌ها بر اساس انبار
        $chartData = InventorySnapshot::select('warehouse_id')
            ->selectRaw('SUM(stock_count) as total_inventory')
            ->groupBy('warehouse_id')
            ->with('warehouse')
            ->get()
            ->map(function ($item) {
                return [
                    'warehouse_name' => $item->warehouse->name,
                    'total_inventory' => (int)$item->total_inventory,
                ];
            });

        return view('panel.inventory.snap-shot.index', compact('monthNames', 'snap_shots', 'snap_shots_grouped', 'total_count', 'chartData'));
    }

    public function search(Request $request)
    {
        $query = InventorySnapshot::query()->with(['product.category', 'product.productModels', 'warehouse']);

        // فیلتر بر اساس دسته‌بندی کالا
        if ($request->filled('category')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        // فیلتر بر اساس برند کالا
        if ($request->filled('brand')) {
            $query->whereHas('product.productModels', function ($q) use ($request) {
                $q->where('id', $request->brand);
            });
        }

        // فیلتر بر اساس انبار
        if ($request->filled('warehouse') && $request->warehouse !== 'all') {
            $query->where('warehouse_id', $request->warehouse);
        }

        // فیلتر بر اساس محصول (اگر "all" انتخاب نشده باشد)
        if ($request->filled('product') && $request->product !== 'all') {
            $query->where('product_id', $request->product);
        }

        // فیلتر بر اساس بازه تاریخ (به فرض "YYYY/MM/DD")
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start_date = $request->start_date; // مثلاً "1403/09/22"
            $end_date   = $request->end_date;   // مثلاً "1403/09/30"
            $query->whereBetween('snapshot_date', [$start_date, $end_date]);
        }

        // محاسبه مجموع موجودی با استفاده از clone برای جلوگیری از محدودیت‌های paginate
        $total_count = (clone $query)->sum('stock_count');

        // دریافت نتایج صفحه‌بندی شده
        $snap_shots = $query->paginate(30);

        $snap_shots_grouped = $snap_shots->groupBy(function ($snapshot) {
            return Verta::parse($snapshot->snapshot_date)->format('n');
        })->sortKeysDesc();

        $monthNames = [
            1  => 'فروردین',
            2  => 'اردیبهشت',
            3  => 'خرداد',
            4  => 'تیر',
            5  => 'مرداد',
            6  => 'شهریور',
            7  => 'مهر',
            8  => 'آبان',
            9  => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];

        // داده‌های نمودار بر اساس نتایج فیلتر شده
        $chartData = (clone $query)
            ->select('warehouse_id')
            ->selectRaw('SUM(stock_count) as total_inventory')
            ->groupBy('warehouse_id')
            ->with('warehouse')
            ->get()
            ->map(function ($item) {
                return [
                    'warehouse_name' => $item->warehouse->name,
                    'total_inventory' => (int)$item->total_inventory,
                ];
            });

        return view('panel.inventory.snap-shot.index', compact('monthNames', 'snap_shots', 'snap_shots_grouped', 'total_count', 'chartData'));
    }
}

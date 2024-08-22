<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Role;
use App\Models\Sms;
use App\Models\User;
use App\Models\UserVisit;
use App\Models\Visitor;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class PanelController extends Controller
{
    public function index(Request $request)
    {
        $from_date = $request->from_date
            ? Verta::parse($request->from_date)->toCarbon()->toDateTimeString()
            : Invoice::orderBy('created_at')->first()->created_at;

        $to_date = $request->to_date
            ? Verta::parse($request->to_date)->endDay()->toCarbon()->toDateTimeString()
            : Invoice::orderBy('created_at', 'desc')->first()->created_at;

        // invoices
        $invoices1 = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])
            ->whereHas('products', function ($query) {
                $query->select('products.id', 'invoice_product.invoice_net');
            })
            ->where('status', 'pending')
            ->join('invoice_product', 'invoices.id', '=', 'invoice_product.invoice_id')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(invoice_product.invoice_net) as amount'))
            ->get();

        $invoices2 = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])
            ->whereHas('other_products', function ($query) {
                $query->select('other_products.invoice_net');
            })
            ->where('status', 'pending')
            ->join('other_products', 'invoices.id', '=', 'other_products.invoice_id')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(other_products.invoice_net) as amount'))
            ->get();

        // merge same province invoices and sum it amounts
        $invoices = collect();
        $invoices = $invoices->merge($invoices1);

        $invoices2->each(function ($item) use ($invoices) {
            $existingInvoice = $invoices->firstWhere('province', $item->province);
            if ($existingInvoice) {
                $existingInvoice->amount += $item->amount;
            } else {
                $invoices->push($item);
            }
        });

        // final discount
        $invoices_discounts = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])
            ->where('status', 'pending')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(invoices.discount) as discount'))
            ->get();

        foreach ($invoices as $key => $invoice) {
            $invoices[$key]->amount -= $invoices_discounts->where('province', $invoice->province)->first()->discount;
        }

        // factors
        $factors1 = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])
            ->whereHas('products', function ($query) {
                $query->select('products.id', 'invoice_product.invoice_net');
            })
            ->where('status', 'invoiced')
            ->join('invoice_product', 'invoices.id', '=', 'invoice_product.invoice_id')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(invoice_product.invoice_net) as amount'))
            ->get(['province', 'amount']);

        $factors2 = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])
            ->whereHas('other_products', function ($query) {
                $query->select('other_products.invoice_net');
            })
            ->where('status', 'invoiced')
            ->join('other_products', 'invoices.id', '=', 'other_products.invoice_id')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(other_products.invoice_net) as amount'))
            ->get();

        // merge same province factors and sum it amounts
        $factors = collect();
        $factors = $factors->merge($factors1);

        $factors2->each(function ($item) use ($factors) {
            $existingInvoice = $factors->firstWhere('province', $item->province);
            if ($existingInvoice) {
                $existingInvoice->amount += $item->amount;
            } else {
                $factors->push($item);
            }
        });

        // final discount
        $factors_discounts = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])
            ->where('status', 'invoiced')
            ->groupBy('province')
            ->select('province', DB::raw('SUM(invoices.discount) as discount'))
            ->get();

        foreach ($factors as $key => $factor) {
            $factors[$key]->amount -= $factors_discounts->where('province', $factor->province)->first()->discount;
        }

        $factors_monthly = $this->getFactorsMonthly();

        // آمار بازدید کاربران
        $from_date2 = $request->from_date
            ? Verta::parse($request->from_date)->toCarbon()->startOfDay()
            : UserVisit::orderBy('created_at')->first()->created_at;

        $to_date2 = $request->to_date
            ? Verta::parse($request->to_date)->toCarbon()->endOfDay()
            : UserVisit::orderBy('created_at', 'desc')->first()->created_at;

// آمار بازدید کاربران به صورت روزانه
        $userVisits = UserVisit::whereBetween('created_at', [$from_date2, $to_date2])
            ->groupBy(DB::raw("DATE(created_at)"))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as visits'))
            ->orderBy('date', 'asc')
            ->get();

// تبدیل تاریخ‌ها به شمسی
        $userVisits = $userVisits->map(function ($visit) {
            $shamsiDate = Verta::instance($visit->date)->format('Y/m/d');
            return [
                'date' => $shamsiDate,
                'visits' => $visit->visits
            ];
        });

        $totalVisits = $userVisits->sum('visits');
        $users = UserVisit::latest()->paginate(10);

        $from_date3 = $request->from_date
            ? Verta::parse($request->from_date)->toCarbon()->startOfDay()
            : Sms::orderBy('created_at')->first()->created_at;

        $to_date3 = $request->to_date
            ? Verta::parse($request->to_date)->toCarbon()->endOfDay()
            : Sms::orderBy('created_at', 'desc')->first()->created_at;

// آمار SMS‌های ارسال شده به صورت روزانه
        $smsData = Sms::whereBetween('created_at', [$from_date3, $to_date3])
            ->groupBy(DB::raw("DATE(created_at)"))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as sms_count'))
            ->orderBy('date', 'asc')
            ->get();

// تبدیل تاریخ‌ها به شمسی
        $smsData = collect($smsData)->map(function ($sms) {
            $shamsiDate = Verta::instance($sms->date)->format('Y/m/d');
            return [
                'date' => $shamsiDate,
                'sms_count' => $sms->sms_count
            ];
        });

        $sms_dates = $smsData->pluck('date');
        $sms_counts = $smsData->pluck('sms_count');
        $totalSmsSent = $smsData->sum('sms_count');

        $from_date4 = $request->from_date
            ? Carbon::parse($request->from_date)->startOfDay()
            : Sms::orderBy('created_at')->first()->created_at;

        $to_date4 = $request->to_date
            ? Carbon::parse($request->to_date)->endOfDay()
            : Carbon::now()->endOfDay();  // تنظیم به تاریخ و زمان حال

        // دریافت آمار پیامک‌های ارسال شده بر اساس تاریخ و کاربر
        $smsData = Sms::whereBetween('created_at', [$from_date4, $to_date4])
            ->groupBy('user_id', DB::raw('DATE(created_at)'))
            ->select('user_id', DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as sms_count'))
            ->orderBy('user_id')
            ->orderBy('date')
            ->get();

        // ایجاد آرایه‌ای از تمام تاریخ‌ها بین from_date و to_date به فرمت میلادی
        $allDates = [];
        $currentDate = $from_date4->copy();
        $endDate4 = $to_date4;
        while ($currentDate->lte($endDate4)) {
            $allDates[] = $currentDate->format('Y-m-d'); // تاریخ میلادی
            $currentDate->addDay();
        }

        // گروه‌بندی داده‌های SMS بر اساس کاربر
        $userSmsData = $smsData->groupBy('user_id');

        // آماده‌سازی داده‌ها برای نمودار
        $datasets = [];
        $labels = array_map(function($date) {
            return Jalalian::fromCarbon(Carbon::parse($date))->format('Y/m/d'); // تبدیل تاریخ میلادی به شمسی
        }, $allDates);

        // تابعی برای تولید رنگ ثابت بر اساس id کاربر
        function generateColor($id)
        {
            srand($id);  // استفاده از ID کاربر برای تولید رنگ یکتا
            return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }

        foreach ($userSmsData as $userId => $userData) {
            $user = User::find($userId);
            if ($user) {
                $userLabel = $user->fullName(); // تغییر به name یا family در صورت نیاز
            } else {
                $userLabel = "ناشناس"; // یا هر مقدار پیش‌فرض دیگری
            }

            // پر کردن داده‌ها برای تاریخ‌هایی که مقدار ندارند
            $data = [];
            foreach ($allDates as $date) {
                $data[] = isset($userData->where('date', $date)->first()->sms_count) ? $userData->where('date', $date)->first()->sms_count : 0;
            }

            $datasets[] = [
                'label' => $userLabel,
                'data' => $data,
                'backgroundColor' => generateColor($userId),
                'borderColor' => generateColor($userId),
                'borderWidth' => 1
            ];
        }
        $from_date5 = $request->from_date
            ? Carbon::parse($request->from_date)->startOfDay()
            : Sms::orderBy('created_at')->first()->created_at;

        $to_date5 = $request->to_date
            ? Carbon::parse($request->to_date)->endOfDay()
            : Carbon::now()->endOfDay();

// دریافت آمار پیامک‌های ارسال شده بر اساس تاریخ و کاربر
        $smsData = Sms::whereBetween('created_at', [$from_date5, $to_date5])
            ->groupBy('user_id')
            ->select('user_id', DB::raw('COUNT(*) as sms_count'), DB::raw('MAX(created_at) as last_sent_at'))
            ->paginate(10); // اضافه کردن صفحه‌بندی

// تابع دستی برای تبدیل تاریخ میلادی به شمسی
        function gregorianToJalali($g_y, $g_m, $g_d) {
            $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
            $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

            $gy = $g_y - 1600;
            $gm = $g_m - 1;
            $gd = $g_d - 1;

            $g_day_no = 365 * $gy + intdiv($gy + 3, 4) - intdiv($gy + 99, 100) + intdiv($gy + 399, 400);

            for ($i = 0; $i < $gm; ++$i) {
                $g_day_no += $g_days_in_month[$i];
            }

            if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0))) {
                $g_day_no++;
            }

            $g_day_no += $gd;

            $j_day_no = $g_day_no - 79;

            $j_np = intdiv($j_day_no, 12053);
            $j_day_no %= 12053;

            $jy = 979 + 33 * $j_np + 4 * intdiv($j_day_no, 1461);

            $j_day_no %= 1461;

            if ($j_day_no >= 366) {
                $jy += intdiv($j_day_no - 1, 365);
                $j_day_no = ($j_day_no - 1) % 365;
            }

            for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) {
                $j_day_no -= $j_days_in_month[$i];
            }

            $jm = $i + 1;
            $jd = $j_day_no + 1;

            return [$jy, $jm, $jd];
        }

// تبدیل تاریخ‌ها به شمسی
        $smsData->getCollection()->transform(function ($item) {
            $lastSentDate = Carbon::parse($item->last_sent_at);

            // تبدیل تاریخ میلادی به شمسی
            list($jy, $jm, $jd) = gregorianToJalali($lastSentDate->year, $lastSentDate->month, $lastSentDate->day);
            $persianDate = $jy . '/' . str_pad($jm, 2, '0', STR_PAD_LEFT) . '/' . str_pad($jd, 2, '0', STR_PAD_LEFT);

            return [
                'user_id' => $item->user_id,
                'sms_count' => $item->sms_count,
                'last_sent_at' => $persianDate // تاریخ آخرین ارسال به شمسی
            ];
        });

        // دریافت اطلاعات کاربران
        $userIds = $smsData->pluck('user_id');
        $users2 = User::whereIn('id', $userIds)->get();

// ایجاد آرایه‌ای برای نام کاربران برای استفاده در ویو
        $userNames = $users2->keyBy('id')->map(function ($user2) {
            return $user2->fullName(); // یا $user->name
        })->toArray();
        $from_date6 = $request->from_date
            ? Verta::parse($request->from_date)->toCarbon()->startOfDay()
            : \Carbon\Carbon::minValue();

        $to_date6 = $request->to_date
            ? Verta::parse($request->to_date)->toCarbon()->endOfDay()
            : \Carbon\Carbon::maxValue();

        // دریافت داده‌ها از جدول invoice_product با استفاده از رابطه belongsToMany
        $productOrders = \App\Models\Invoice::whereBetween('created_at', [$from_date6, $to_date6])
            ->with('products')  // بارگذاری محصولات مرتبط
            ->get()
            ->flatMap(function ($invoice) {
                return $invoice->products;
            })
            ->groupBy('id')
            ->map(function ($products) {
                return $products->sum('pivot.count');
            });

        // آماده‌سازی داده‌ها برای نمودار
        $productNames = \App\Models\Product::whereIn('id', $productOrders->keys())->pluck('title');  // نام محصولات
        $orderCounts = $productOrders->values();  // تعداد سفارشات

        $from_date7 = $request->from_date
            ? Verta::parse($request->from_date)->toCarbon()->startOfDay()
            : \Carbon\Carbon::minValue();

        $to_date7 = $request->to_date
            ? Verta::parse($request->to_date)->toCarbon()->endOfDay()
            : \Carbon\Carbon::maxValue();

        // دریافت داده‌ها از جدول invoices با استفاده از رابطه belongsTo
        $customerOrders = \App\Models\Invoice::whereBetween('created_at', [$from_date7, $to_date7])
            ->with('customer')  // بارگذاری مشتریان مرتبط
            ->get()
            ->groupBy('customer_id')
            ->map(function ($invoices) {
                return $invoices->count();
            });

        // آماده‌سازی داده‌ها برای نمودار
        $customerNames = \App\Models\Customer::whereIn('id', $customerOrders->keys())->pluck('name');  // نام مشتریان
        $orderCounts2 = $customerOrders->values();  // تعداد سفارشات
// داده‌های نمودار را به نمای (view) ارسال کنید
        $labels = collect($labels)->map(function ($label) {
            return Verta::parse($label)->format('Y/m/d');
        });
        // دریافت اطلاعات مربوط به موجودی هر انبار
    $inventories = Inventory::rightJoin('warehouses', 'inventories.warehouse_id', '=', 'warehouses.id')
        ->select('warehouses.name as warehouse_name', DB::raw('COALESCE(SUM(inventories.current_count), 0) as total_inventory'))
        ->groupBy('warehouses.id', 'warehouses.name')
        ->get();

        $products = \App\Models\Product::all();  // دریافت تمامی محصولات

// آماده‌سازی داده‌ها برای نمودار
        $productNames = $products->pluck('title');  // نام محصولات
        $productCounts = $products->pluck('total_count');  // مقدار موجودی هر محصول
        // تاریخ امروز
        $today = Carbon::today();
        // تاریخ 15 روز پیش
        $fifteenDaysAgo = $today->subDays(15);

        // دریافت تعداد بازدیدها به ازای هر روز در 15 روز اخیر
        $visitsData = Visitor::whereBetween('created_at', [$fifteenDaysAgo, Carbon::now()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'), 'asc')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as visits')
            ]);


        // تبدیل تاریخ‌ها و داده‌ها به فرمت مناسب برای استفاده در ویو
        $visitsDates = [];
        $visitsCounts = [];

        foreach ($visitsData as $data) {
            $visitsDates[] = Verta::instance($data->date)->format('Y/m/d'); // تبدیل تاریخ میلادی به شمسی
            $visitsCounts[] = $data->visits;
        }

        // مجموع بازدیدها
            $totalVisits2 = array_sum($visitsCounts);
        $visitsData = Visitor::selectRaw('city, DATE(created_at) as date, COUNT(*) as visits')
            ->groupBy('city', 'date')
            ->orderBy('date', 'asc')
            ->get();
        $today2 = Carbon::today();
        $fifteenDaysAgo2 = $today2->subDays(15);

        // دریافت تعداد بازدیدها به ازای هر روز در 15 روز اخیر
        $visitsData3 = Visitor::whereBetween('created_at', [$fifteenDaysAgo2, Carbon::now()])
            ->groupBy(DB::raw('DATE(created_at)'), 'city')
            ->orderBy(DB::raw('DATE(created_at)'), 'asc')
            ->get([
                DB::raw('DATE(created_at) as date'),
                'city',
                DB::raw('COUNT(*) as visits')
            ]);

        // گروه‌بندی بر اساس شهرها
        $cities = $visitsData3->groupBy('city');

        // آماده‌سازی داده‌ها برای نمودار
        $citiesData = [];
        $dates2 = array_unique($visitsData3->pluck('date')->toArray());
        foreach ($cities as $city => $data) {
            $citiesData[$city] = array_fill_keys($dates2, 0);
            foreach ($data as $item) {
                $citiesData[$city][$item->date] = $item->visits;
            }
        }

        $totalVisits3 = $visitsData3->sum('visits');
        return view('panel.index', [
            'labels' => $labels,
            'datasets' => $datasets,
            'smsData' => $smsData,
            'userNames' => $userNames,
            'productNames' => $productNames,
            'orderCounts' => $orderCounts,
            'customerNames' => $customerNames,
            'orderCounts2' => $orderCounts2,
            'citiesData' => $citiesData,
            'dates2' => $dates2,
            'totalVisits3' => $totalVisits3
        ], compact('invoices', 'factors', 'factors_monthly', 'userVisits', 'totalVisits', 'users', 'sms_dates', 'sms_counts', 'totalSmsSent','users2','inventories','productNames', 'productCounts','visitsDates', 'visitsCounts', 'totalVisits2','visitsData'));
    }
        public function readNotification($notification = null)
    {
        if ($notification == null) {
            auth()->user()->unreadNotifications->markAsRead();
            return back();
        }

        $notif = auth()->user()->unreadNotifications()->whereId($notification)->first();
        if (!$notif) {
            return back();
        }

        $notif->markAsRead();
        return redirect()->to($notif->data['url']);
    }

    public function login(Request $request)
    {
        if ($request->method() == 'GET') {
            $users = User::where('id', '!=', auth()->id())->whereIn('id', [3, 4, 152])->get(['id', 'name', 'family']);

            return view('panel.login', compact('users'));
        }

        Auth::loginUsingId($request->user);
        return redirect()->route('panel');
    }

    public function sendSMS(Request $request)
    {
        $result = sendSMS($request->bodyId, $request->phone, $request->args, ['text' => $request->text]);
        return $result;
    }

    public function najva_token_store(Request $request)
    {
        \auth()->user()->update([
            'najva_token' => $request->najva_user_token
        ]);

        return response()->json(['data' => 'your token stored: ' . $request->najva_user_token]);
    }

    public function saveFCMToken(Request $request)
    {
        auth()->user()->update(['fcm_token' => $request->token]);
        return response()->json(['token saved successfully.']);
    }

    private function getFactorsMonthly()
    {
        $factors = [
            'فروردین' => 0,
            'اردیبهشت' => 0,
            'خرداد' => 0,
            'تیر' => 0,
            'مرداد' => 0,
            'شهریور' => 0,
            'مهر' => 0,
            'آبان' => 0,
            'آذر' => 0,
            'دی' => 0,
            'بهمن' => 0,
            'اسفند' => 0,
        ];

        for ($i = 1; $i <= 12; $i++) {
            $from_date = \verta()->month($i)->startMonth()->toCarbon()->toDateTimeString();
            $to_date = \verta()->month($i)->endMonth()->toCarbon()->toDateTimeString();

            // factors
            $factors1 = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])->whereHas('products', function ($query) {
                $query->select('products.id', 'invoice_product.invoice_net');
            })->where('status', 'invoiced')
                ->join('invoice_product', 'invoices.id', '=', 'invoice_product.invoice_id')
                ->groupBy('province')
                ->select('province', DB::raw('SUM(invoice_product.invoice_net) as amount'))
                ->get(['amount']);

            // factors
            $factors2 = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])->whereHas('other_products', function ($query) {
                $query->select('other_products.invoice_net');
            })->where('status', 'invoiced')
                ->join('other_products', 'invoices.id', '=', 'other_products.invoice_id')
                ->groupBy('province')
                ->select('province', DB::raw('SUM(other_products.invoice_net) as amount'))
                ->get(['amount']);

            $month = \verta()->month($i)->format('%B');

            foreach ($factors1 as $item) {
                $factors[$month] += $item->amount;
            }
            foreach ($factors2 as $item) {
                $factors[$month] += $item->amount;
            }

            $factors_discounts_amount = Invoice::whereBetween('invoices.created_at', [$from_date, $to_date])->where('status', 'invoiced')->sum('discount');
            $factors[$month] -= $factors_discounts_amount;
        }

        return collect($factors);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Role;
use App\Models\Sms;
use App\Models\User;
use App\Models\UserVisit;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $smsData = $smsData->map(function ($sms) {
            $shamsiDate = Verta::instance($sms->date)->format('Y/m/d');
            return [
                'date' => $shamsiDate,
                'sms_count' => $sms->sms_count
            ];
        });

        $sms_dates = $smsData->pluck('date');
        $sms_counts = $smsData->pluck('sms_count');
        $totalSmsSent = $smsData->sum('sms_count');

        // تبدیل تاریخ ورودی به شمسی
        $from_date4 = $request->from_date
            ? Verta::parse($request->from_date)->toCarbon()->startOfDay()
            : Sms::orderBy('created_at')->first()->created_at;

        $to_date4 = $request->to_date
            ? Verta::parse($request->to_date)->toCarbon()->endOfDay()
            : Sms::orderBy('created_at', 'desc')->first()->created_at;

        // تبدیل تاریخ‌ها به شمسی برای استفاده در نمودار
        $from_date4 = Verta::instance($from_date4)->format('Y-m-d');
        $to_date4 = Verta::instance($to_date4)->format('Y-m-d');

        // دریافت آمار ارسال SMS برای هر کاربر
        $smsData = Sms::whereBetween('created_at', [$from_date4, $to_date4])
            ->groupBy('user_id', DB::raw('DATE(created_at)'))
            ->select('user_id', DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as sms_count'))
            ->orderBy('user_id')
            ->orderBy('date')
            ->get();

        // ایجاد آرایه‌ای از تمام تاریخ‌ها بین from_date و to_date به شمسی
        $allDates = [];
        $currentDate = Verta::parse($from_date4);
        $to_date4 = Verta::parse($to_date4);

        while ($currentDate->lte($to_date4)) {
            $allDates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        // گروه‌بندی داده‌های SMS بر اساس کاربر
        $userSmsData = $smsData->groupBy('user_id');

        // آماده‌سازی داده‌ها برای نمودار
        $datasets = [];
        $labels = $allDates;

        // تابعی برای تولید رنگ ثابت بر اساس id کاربر
        function generateColor($id) {
            srand($id);  // استفاده از ID کاربر برای تولید رنگ یکتا
            return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }

        foreach ($userSmsData as $userId => $userData) {
            $user = User::find($userId);
            if ($user) {
                $userLabel = $user->fullName();

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
        }
        return view('panel.index', compact('invoices', 'factors', 'factors_monthly', 'userVisits', 'totalVisits', 'users', 'sms_dates', 'sms_counts', 'totalSmsSent', 'datasets', 'labels'));
    }
    private function getRandomColor()
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
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

<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ExchangeController extends Controller
{
    public function index()
    {
        $this->authorize('exchange-list');
        $apiKey = env('EXCHANGE_API_KEY');
        $url = "http://api.navasan.tech/latest/?api_key={$apiKey}";
        $response = Http::get($url);
        $currenciesMapping = [
            'sekkeh'                   => 'سکه امامی',
            'abshodeh'                 => 'آبشده (مثقال طلا)',
            'usd_farda_buy'            => 'دلار تهران فردایی خرید',
            'usd_farda_sell'           => 'دلار تهران فردایی فروش',
            'aed_sell'                 => 'درهم دبی فروش',
            'usd_sell'                 => 'دلار تهران فروش',
            'usd_buy'                  => 'دلار تهران خرید',
            '18ayar'                   => 'هر گرم طلای 18 عیار',
            'dirham_dubai'             => 'درهم دبی فروش',
            'harat_naghdi_buy'         => 'دلار هرات خرید',
            'harat_naghdi_sell'        => 'دلار هرات فروش',
            'dolar_harat_sell'         => 'دلار هرات فروش نقد',
            'bahar'                    => 'سکه بهار آزادی',
            'nim'                      => 'سکه نیم',
            'rob'                      => 'سکه ربع',
            'gerami'                   => 'سکه گرمی',
            'dolar_soleimanie_sell'    => 'دلار سلیمانیه',
            'dolar_kordestan_sell'     => 'دلار کردستان',
            'dolar_mashad_sell'        => 'دلار مشهد',
        ];
        // بررسی وضعیت ریسپانس
        if ($response->successful()) {
            $data = $response->json(); // تبدیل ریسپانس به آرایه
        } else {
            $data = []; // اگر خطا داشتید، داده‌ها خالی باشد
        }
        return view('panel.exchange.index', compact('data','currenciesMapping'));
    }

    public function showDetails(Request $request)
    {
        $item = $request->route('item');
        // دریافت تاریخ‌های انتخاب‌شده از فرم (با تبدیل "/" به "-" در صورت نیاز)
        $startDateInput = $request->query('start_date', null);
        $endDateInput   = $request->query('end_date', null);
        $startDate = $startDateInput ? str_replace('/', '-', $startDateInput) : verta()->subYear()->format('Y-m-d');
        $endDate   = $endDateInput   ? str_replace('/', '-', $endDateInput)   : verta()->format('Y-m-d');

        // دریافت نوع گروه‌بندی: daily, weekly، یا monthly
        $groupBy = $request->query('group_by', 'daily');

        $apikey = env('EXCHANGE_API_KEY');

        // درخواست به API
        $response = Http::get("http://api.navasan.tech/ohlcSearch/", [
            'api_key' => $apikey,
            'item'    => $item,
            'start'   => $startDate,
            'end'     => $endDate,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            usort($data, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            // گروه‌بندی داده‌ها در صورت انتخاب هفتگی یا ماهانه
            if ($groupBy !== 'daily') {
                $collection = collect($data);

                if ($groupBy === 'weekly') {
                    // گروه‌بندی بر اساس هفته (با فرمت Year-Week)
                    $grouped = $collection->groupBy(function ($entry) {
                        return Carbon::parse($entry['date'])->format('o-W');
                    });
                    $data = $grouped->map(function ($weekData) {
                        $weekData = $weekData->sortBy('date')->values();
                        $first = $weekData->first();
                        $last  = $weekData->last();
                        return [
                            'date'  => $first['date'] . ' تا تاریخ ' . $last['date'],
                            'open'  => $first['open'],
                            'close' => $last['close'],
                        ];
                    })->values()->all();
                } elseif ($groupBy === 'monthly') {
                    $grouped = $collection->groupBy(function ($entry) {
                        return Carbon::parse($entry['date'])->format('Y-m');
                    });
                    // آرایه نگاشت شماره ماه به نام ماه فارسی
                    $persianMonths = [
                        '01' => 'فروردین',
                        '02' => 'اردیبهشت',
                        '03' => 'خرداد',
                        '04' => 'تیر',
                        '05' => 'مرداد',
                        '06' => 'شهریور',
                        '07' => 'مهر',
                        '08' => 'آبان',
                        '09' => 'آذر',
                        '10' => 'دی',
                        '11' => 'بهمن',
                        '12' => 'اسفند',
                    ];
                    $data = $grouped->map(function ($monthData, $key) use ($persianMonths) {
                        $parts = explode('-', $key);
                        $year = $parts[0];
                        $monthNumber = $parts[1];
                        $monthName = $persianMonths[$monthNumber] ?? $monthNumber;

                        $monthData = $monthData->sortBy('date')->values();
                        $first = $monthData->first();
                        $last  = $monthData->last();
                        // نمایش فقط نام ماه به همراه سال
                        return [
                            'date'  => $monthName . ' ' . $year,
                            'open'  => $first['open'],
                            'close' => $last['close'],
                        ];
                    })->values()->all();
                }
            }

        } else {
            $data = [];
        }

        $currenciesMapping = [
            'sekkeh'                   => 'سکه امامی',
            'abshodeh'                 => 'آبشده (مثقال طلا)',
            'usd_farda_buy'            => 'دلار تهران فردایی خرید',
            'usd_farda_sell'           => 'دلار تهران فردایی فروش',
            'aed_sell'                 => 'درهم دبی فروش',
            'usd_sell'                 => 'دلار تهران فروش',
            'usd_buy'                  => 'دلار تهران خرید',
            '18ayar'                   => 'هر گرم طلای 18 عیار',
            'dirham_dubai'             => 'درهم دبی فروش',
            'harat_naghdi_buy'         => 'دلار هرات خرید',
            'harat_naghdi_sell'        => 'دلار هرات فروش',
            'dolar_harat_sell'         => 'دلار هرات فروش نقد',
            'bahar'                    => 'سکه بهار آزادی',
            'nim'                      => 'سکه نیم',
            'rob'                      => 'سکه ربع',
            'gerami'                   => 'سکه گرمی',
            'dolar_soleimanie_sell'    => 'دلار سلیمانیه',
            'dolar_kordestan_sell'     => 'دلار کردستان',
            'dolar_mashad_sell'        => 'دلار مشهد',
        ];

        return view('panel.exchange.details', compact('data', 'item', 'currenciesMapping', 'startDate', 'endDate', 'groupBy'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

@extends('panel.layouts.master')
@section('title', 'نرخ ارزها')
@section('content')
    <div class="container-fluid p-0">
        <div class="card border-0">
            <div class="card-body p-0">
                <div class="card-title d-flex justify-content-between align-items-center p-3">
                    <h1>نرخ ارزها</h1>
                </div>

                <!-- فرم جستجو جهت هدایت به صفحه OHLC -->
                @php
                    // نگاشت ارزها (می‌توانید از همین آرایه استفاده کنید)
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
                @endphp

                <div class="p-3">
                    <form method="GET" action="{{ route('exchange.ohlc') }}">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label for="item" class="form-label">انتخاب ارز:</label>
                                <select name="item" id="item" class="form-control">
                                    @foreach($currenciesMapping as $key => $persianName)
                                        <option value="{{ $key }}">{{ $persianName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="start" class="form-label">تاریخ شروع:</label>
                                <input type="text" name="start" id="start" class="form-control" value="1398-05-17">
                            </div>
                            <div class="col-md-3">
                                <label for="end" class="form-label">تاریخ پایان:</label>
                                <input type="text" name="end" id="end" class="form-control" value="1398-06-17">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">جستجو</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ادامه‌ی نمایش جدول نرخ ارزها -->
                @php
                    // فیلتر کردن ارزهای موجود در داده‌های API بر اساس mapping
                    $filteredCurrencies = [];
                    foreach ($currenciesMapping as $key => $persianName) {
                        if (isset($data[$key])) {
                            $filteredCurrencies[] = [
                                'key'       => $key,
                                'name'      => $persianName,
                                'value'     => $data[$key]['value'] ?? 0,
                                'change'    => $data[$key]['change'] ?? 0,
                                'timestamp' => $data[$key]['timestamp'] ?? 0,
                                'date'      => $data[$key]['date'] ?? 'N/A',
                            ];
                        }
                    }

                    // مرتب‌سازی ارزها بر اساس timestamp به ترتیب نزولی (از جدیدترین به قدیمی‌ترین)
                    usort($filteredCurrencies, function($a, $b) {
                        return $b['timestamp'] <=> $a['timestamp'];
                    });
                @endphp

                <div class="overflow-auto" style="width: 100%;">
                    <table class="table table-striped table-bordered dataTable dtr-inline text-center w-100">
                        <thead>
                        <tr>
                            <th>نام ارز</th>
                            <th>قیمت</th>
                            <th>تغییرات نسبت به روز گذشته</th>
                            <th>نرخ روز گذشته</th>
                            <th>تاریخ تغییر</th>
                            <th>جزئیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($filteredCurrencies as $currency)
                            @php
                                $yesterdayPrice = $currency['value'] - $currency['change'];
                            @endphp
                            <tr>
                                <td>{{ $currency['name'] }}</td>
                                <td>{{ number_format($currency['value']) }} تومان</td>
                                <td>
                                    @if ($currency['change'] > 0)
                                        <p class="text-success">{{ number_format($currency['change']) }}+ تومان</p>
                                    @elseif ($currency['change'] == 0)
                                        <p class="text-dark">{{ number_format($currency['change']) }} تومان</p>
                                    @else
                                        <p class="text-danger">{{ number_format($currency['change']) }}- تومان</p>
                                    @endif
                                </td>
                                <td>{{ number_format($yesterdayPrice) }} تومان</td>
                                <td>{{ $currency['date'] }}</td>
                                <td>
                                    <a href="{{ route('exchange.details', ['item' => $currency['key']]) }}" class="btn btn-info btn-sm">
                                        مشاهده جزئیات
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <!-- در صورت نیاز می‌توانید مجموع یا خلاصه‌ای در فوتر قرار دهید -->
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

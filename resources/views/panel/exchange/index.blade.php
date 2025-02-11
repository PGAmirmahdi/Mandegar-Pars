@extends('panel.layouts.master')
@section('title', 'نرخ ارزها')
@section('content')
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex justify-content-between align-items-center p-3">
                    <h1>نرخ ارزها</h1>
                </div>
                @php
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

                <div class="overflow-auto">
                    <table class="table table-striped table-bordered dataTable text-center">
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
                                        <p class="text-success">{{ number_format($currency['change']) }} <i class="fa fa-up-long text-success mr-2"></i> + تومان </p>
                                    @elseif ($currency['change'] == 0)
                                        <p class="text-dark">{{ number_format($currency['change']) }} تومان</p>
                                    @else
                                        <p class="text-danger">{{ number_format($currency['change']) }} تومان <i class="fa fa-down-long text-danger mr-2"></i> </p>
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
@endsection

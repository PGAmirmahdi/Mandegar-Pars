@extends('panel.layouts.master')
@section('title', 'جزئیات نرخ ارز')

@section('content')
        <div class="card">
            <div class="card-body">
                <h1 class="mb-4">جزئیات نرخ {{ $currenciesMapping[$item] ?? strtoupper($item) }}</h1>

                <!-- فرم فیلتر تاریخ و نوع نمایش -->
                <form method="GET" action="{{ route('exchange.details', ['item' => $item]) }}">
                    <div class="row">
                        <div class="col-xl-2 col-md-3">
                            <input type="text" class="form-control date-picker-shamsi-list" id="start_date" placeholder="تاریخ شروع"
                                   name="start_date"
                                   value="">
                        </div>
                        <div class="col-xl-2 col-md-3">
                            <input type="text" class="form-control date-picker-shamsi-list" id="end_date" placeholder="تاریخ پایان"
                                   name="end_date"
                                   value="">
                        </div>
                        <div class="col-xl-2 col-md-3">
                            <select name="group_by" id="group_by" class="form-control">
                                <option value="daily" {{ $groupBy == 'daily' ? 'selected' : '' }}>روزانه</option>
                                <option value="weekly" {{ $groupBy == 'weekly' ? 'selected' : '' }}>هفتگی</option>
                                <option value="monthly" {{ $groupBy == 'monthly' ? 'selected' : '' }}>ماهانه</option>
                            </select>
                        </div>
                        <div class="col-xl-3 col-md-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
                <hr>
                <!-- نمودار تغییرات قیمت -->
                <canvas id="priceChart"></canvas>
                <table class="table table-striped text-center">
                    <thead>
                    <tr>
                        <th>تاریخ</th>
                        <th>قیمت شروع</th>
                        <th>قیمت بسته شده</th>
                        <th>تغییر قیمت</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(array_reverse($data) as $entry)
                        <tr>
                            <td>
                                @if($entry['date'] == verta(now())->format('Y-m-d'))
                                    امروز
                                @else
                                    {{ $entry['date'] }}
                                @endif
                            </td>
                            <td>{{ number_format($entry['open']) }} تومان</td>
                            <td>{{ number_format($entry['close']) }} تومان</td>
                            @if(number_format($entry['close'] - $entry['open']) > 0)
                                <td class="text-success">
                                    <i class="fa fa-up-long text-success mr-2"></i> {{ number_format($entry['close'] - $entry['open']) }} + تومان
                                </td>
                            @elseif(number_format($entry['close'] - $entry['open']) < 0)
                                <td class="text-danger">
                                    <i class="fa fa-down-long text-danger mr-2"></i> {{ number_format($entry['close'] - $entry['open']) }} تومان
                                </td>
                            @else
                                <td class="text-primary">
                                    بدون تغییر
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    <!-- اضافه کردن کتابخانه Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById('priceChart').getContext('2d');
            var priceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_column($data, 'date')) !!},
                    datasets: [{
                        label: 'قیمت بسته‌شده',
                        data: {!! json_encode(array_column($data, 'close')) !!},
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0, 0, 255, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'تاریخ'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'قیمت (تومان)'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection

@extends('panel.layouts.master')
@section('title', 'پنل مدیریت')

@section('styles')
    <link rel="stylesheet" href="/assets/css/notes-styles.css">
    <style>
        #app_updates ul:not(.list-unstyled) li {
            list-style-type: disclosure-closed
        }

        #app_updates ul {
            line-height: 2rem;
        }
    </style>
@endsection
@section('content')
    @php
        $update = \App\Models\SoftwareUpdate::where('date', [now()->startOfDay(), now()->endOfDay()])->latest()->first();
    @endphp
    @if($update)
        <div class="alert alert-success alert-with-border alert-dismissible fade show mb-4 pr-3" id="app_updates">
            <div>
                <i class="ti-announcement d-inline m-r-10"></i>
                <h5 class="alert-heading d-inline">بروزرسانی نرم افزار - تاریخ
                    انتشار {{ verta($update->date)->format('Y/m/d') }} - نسخه{{ $update->version }}</h5>
            </div>
            <ul>
                @foreach(explode(',',$update->description) as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
            <hr>
            <small>برای مشاهده تغییرات پیشین می توانید به صفحه نسخه های برنامه مراجعه کنید</small>
        </div>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/moment-jalaali@latest/build/moment-jalaali.min.js"></script>
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>آمار</h6>
                <div class="slick-single-arrows">
                    <a class="btn btn-outline-light btn-sm">
                        <i class="ti-angle-right"></i>
                    </a>
                    <a class="btn btn-outline-light btn-sm">
                        <i class="ti-angle-left"></i>
                    </a>
                </div>
            </div>
            <div class="row slick-single-item">
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <div class="card border mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="icon-block icon-block-sm bg-success icon-block-floating mr-2">
                                        <i class="fa fa-users"></i>
                                    </div>
                                </div>
                                <span class="font-size-13">کاربران</span>
                                <h2 class="mb-0 ml-auto font-weight-bold text-success primary-font line-height-30">{{ \App\Models\User::count() }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <div class="card border mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="icon-block icon-block-sm bg-secondary icon-block-floating mr-2">
                                        <i class="fa fa-users"></i>
                                    </div>
                                </div>
                                <span class="font-size-13">مشتریان</span>
                                <h2 class="mb-0 ml-auto font-weight-bold text-secondary primary-font line-height-30">{{ \App\Models\Customer::count() }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <div class="card border mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="icon-block icon-block-sm bg-info icon-block-floating mr-2">
                                        <i class="fa fa-p"></i>
                                    </div>
                                </div>
                                <span class="font-size-13">محصولات</span>
                                <h2 class="mb-0 ml-auto font-weight-bold text-info primary-font line-height-30">{{ \App\Models\Product::count() }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <div class="card border mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="icon-block icon-block-sm bg-primary icon-block-floating mr-2">
                                        <i class="fa fa-shopping-cart"></i>
                                    </div>
                                </div>
                                <span class="font-size-13">سفارش مشتری</span>
                                <h2 class="mb-0 ml-auto font-weight-bold text-primary primary-font line-height-30">{{ \App\Models\Invoice::where('status','!=','invoiced')->count() }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                @can('accountant')
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                        <div class="card border mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div>
                                        <div class="icon-block icon-block-sm bg-primary icon-block-floating mr-2">
                                            <i class="fa fa-shopping-cart"></i>
                                        </div>
                                    </div>
                                    <span class="font-size-13">فاکتور</span>
                                    <h2 class="mb-0 ml-auto font-weight-bold text-primary primary-font line-height-30">{{ \App\Models\Invoice::where('status','invoiced')->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <div class="card border mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <div class="icon-block icon-block-sm bg-danger icon-block-floating mr-2">
                                        <i class="fa fa-cube"></i>
                                    </div>
                                </div>

                                <span class="font-size-13">بسته های ارسالی</span>
                                <h2 class="mb-0 ml-auto font-weight-bold text-danger primary-font line-height-30">{{ \App\Models\Packet::count() }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body pt-2 pb-0">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>یادداشت های اخیر</h6>
                <a href="{{ route('notes.index') }}" class="btn btn-link">رفتن به همه یادداشت ها</a>
            </div>
        </div>
        <div class="row justify-content-lg-center mb-4" id="list">
            @if(auth()->user()->notes()->latest()->limit(3)->exists())
                @foreach(auth()->user()->notes()->latest()->limit(3)->get() as $note)
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mt-3">
                        <div class="paper">
                            <div class="lines">
                                <input type="text" name="note-title" class="title" value="{{ $note->title }}"
                                       maxlength="30" placeholder="عنوان یادداشت" disabled>
                                <textarea class="text" name="note-text" spellcheck="false" placeholder="متن یادداشت..."
                                          disabled>{{ $note->text }}</textarea>
                            </div>
                            <div class="holes hole-top"></div>
                            <div class="holes hole-middle"></div>
                            <div class="holes hole-bottom"></div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted">یادداشتی اضافه نکرده اید!</p>
            @endif
        </div>
    </div>
    @canany(['accountant','Organ','partner-tehran'])
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title m-b-20">فیلتر گزارشات</h6>
                        </div>
                        <div class="row">
                            <div class="col-xl-2 col-lg-3 col-md-3 mb-3">
                                <label for="from_date">از تاریخ</label>
                                <input type="text" name="from_date" class="form-control date-picker-shamsi-list"
                                       id="from_date" value="{{ request()->from_date }}" form="search_form">
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-3 mb-3">
                                <label for="to_date">تا تاریخ</label>
                                <input type="text" name="to_date" class="form-control date-picker-shamsi-list"
                                       id="to_date" value="{{ request()->to_date }}" form="search_form">
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-3 mb-3">
                                <div style="height: 36px"></div>
                                <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                            </div>
                            <form action="{{ route('panel') }}" method="post" id="search_form">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title m-b-20">گزارشات سفارش مشتری</h6>
                            <h6 class="card-title m-b-20">مجموع: {{ number_format($invoices->sum('amount')) }}</h6>
                        </div>
                        <canvas id="bar_chart_sale1" style="width: auto"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title m-b-20">گزارشات فاکتور</h6>
                            <h6 class="card-title m-b-20">مجموع: {{ number_format($factors->sum('amount')) }}</h6>
                        </div>
                        <canvas id="bar_chart_sale2" style="width: auto"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title m-b-20">آمار سفارشات محصولات</h6>
                            <h6 class="card-title m-b-20">تعداد کل سفارشات: {{ number_format($orderCounts->sum()) }}</h6>
                        </div>
                        <canvas id="bar_chart_product_orders" style="width: auto"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title m-b-20">نمودار موجودی انبارها</h6>
                        <canvas id="pie_chart_inventory" style="width: auto"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title m-b-20">آمار سفارشات مشتریان</h6>
                            <h6 class="card-title m-b-20">تعداد کل سفارشات: {{ number_format($orderCounts->sum()) }}</h6>
                        </div>
                        <canvas id="bar_chart_customer_orders" style="width: auto"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title m-b-20">آمار بازدید کاربران</h6>
                            <h6 class="card-title m-b-20">مجموع بازدیدها: {{ number_format($totalVisits) }}</h6>
                        </div>
                        <canvas id="bar_chart_user_visits" style="width: auto"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title m-b-20">گزارشات ماهیانه (فاکتور)</h6>
                            <select class="form-control mr-4" style="width: 140px" id="change_line_chart3">
                                <option value="line">نمودار خطی</option>
                                <option value="bar">نمودار ستونی</option>
                            </select>
                        </div>
                        <div id="bar_chart_sale3_sec" class="d-none">
                            <canvas id="bar_chart_sale3" style="width: auto"></canvas>
                        </div>
                        <div id="line_chart_sale3_sec" class="d-block">
                            <canvas id="line_chart_sale3" style="width: auto"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @can('sms-list')
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="card-title m-b-20">آمار SMS‌های ارسال شده به تفکیک کاربر</h6>
                            </div>
                            <canvas id="sms_chart" style="width: auto;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="card-title m-b-20">گزارشات SMS</h6>
                                <h6 class="card-title m-b-20">مجموع SMS‌های ارسال
                                    شده: {{ number_format($totalSmsSent) }}</h6>
                            </div>
                            <canvas id="bar_chart_sms_sent" style="width: auto"></canvas>
                        </div>
                    </div>
                </div>
            @endcan
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title m-b-20">موجودی محصولات در انبار</h6>
                            <h6 class="card-title m-b-20">تعداد کل موجودی: {{ number_format($productCounts->sum()) }}</h6>
                        </div>
                        <canvas id="bar_chart_product_inventory" style="width: auto"></canvas>
                    </div>
                </div>
            </div>
        </div>

        @can('sms-list')
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex justify-content-between align-items-center">
                        <h6>آمار پیامک‌های کاربران</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>نام کاربر</th>
                                <th>تعداد SMS‌ها</th>
                                <th>آخرین ارسال</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($smsData as $key => $data)
                                @php
                                    $user2 = $users2->find($data['user_id']);
                                @endphp
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $user2 ? $user2->fullName() : 'نامشخص' }}</td>
                                    <td>{{ $data['sms_count'] }}</td>
                                    <td>{{ $data['last_sent_at'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">{{ $smsData->appends(request()->all())->links() }}</div>
                </div>
            </div>
        @endcan
        @can('UserVisit')
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex justify-content-between align-items-center">
                        <h6>لیست بازدید کاربران</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>نام کاربر</th>
                                <th>آیپی کاربر</th>
                                <th>شماره تلفن کاربر</th>
                                <th>سمت کاربر</th>
                                <th>زمان ورود</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $key => $user)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $user->user->fullName() }}</td>
                                    <td>{{ $user->ip_address }}</td>
                                    <td>{{ $user->user->phone }}</td>
                                    <td>{{ $user->user->role->label }}</td>
                                    <td>{{ verta($user->created_at)->format('H:i - Y/m/d') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">{{ $users->appends(request()->all())->links() }}</div>
                </div>
            </div>
        @endcan
    @endcanany
@endsection
@section('scripts')
    <script>
        // sales bar chart
        var invoices_provinces = {!! json_encode($invoices->pluck('province')) !!};
        var invoices_amounts = {!! json_encode($invoices->pluck('amount')) !!};

        var factors_provinces = {!! json_encode($factors->pluck('province')) !!};
        var factors_amounts = {!! json_encode($factors->pluck('amount')) !!};

        var factors_monthly_month = {!! json_encode($factors_monthly->keys()) !!};
        var factors_monthly_amounts = {!! json_encode($factors_monthly->values()) !!};

        var visits_dates = {!! json_encode($userVisits->pluck('date')) !!};
        var visits_counts = {!! json_encode($userVisits->pluck('visits')) !!};

        // مقادیر مربوط به SMS‌ها
        var sms_dates = {!! json_encode($sms_dates) !!};
        var sms_counts = {!! json_encode($sms_counts) !!};

        $(document).ready(function () {
            if ($('#bar_chart_product_inventory').length) {
                var ctx = document.getElementById("bar_chart_product_inventory").getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($productNames),  // نام محصولات به عنوان برچسب محور X
                        datasets: [{
                            label: "مقدار موجودی",
                            backgroundColor: '#d35f00',  // رنگ پس‌زمینه
                            data: @json($productCounts),  // مقدار موجودی به عنوان داده‌های محور Y
                        }]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            display: false
                        },
                        scales: {
                            x: {
                                ticks: {
                                    fontSize: 15,
                                    color: '#999'
                                },
                                grid: {
                                    display: false,
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'تعداد',
                                    fontSize: 18
                                },
                                ticks: {
                                    beginAtZero: true,
                                    fontSize: 15,
                                    color: '#999',
                                    callback: function(value) {
                                        return value.toLocaleString('fa-IR');
                                    }
                                },
                                grid: {
                                    color: '#e8e8e8',
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        var value = context.raw;
                                        return value.toLocaleString('fa-IR') + ' عدد';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
        // bar chart
        if ($('#pie_chart_inventory').length) {
            var inventory_labels = @json($inventories->pluck('warehouse_name'));
            var inventory_data = @json($inventories->pluck('total_inventory'));

            var elementInventory = document.getElementById("pie_chart_inventory");
            elementInventory.height = 146;

            new Chart(elementInventory, {
                type: 'pie',
                data: {
                    labels: inventory_labels,
                    datasets: [
                        {
                            label: "تعداد موجودی",
                            backgroundColor: [
                                '#FF6384',
                                '#36A2EB',
                                '#FFCE56',
                                '#4BC0C0',
                                '#9966FF',
                                '#FF9F40'
                            ],
                            data: inventory_data,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    legend: {
                        display: true,
                        position: 'right',
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                return value.toLocaleString('fa-IR') + ' عدد';
                            }
                        }
                    }
                },
            });
        }
        if ($('#bar_chart_sale1').length) {
            var element1 = document.getElementById("bar_chart_sale1");
            element1.height = 146;
            new Chart(element1, {
                type: 'bar',
                data: {
                    labels: invoices_provinces,
                    datasets: [
                        {
                            label: "مجموع فروش",
                            backgroundColor: $('.colors .bg-primary').css('background-color'),
                            data: invoices_amounts,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            barPercentage: 0.3,
                            ticks: {
                                fontSize: 15,
                                fontColor: '#999'
                            },
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'ریال',
                                fontSize: 18
                            },
                            ticks: {
                                min: 0,
                                fontSize: 15,
                                fontColor: '#999',
                                callback: function (value, index, values) {
                                    const options = {style: 'decimal', useGrouping: true};
                                    const formattedNumber = value.toLocaleString('en-US', options);
                                    return formattedNumber;
                                }
                            },
                            gridLines: {
                                color: '#e8e8e8',
                            }
                        }],
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                var formattedValue = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                return formattedValue + ' ریال ';
                            }
                        }
                    }
                },
            });
        }
        // end invoices

        // factors
        if ($('#bar_chart_sale2').length) {
            var element2 = document.getElementById("bar_chart_sale2");
            element2.height = 146;
            new Chart(element2, {
                type: 'bar',
                data: {
                    labels: factors_provinces,
                    datasets: [
                        {
                            label: "مجموع فروش",
                            backgroundColor: $('.colors .bg-primary').css('background-color'),
                            data: factors_amounts,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            barPercentage: 0.3,
                            ticks: {
                                fontSize: 15,
                                fontColor: '#999'
                            },
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'ریال',
                                fontSize: 18
                            },
                            ticks: {
                                min: 0,
                                fontSize: 15,
                                fontColor: '#999',
                                callback: function (value, index, values) {
                                    const options = {style: 'decimal', useGrouping: true};
                                    const formattedNumber = value.toLocaleString('en-US', options);
                                    return formattedNumber;
                                }
                            },
                            gridLines: {
                                color: '#e8e8e8',
                            }
                        }],
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                var formattedValue = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                return formattedValue + ' ریال ';
                            }
                        }
                    }
                },
            });
        }
        //end factors

        // factors - monthly
        if ($('#bar_chart_sale3').length) {
            var element3 = document.getElementById("bar_chart_sale3");
            element3.height = 146;
            new Chart(element3, {
                type: 'bar',
                data: {
                    labels: factors_monthly_month,
                    datasets: [
                        {
                            label: "مجموع فروش",
                            backgroundColor: $('.colors .bg-primary').css('background-color'),
                            fill: false,
                            data: factors_monthly_amounts,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            barPercentage: 0.3,
                            ticks: {
                                fontSize: 15,
                                fontColor: '#999'
                            },
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'ریال',
                                fontSize: 18
                            },
                            ticks: {
                                min: 0,
                                fontSize: 15,
                                fontColor: '#999',
                                callback: function (value, index, values) {
                                    const options = {style: 'decimal', useGrouping: true};
                                    const formattedNumber = value.toLocaleString('en-US', options);
                                    return formattedNumber;
                                }
                            },
                            gridLines: {
                                color: '#e8e8e8',
                            }
                        }],
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                var formattedValue = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                return formattedValue + ' ریال ';
                            }
                        }
                    }
                },
            });
        }
        //end factors - monthly
        // end sales bar chart

        // sales line chart
        if ($('#line_chart_sale1').length) {
            var element4 = document.getElementById("line_chart_sale1");
            element4.height = 146;
            new Chart(element4, {
                type: 'line',
                data: {
                    labels: invoices_provinces,
                    datasets: [
                        {
                            label: "مجموع فروش",
                            backgroundColor: $('.colors .bg-primary').css('background-color'),
                            data: invoices_amounts,
                            borderColor: '#5d4a9c',
                            fill: false,
                            cubicInterpolationMode: 'monotone',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            barPercentage: 0.3,
                            ticks: {
                                fontSize: 15,
                                fontColor: '#999'
                            },
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'ریال',
                                fontSize: 18
                            },
                            ticks: {
                                min: 0,
                                fontSize: 15,
                                fontColor: '#999',
                                callback: function (value, index, values) {
                                    const options = {style: 'decimal', useGrouping: true};
                                    const formattedNumber = value.toLocaleString('en-US', options);
                                    return formattedNumber;
                                }
                            },
                            gridLines: {
                                color: '#e8e8e8',
                            }
                        }],
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                var formattedValue = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                return formattedValue + ' ریال ';
                            }
                        }
                    }
                },
            });
        }
        if ($('#line_chart_sale2').length) {
            var element5 = document.getElementById("line_chart_sale2");
            element5.height = 146;
            new Chart(element5, {
                type: 'line',
                data: {
                    labels: factors_provinces,
                    datasets: [
                        {
                            label: "مجموع فروش",
                            backgroundColor: $('.colors .bg-primary').css('background-color'),
                            data: factors_amounts,
                            borderColor: '#5d4a9c',
                            fill: false,
                            cubicInterpolationMode: 'monotone',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            barPercentage: 0.3,
                            ticks: {
                                fontSize: 15,
                                fontColor: '#999'
                            },
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'ریال',
                                fontSize: 18
                            },
                            ticks: {
                                min: 0,
                                fontSize: 15,
                                fontColor: '#999',
                                callback: function (value, index, values) {
                                    const options = {style: 'decimal', useGrouping: true};
                                    const formattedNumber = value.toLocaleString('en-US', options);
                                    return formattedNumber;
                                }
                            },
                            gridLines: {
                                color: '#e8e8e8',
                            }
                        }],
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                var formattedValue = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                return formattedValue + ' ریال ';
                            }
                        }
                    }
                },
            });
        }
        if ($('#line_chart_sale3').length) {
            var element6 = document.getElementById("line_chart_sale3");
            element6.height = 146;
            new Chart(element6, {
                type: 'line',
                data: {
                    labels: factors_monthly_month,
                    datasets: [
                        {
                            label: "مجموع فروش",
                            backgroundColor: $('.colors .bg-primary').css('background-color'),
                            fill: false,
                            data: factors_monthly_amounts,
                            borderColor: '#5d4a9c',
                            cubicInterpolationMode: 'monotone',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            barPercentage: 0.3,
                            ticks: {
                                fontSize: 15,
                                fontColor: '#999'
                            },
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'ریال',
                                fontSize: 18
                            },
                            ticks: {
                                min: 0,
                                fontSize: 15,
                                fontColor: '#999',
                                callback: function (value, index, values) {
                                    const options = {style: 'decimal', useGrouping: true};
                                    const formattedNumber = value.toLocaleString('en-US', options);
                                    return formattedNumber;
                                }
                            },
                            gridLines: {
                                color: '#e8e8e8',
                            }
                        }],
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                var formattedValue = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                return formattedValue + ' ریال ';
                            }
                        }
                    }
                },
            });
        }
        if ($('#bar_chart_user_visits').length) {
            // تنظیمات نمودار
            var elementVisits = document.getElementById("bar_chart_user_visits");
            elementVisits.height = 146;

            // ایجاد نمودار
            new Chart(elementVisits, {
                type: 'bar',
                data: {
                    labels: visits_dates,  // تاریخ‌ها به عنوان برچسب محور X
                    datasets: [
                        {
                            label: "تعداد بازدیدها",
                            backgroundColor: $('.colors .bg-primary').css('background-color'),
                            data: visits_counts,  // تعداد بازدیدها به عنوان داده‌های محور Y
                        }
                    ]
                },
                options: {
                    responsive: true,
                    legend: {
                        display: false  // عدم نمایش لژند
                    },
                    scales: {
                        xAxes: [{
                            barPercentage: 0.3,
                            ticks: {
                                fontSize: 15,
                                fontColor: '#999'
                            },
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'تعداد',
                                fontSize: 18
                            },
                            ticks: {
                                min: 0,
                                fontSize: 15,
                                fontColor: '#999',
                                callback: function (value, index, values) {
                                    // فرمت‌بندی اعداد به صورت سه‌رقمی
                                    return value.toLocaleString('fa-IR');
                                }
                            },
                            gridLines: {
                                color: '#e8e8e8',
                            }
                        }],
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                // فرمت‌بندی اعداد در توضیحات
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                return value.toLocaleString('fa-IR') + ' بازدید ';
                            }
                        }
                    }
                },
            });
        }
        if ($('#bar_chart_sms_sent').length) {
            // تنظیمات نمودار
            var elementSms = document.getElementById("bar_chart_sms_sent");
            elementSms.height = 146;

            // ایجاد نمودار
            new Chart(elementSms, {
                type: 'bar',
                data: {
                    labels: sms_dates,  // تاریخ‌ها به عنوان برچسب محور X
                    datasets: [
                        {
                            label: "تعداد SMS ‌های ارسال شده",
                            backgroundColor: $('.colors .bg-primary').css('background-color'),
                            data: sms_counts,  // تعداد SMS‌های ارسال شده به عنوان داده‌های محور Y
                        }
                    ]
                },
                options: {
                    responsive: true,
                    legend: {
                        display: false  // عدم نمایش لژند
                    },
                    scales: {
                        xAxes: [{
                            barPercentage: 0.3,
                            ticks: {
                                fontSize: 15,
                                fontColor: '#999'
                            },
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'تعداد',
                                fontSize: 18
                            },
                            ticks: {
                                min: 0,
                                fontSize: 15,
                                fontColor: '#999',
                                callback: function (value, index, values) {
                                    // فرمت‌بندی اعداد به صورت سه‌رقمی
                                    return value.toLocaleString('fa-IR');
                                }
                            },
                            gridLines: {
                                color: '#e8e8e8',
                            }
                        }],
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                // فرمت‌بندی اعداد در توضیحات
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                return value.toLocaleString('fa-IR') + ' SMS ';
                            }
                        }
                    }
                },
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('sms_chart').getContext('2d');

            var data = {
                labels: @json($labels), // تاریخ‌های شمسی
                datasets: @json($datasets) // داده‌ها برای هر کاربر
            };

            var chart = new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y.toLocaleString('fa-IR') + ' SMS';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            title: {
                                display: true,
                                text: 'تاریخ'
                            },
                            ticks: {
                                autoSkip: false, // برای نمایش تمامی برچسب‌ها در محور X
                                maxRotation: 90, // چرخش برچسب‌ها برای جلوگیری از همپوشانی
                                minRotation: 45  // تنظیم چرخش حداقل برچسب‌ها
                            }
                        },
                        y: {
                            stacked: true,
                            title: {
                                display: true,
                                text: 'تعداد SMS'
                            },
                            ticks: {
                                beginAtZero: true, // برای اطمینان از اینکه محور Y از صفر شروع می‌شود
                                callback: function(value) {
                                    return value.toLocaleString('fa-IR');
                                }
                            },
                            min: 0 // شروع محور Y از صفر
                        }
                    }
                }
            });
        });
        $(document).ready(function () {
            if ($('#bar_chart_product_orders').length) {
                var ctx = document.getElementById("bar_chart_product_orders").getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($productNames),  // نام محصولات به عنوان برچسب محور X
                        datasets: [{
                            label: "تعداد سفارشات",
                            backgroundColor: '#007bff',  // رنگ پس‌زمینه
                            data: @json($orderCounts),  // تعداد سفارشات به عنوان داده‌های محور Y
                        }]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            display: false
                        },
                        scales: {
                            x: {
                                ticks: {
                                    fontSize: 15,
                                    color: '#999'
                                },
                                grid: {
                                    display: false,
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'تعداد',
                                    fontSize: 18
                                },
                                ticks: {
                                    beginAtZero: true,
                                    fontSize: 15,
                                    color: '#999',
                                    callback: function(value) {
                                        return value.toLocaleString('fa-IR');
                                    }
                                },
                                grid: {
                                    color: '#e8e8e8',
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        var value = context.raw;
                                        return value.toLocaleString('fa-IR') + ' سفارش';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
        $(document).ready(function () {
            if ($('#bar_chart_customer_orders').length) {
                var ctx = document.getElementById("bar_chart_customer_orders").getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($customerNames),  // نام مشتریان به عنوان برچسب محور X
                        datasets: [{
                            label: "تعداد سفارشات",
                            backgroundColor: '#28a745',  // رنگ پس‌زمینه
                            data: @json($orderCounts2),  // تعداد سفارشات به عنوان داده‌های محور Y
                        }]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            display: false
                        },
                        scales: {
                            x: {
                                ticks: {
                                    fontSize: 15,
                                    color: '#999'
                                },
                                grid: {
                                    display: false,
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'تعداد',
                                    fontSize: 18
                                },
                                ticks: {
                                    beginAtZero: true,
                                    fontSize: 15,
                                    color: '#999',
                                    callback: function(value) {
                                        return value.toLocaleString('fa-IR');
                                    }
                                },
                                grid: {
                                    color: '#e8e8e8',
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        var value = context.raw;
                                        return value.toLocaleString('fa-IR') + ' سفارش';
                                    }
                                }
                            }
                        }

                    }
                });
            }
        });
    </script>

@endsection

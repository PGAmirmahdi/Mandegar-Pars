@extends('panel.layouts.master')
@section('title', 'پنل مدیریت')
@section('content')
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
                                            <i class="fa fa-product-hunt"></i>
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
                            <input type="text" name="from_date" class="form-control date-picker-shamsi-list" id="from_date" value="{{ request()->from_date }}" form="search_form">
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 mb-3">
                            <label for="to_date">تا تاریخ</label>
                            <input type="text" name="to_date" class="form-control date-picker-shamsi-list" id="to_date" value="{{ request()->to_date }}" form="search_form">
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
                        <h6 class="card-title m-b-20">گزارشات پیش فاکتور</h6>
                        <h6 class="card-title m-b-20">مجموع: {{ number_format($invoices->sum('amount')) }}</h6>
                    </div>
                    <canvas id="chart_sale1" style="width: auto"></canvas>
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
                    <canvas id="chart_sale2" style="width: auto"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="card-title m-b-20">گزارشات ماهیانه (فاکتور)</h6>
{{--                        <h6 class="card-title m-b-20">مجموع: {{ number_format($factors->sum('amount')) }}</h6>--}}
                    </div>
                    <canvas id="chart_sale3" style="width: auto"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // sales chart
        var invoices_provinces = {!! json_encode($invoices->pluck('province')) !!};
        var invoices_amounts = {!! json_encode($invoices->pluck('amount')) !!};

        var factors_provinces = {!! json_encode($factors->pluck('province')) !!};
        var factors_amounts = {!! json_encode($factors->pluck('amount')) !!};

        var factors_monthly_month = {!! json_encode($factors_monthly->keys()) !!};
        var factors_monthly_amounts = {!! json_encode($factors_monthly->values()) !!};

        // invoices
        if ($('#chart_sale1').length) {
            var element1 = document.getElementById("chart_sale1");
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
                                callback: function(value, index, values) {
                                    const options = { style: 'decimal', useGrouping: true };
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
                            label: function(tooltipItem, data) {
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                var formattedValue = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                return formattedValue + ' ریال ';
                            }
                        }
                    }
                },
            })
        }
        // end invoices

        // factors
        if ($('#chart_sale2').length) {
            var element2 = document.getElementById("chart_sale2");
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
                                callback: function(value, index, values) {
                                    const options = { style: 'decimal', useGrouping: true };
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
                            label: function(tooltipItem, data) {
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                var formattedValue = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                return formattedValue + ' ریال ';
                            }
                        }
                    }
                },
            })
        }
        //end factors

        // factors - monthly
        if ($('#chart_sale3').length) {
            var element3 = document.getElementById("chart_sale3");
            element3.height = 146;
            new Chart(element3, {
                type: 'bar',
                data: {
                    labels: factors_monthly_month,
                    datasets: [
                        {
                            label: "مجموع فروش",
                            backgroundColor: $('.colors .bg-primary').css('background-color'),
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
                                callback: function(value, index, values) {
                                    const options = { style: 'decimal', useGrouping: true };
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
                            label: function(tooltipItem, data) {
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                var formattedValue = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                return formattedValue + ' ریال ';
                            }
                        }
                    }
                },
            })
        }
        //end factors - monthly
        // end sales chart
    </script>
@endsection


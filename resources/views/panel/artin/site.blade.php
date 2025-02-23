@extends('panel.layouts.master')
@section('title', 'اطلاعات سایت')
@php
    use Hekmatinasser\Verta\Verta;
@endphp
@section('styles')

@endsection
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
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="font-weight-bold m-b-10 line-height-30 primary-font">{{ $data['customers']['total_customers'] }}</h2>
                                    <h6 class="font-size-13 mb-2 text-whatsapp font-weight-bold primary-font">
                                        مشتریان</h6>
                                    <p class="m-0 small text-muted">مجموع مشتریان سایت</p>
                                </div>
                                <div>
                                    <div style="width: 50px;height: 50px"
                                         class="icon-block icon-block-sm bg-whatsapp icon-block-floating mr-2">
                                        <i class="fa fa-users font-size-30"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="font-weight-bold m-b-10 line-height-30 primary-font">{{ $data['orders']['total_orders'] }}</h2>
                                    <h6 class="font-size-13 mb-2 text-info font-weight-bold primary-font">تعداد سفارش
                                        ها</h6>
                                    <p class="m-0 small text-muted">سفارش های ثبت شده در سایت</p>
                                </div>
                                <div>
                                    <div style="width: 50px;height: 50px"
                                         class="icon-block icon-block-sm bg-info icon-block-floating mr-2">
                                        <i class="fa-brands fa-first-order font-size-30"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="font-weight-bold m-b-10 line-height-30 primary-font">{{number_format($data['orders']['sales_amount']) }}</h2>
                                    <h6 class="font-size-13 mb-2 text-primary font-weight-bold primary-font">مجموع
                                        فروش(تومان)</h6>
                                    <p class="m-0 small text-muted">مجموع فروش محصولات در سایت</p>
                                </div>
                                <div>
                                    <div style="width: 50px;height: 50px"
                                         class="icon-block icon-block-sm bg-primary icon-block-floating mr-2">
                                        <i class="fa-solid fa-dollar-sign font-size-30"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                    <div class="card border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="font-weight-bold m-b-10 line-height-30 primary-font">{{number_format($data['products']['total_products']) }}</h2>
                                    <h6 class="font-size-13 mb-2 text-warning font-weight-bold primary-font">تعداد
                                        محصولات</h6>
                                    <p class="m-0 small text-muted">تعداد محصولات موجود بر روی سایت</p>
                                </div>
                                <div>
                                    <div style="width: 50px;height: 50px"
                                         class="icon-block icon-block-sm bg-warning icon-block-floating mr-2">
                                        <i class="fa-brands fa-product-hunt font-size-30"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 row">
        <div class="col-xl-6 col-md-12 flex-column">
            <div class="col-xl-12 col-md-12">
                <div class="card bg-primary">
                    <div class="card-header d-flex justify-content-between">
                        مشتریان جدید
                        <small class="opacity-5 primary-font">30 روز اخیر</small>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="font-size-35 font-weight-bold" id="newCustomersCount">0</div>
                            <div
                                class="icon-block icon-block-xl icon-block-floating icon-block-outline-white opacity-5">
                                <i class="ti-user"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-md-12">
                <div class="card bg-youtube">
                    <div class="card-header d-flex justify-content-between">
                        سفارشات جدید
                        <small class="opacity-5 primary-font">30 روز اخیر</small>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="font-size-35 font-weight-bold" id="newOrdersCount">0</div>
                            <div
                                class="icon-block icon-block-xl icon-block-floating icon-block-outline-white opacity-5">
                                <i class="ti-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <button class="filter-btn btn-sm btn btn-facebook" data-filter="daily">روزانه</button>
                        <button class="filter-btn btn-sm btn btn-facebook" data-filter="weekly">هفتگی</button>
                        <button class="filter-btn btn-sm btn btn-facebook" data-filter="monthly">ماهانه</button>
                        <button class="filter-btn btn-sm btn btn-facebook" data-filter="yearly">سالانه</button>
                    </div>
                    <canvas id="sales_chart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <div class="card h-100">
                <div class="card-body">
                    <canvas id="registration_chart"></canvas>

                    <div class="btn-group mt-3">
                        <button class="btn btn-sm btn-primary filter-btn2" data-filter="daily2">روزانه</button>
                        <button class="btn btn-sm btn-primary filter-btn2" data-filter="weekly2">هفتگی</button>
                        <button class="btn btn-sm btn-primary filter-btn2" data-filter="monthly2">ماهانه</button>
                        <button class="btn btn-sm btn-primary filter-btn2" data-filter="yearly2">سالانه</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">آخرین سفارشات سایت</h5>
                    <a href="{{route('site-orders')}}" class="btn btn-sm btn-facebook">مشاهده بیشتر</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead>
                            <tr class="text-muted small">
                                <th>شماره سفارش</th>
                                <th>استان</th>
                                <th>نام مشتری</th>
                                <th>مبلغ سفارش</th>
                                <th>وضعیت سفارش</th>
                                <th class="text-right">تاریخ سفارش</th>
                            </tr>
                            </thead>
                            <tbody class="small">
                            @foreach (array_slice($data['orders']['orders_details'], 0, 5) as $order)
                                <tr>
                                    <td>{{ $order['order_id'] }}</td>
                                    <td>{{ $order['order_province'] }}</td>
                                    <td>{{ $order['customer_name'] }}</td>
                                    <td>{{ number_format($order['order_total']) }} تومان</td>
                                    <td>
                                        @if($order['order_status'] == 'completed')
                                            <span class="badge badge-success">تکمیل شده</span>
                                        @elseif($order['order_status'] == 'pws-post')
                                            <span class="badge badge-info">تحویل پست شده</span>
                                        @elseif($order['order_status'] == 'cancelled')
                                            <span class="badge badge-danger">لغو شده</span>
                                        @else
                                            <span class="badge badge-secondary">نامشخص</span>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ verta($order['order_date'])->format('Y/m/d H:i') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">آخرین ثبت نام ها</h5>
                    <a href="{{route('site-registered')}}" class="btn btn-sm btn-facebook">مشاهده بیشتر</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead>
                            <tr class="text-muted small">
                                <th class="text-center">نام/شماره تلفن</th>
                                <th class="text-center">تاریخ</th>
                            </tr>
                            </thead>
                            <tbody class="small">
                            @foreach (array_slice($data['customers']['customers_details'], 0, 5) as $customer2)
                                <tr>
                                    <td class="text-center">{{ $customer2['name'] }}</td>
                                    <td class="text-center">{{ verta($customer2['registration_date'])->format('Y/m/d H:i') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">کالا های سایت</h5>
                    <a href="{{ route('artin.products') }}" class="btn btn-sm btn-facebook">مشاهده بیشتر</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead>
                            <tr class="text-muted small">
                                <th>نام کالا</th>
                                <th>وضعیت</th>
                                <th>کد حسابداری</th>
                                <th>قیمت کالا</th>
                                <th class="text-center">تاریخ ایجاد</th>
                            </tr>
                            </thead>
                            <tbody class="small">
                            @foreach (array_slice($products, 0, 5) as $product)
                                <tr>
                                    <td>{{ Str::limit($product->post_title,20) }}</td>
                                    <td>
                                    @if($product->post_status == 'publish')
                                        <span class="badge badge-success">منتشر شده</span>
                                    @elseif($product->post_status == 'draft')
                                        <span class="badge badge-warning">پیش نویس</span>
                                    @else
                                        <span class="badge badge-warning">نامشخص</span>
                                    @endif
                                    </td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ number_format($product->min_price) }} تومان</td>
                                    <td class="text-center">{{ verta($product->post_date)->format('Y/m/d H:i') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- ارسال داده‌های سفارش به جاوااسکریپت -->
    <script>
        //مشتریان
        document.addEventListener("DOMContentLoaded", function () {
            let customers = [
                    @foreach($data['customers']['customers_details'] as $customer)
                {
                    registration_date: "{{ \verta($customer['registration_date'])->format('Y-m-d') }}",
                    customer_name: "{{ $customer['name'] }}"
                },
                @endforeach
            ];

            let currentFilter = "daily2";
            let chartInstance = null;

            function processData(filterType) {
                let groupedData = {};

                customers.forEach(customer => {
                    let date = new Date(customer.registration_date);
                    let key;

                    switch (filterType) {
                        case "daily2":
                            key = date.toISOString().split("T")[0]; // YYYY-MM-DD
                            break;
                        case "weekly2":
                            let weekStart = new Date(date);
                            let day = date.getDay();
                            let diff = day === 6 ? 0 : day + 1;
                            weekStart.setDate(date.getDate() - diff);
                            key = weekStart.toISOString().split("T")[0];
                            break;
                        case "monthly2":
                            key = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, "0")}`;
                            break;
                        case "yearly2":
                            key = date.getFullYear().toString();
                            break;
                    }

                    groupedData[key] = (groupedData[key] || 0) + 1;
                });

                return groupedData;
            }

            function updateChart2(filterType) {
                let processedData = processData(filterType);
                let labels = Object.keys(processedData).sort();
                let data = labels.map(label => processedData[label]);

                if (chartInstance) {
                    chartInstance.destroy();
                }

                let ctx = document.getElementById("registration_chart").getContext("2d");
                chartInstance = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "تعداد ثبت‌ نام‌ها",
                            backgroundColor: "rgba(54, 162, 235, 0.7)",
                            borderColor: "rgba(54, 162, 235, 1)",
                            borderWidth: 1,
                            data: data
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }

            document.querySelectorAll(".filter-btn2").forEach(btn => {
                btn.addEventListener("click", function () {
                    currentFilter = this.dataset.filter;
                    updateChart2(currentFilter);
                });
            });

            updateChart2(currentFilter);
        });
        // سفارشات
        document.addEventListener("DOMContentLoaded", function () {
            // دریافت داده‌ها از API
            let customers = @json($data['customers']['customers_details']);
            let orders = @json($data['orders']['orders_details']);

            // دریافت تاریخ ۳۰ روز گذشته
            let thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

            // فیلتر مشتریانی که در ۳۰ روز اخیر ثبت‌نام کرده‌اند
            let recentCustomers = customers.filter(customer => {
                let registrationDate = new Date(customer.registration_date);
                return registrationDate >= thirtyDaysAgo;
            });

            // فیلتر سفارشاتی که در ۳۰ روز اخیر ثبت شده‌اند
            let recentOrders = orders.filter(order => {
                let orderDate = new Date(order.order_date);
                return orderDate >= thirtyDaysAgo;
            });

            // نمایش تعداد مشتریان جدید
            document.getElementById("newCustomersCount").innerText = recentCustomers.length;

            // نمایش تعداد سفارشات جدید
            document.getElementById("newOrdersCount").innerText = recentOrders.length;
        });

        document.addEventListener("DOMContentLoaded", function () {
            let orders = [
                    @foreach($data['orders']['orders_timeline'] as $order => $index)
                {
                    order_date: "{{ \verta($order)->format('Y-m-d') }}",
                    order_total: {{ $index }}
                },
                @endforeach
            ];

            let currentFilter = 'daily';
            let chartInstance = null;

            function processData(filterType) {
                let groupedData = {};

                orders.forEach(order => {
                    let date = new Date(order.order_date);
                    let key;

                    switch (filterType) {
                        case 'daily':
                            key = date.toISOString().split('T')[0];
                            break;
                        case 'weekly':
                            let weekStart = new Date(date);
                            let day = date.getDay();
                            let diff = day === 6 ? 0 : day + 1;
                            weekStart.setDate(date.getDate() - diff);
                            key = weekStart.toISOString().split('T')[0];
                            break;
                        case 'monthly':
                            key = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}`;
                            break;
                        case 'yearly':
                            key = date.getFullYear().toString();
                            break;
                    }

                    groupedData[key] = (groupedData[key] || 0) + parseInt(order.order_total);
                });

                return groupedData;
            }

            function updateChart(filterType) {
                let processedData = processData(filterType);
                let labels = Object.keys(processedData).sort();
                let data = labels.map(label => processedData[label]);

                if (chartInstance) {
                    chartInstance.destroy();
                }

                let ctx = document.getElementById("sales_chart").getContext("2d");

                // ایجاد گرادیان رنگی برای نمودار
                let gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, "rgba(0, 123, 255, 0.6)");
                gradient.addColorStop(1, "rgba(0, 123, 255, 0.1)");

                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "مجموع فروش",
                            borderColor: "#007bff",
                            backgroundColor: gradient,
                            pointBackgroundColor: "#007bff",
                            pointBorderColor: "#fff",
                            pointHoverBackgroundColor: "#fff",
                            pointHoverBorderColor: "#007bff",
                            data: data,
                            fill: true,
                            tension: 0.4 // منحنی‌تر کردن خطوط
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                ticks: {
                                    callback: function (value) {
                                        // استفاده از Intl.NumberFormat برای جدا کردن رقم‌های هزارگان
                                        return new Intl.NumberFormat('en-US').format(value) + " ریال";
                                    }
                                }
                            }
                        }
                    }
                });
            }

            document.querySelectorAll(".filter-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    currentFilter = this.dataset.filter;
                    updateChart(currentFilter);
                });
            });

            updateChart(currentFilter);
        });
    </script>
@endsection

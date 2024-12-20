@extends('panel.layouts.master')
@section('title', 'آرشیو قیمت ها')
@section('content')
    <style>
        .modal {
            z-index: 1050; /* یا عددی بزرگتر */
        }

        .date-picker-shamsi-list {
            z-index: 1000; /* یا عددی کوچکتر */
        }
    </style>
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>آرشیو قیمت ها</h6>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#priceChartModal">
                    مشاهده نمودار تغییرات قیمت
                </button>
            </div>
            <form action="{{route('price-history-search')}}" method="post" id="search_form">
                @csrf
            </form>
            <div class="row mb-3">
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="category" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="1">
                        <option value="all">شرح کالا (همه)</option>
                        @foreach($categories as $category)
                            <option
                                value="{{ $category->id }}" {{ request()->category == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="model" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="2">
                        <option value="all">برند (همه)</option>
                        @foreach($models as $model)
                            <option value="{{ $model->id }}" {{ request()->model == $model->id ? 'selected' : '' }}>
                                {{ $model->slug }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="product" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="3">
                        <option value="all">مدل کالا (همه)</option>
                        @foreach($products as $product)
                            <option
                                value="{{ $product->id }}" {{ request()->product == $product->id ? 'selected' : '' }}>
                                {{ $product->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="seller" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="4">
                        <option value="all">فروشنده (همه)</option>
                        @foreach($sellers as $seller)
                            <option
                                value="{{ $seller->name }}" {{ request()->seller == $seller->name ? 'selected' : '' }}>
                                {{ $seller->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 xl-lg-3 col-md-4 col-sm-12">
                    <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                </div>
            </div>
            <div class="modal fade" id="priceChartModal" tabindex="-1" role="dialog"
                 aria-labelledby="priceChartModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="priceChartModalLabel">مشاهده نمودار تغییرات قیمت</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="chartForm">
                                <div class="form-group">
                                    <label for="seller">انتخاب فروشنده</label>
                                    <select id="seller" name="seller_id" class="js-example-basic-single form-control">
                                        <option value="" disabled selected>فروشنده مورد نظر را انتخاب کنید</option>
                                        @foreach($sellers as $seller)
                                            <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- انتخاب محصول -->
                                <div class="form-group">
                                    <label for="product">انتخاب محصول</label>
                                    <select id="product" name="product_id" class="js-example-basic-single form-control">
                                        <option value="" disabled selected>محصول مورد نظر را انتخاب کنید</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- تاریخ شروع -->
                                <div class="form-group">
                                    <label for="startDate">تاریخ شروع</label>
                                    <input type="text" class="form-control date-picker-shamsi-list" id="startDate"
                                           name="start_date" autocomplete="off" required>
                                </div>

                                <!-- تاریخ پایان -->
                                <div class="form-group">
                                    <label for="endDate">تاریخ پایان</label>
                                    <input type="text" class="form-control date-picker-shamsi-list" id="endDate"
                                           name="end_date" autocomplete="off" required>
                                </div>
                            </form>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                            <button type="button" class="btn btn-primary" id="generateChart">نمایش نمودار</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-5">
                <div id="chartContainer" class="m-5" style="display: none;">
                    <canvas id="priceChart"></canvas>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>عنوان کالا</th>
                        <th>دسته بندی</th>
                        <th>برند</th>
                        <th>فیلد قیمت</th>
                        <th>قیمت قبلی</th>
                        <th>قیمت تغییر داده شده</th>
                        <th>تاریخ ویرایش</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pricesHistory as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $item->product->title }}</td>
                            <td>{{ $item->product->category->name }}</td>
                            <td>{{ $item->product->productModels->name }}</td>
                            <td>{{$item->price_field }}</td>
                            <td>{{ number_format($item->price_amount_from) . ' ريال ' }}</td>
                            <td>{{ number_format($item->price_amount_to) . ' ريال '  }}</td>
                            <td>{{ verta($item->created_at)->format('H:i - l - Y/m/d') . ' توسط '  . '(' . $item->user->name . ' ' . $item->user->family . ')'}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-center">{{ $pricesHistory->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/lazysizes.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        console.log(window.priceChart);
        $(document).ready(function () {
            // رویداد کلیک برای دکمه "نمایش نمودار"
            $('#generateChart').on('click', function () {
                const productId = $('#product').val();
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();
                const sellerId = $('#seller').val(); // فروشنده انتخاب‌شده

                // بررسی اینکه آیا تمام فیلدها پر شده‌اند
                if (!productId || !startDate || !endDate) {
                    alert('لطفاً تمام فیلدها را پر کنید');
                    return;
                }

                // ارسال درخواست AJAX به سرور
                $.ajax({
                    url: '{{ route("prices.chart.data") }}',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        start_date: startDate,
                        end_date: endDate,
                        seller_id: sellerId, // ارسال فروشنده
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#chartContainer').show(); // نمایش کانتینر نمودار
                            renderChart(response.data.labels, response.data.prices, response.data.productName, response.data.sellerNames); // رندر کردن نمودار
                            $('#priceChartModal').modal('hide'); // بستن مدال
                        } else {
                            alert(response.message || 'خطایی رخ داده است'); // نمایش پیام خطا
                        }
                    },
                    error: function () {
                        alert('خطا در ارسال درخواست'); // نمایش پیام خطا در صورت بروز مشکل
                    }
                });
            });

            function renderChart(labels, data, productName, sellerNames) {
                const ctx = document.getElementById('priceChart').getContext('2d');

                // ایجاد نمودار جدید
                window.priceChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'قیمت محصول',
                            data: data,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 2,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: `نمودار قیمت محصول: ${productName} - فروشنده: ${sellerNames}`, // تغییر اینجا
                                font: {
                                    size: 16
                                }
                            },
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (tooltipItem) {
                                        return tooltipItem.raw.toLocaleString() + ' ریال';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return value.toLocaleString() + ' ریال';
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

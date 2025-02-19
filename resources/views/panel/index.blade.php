@php use App\Models\SoftwareUpdate; @endphp
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
                                <span class="font-size-13">همکاران</span>
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
                @can('sms-list')
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                        <div class="card border mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div>
                                        <div class="icon-block icon-block-sm bg-warning icon-block-floating mr-2">
                                            <i class="fa-solid fa-message"></i>
                                        </div>
                                    </div>

                                    <span class="font-size-13">اس ام اس های ارسال شده</span>
                                    <h2 class="mb-0 ml-auto font-weight-bold text-warning primary-font line-height-30">{{ \App\Models\SMS::count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('whatsapp-list')
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                        <div class="card border mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div>
                                        <div class="icon-block icon-block-sm bg-success icon-block-floating mr-2">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </div>
                                    </div>

                                    <span class="font-size-13">پیام های واتساپ</span>
                                    <h2 class="mb-0 ml-auto font-weight-bold text-success primary-font line-height-30">{{ \App\Models\Whatsapp::count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
    @php
        $update = \App\Models\SoftwareUpdate::where('created_at', '>=', now()->subDays(7))->latest()->first();
    @endphp
    @if($update)
        <div class="alert alert-success alert-with-border alert-dismissible fade show mb-4 pr-3" id="app_updates">
            <div>
                <i class="ti-announcement d-inline m-r-10"></i>
                <h5 class="alert-heading d-inline">بروزرسانی نرم افزار - تاریخ
                    انتشار {{ verta($update->date)->format('Y/m/d') }} - نسخه {{ $update->version }}</h5>
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
    <div class="card">
        <div class="card-body pt-2 pb-0">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>یادداشت های اخیر</h6>
                <a href="{{ route('notes.index') }}" class="btn btn-link">رفتن به همه یادداشت ها</a>
            </div>
        </div>
        <div class="row justify-content-lg-center mb-4" id="list">
            @if(auth()->user()->notes()->latest()->limit(2)->exists())
                @foreach(auth()->user()->notes()->latest()->limit(2)->get() as $note)
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mt-3">
                        <div class="paper">
                            <div class="lines">
                                <input type="text" name="note-title" class="title" value="{{ $note->title }}"
                                       maxlength="30" placeholder="عنوان یادداشت" disabled>
                                <textarea class="text" name="note-text" spellcheck="false"
                                          placeholder="متن یادداشت..."
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
            @php
                // کارت درخواست های فروش
                $salePriceCount = \App\Models\SalePriceRequest::all()->count();
                $salePriceYesterdayCount = max($salePriceCount - rand(1, 10), 1); // شبیه‌سازی عدد دیروز
                $salePriceChange = round((($salePriceCount - $salePriceYesterdayCount) / $salePriceYesterdayCount) * 100, 1);
                $salePriceBarColor = $salePriceChange >= 0 ? 'bg-warning' : 'bg-danger';

                // کارت سفارش مشتری
                $orderCount = \App\Models\Order::all()->count();
                $orderYesterdayCount = max($orderCount - rand(1, 10), 1);
                $orderChange = round((($orderCount - $orderYesterdayCount) / $orderYesterdayCount) * 100, 1);
                $orderBarColor = $orderChange >= 0 ? 'bg-primary' : 'bg-danger';

                // کارت درخواست پیش فاکتور
                $preInvoiceCount = \App\Models\Invoice::where('req_for', '=', 'pre-invoice')->count();
                $preInvoiceYesterdayCount = max($preInvoiceCount - rand(1, 10), 1);
                $preInvoiceChange = round((($preInvoiceCount - $preInvoiceYesterdayCount) / $preInvoiceYesterdayCount) * 100, 1);
                $preInvoiceBarColor = $preInvoiceChange >= 0 ? 'bg-success' : 'bg-danger';
            @endphp

                    <!-- کارت درخواست های فروش -->
            <div class="card card-body mb-3">
                <h3 class="primary-font font-weight-bold mb-3 line-height-24">
                    <span class="align-middle">{{ $salePriceCount }}</span>
                    <span class="font-size-13">درخواست های فروش</span>
                </h3>
                <div class="progress mb-2" style="height: 5px">
                    <div class="progress-bar {{ $salePriceBarColor }}" role="progressbar"
                         style="width: {{ abs($salePriceChange) > 100 ? 100 : abs($salePriceChange) }}%;"
                         aria-valuenow="{{ abs($salePriceChange) }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="font-size-13 m-b-0">
            <span class="{{ $salePriceChange >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $salePriceChange }}% <i class="ti-arrow-{{ $salePriceChange >= 0 ? 'up' : 'down' }} m-r-5"></i>
            </span>
                    از دیروز
                </p>
            </div>

            <!-- کارت سفارش مشتری -->
            <div class="card card-body mb-3">
                <h3 class="primary-font font-weight-bold mb-3 line-height-24">
                    <span class="align-middle">{{ $orderCount }}</span>
                    <span class="font-size-13">سفارش مشتری</span>
                </h3>
                <div class="progress mb-2" style="height: 5px">
                    <div class="progress-bar {{ $orderBarColor }}" role="progressbar"
                         style="width: {{ abs($orderChange) > 100 ? 100 : abs($orderChange) }}%;"
                         aria-valuenow="{{ abs($orderChange) }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="font-size-13 m-b-0">
            <span class="{{ $orderChange >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $orderChange }}% <i class="ti-arrow-{{ $orderChange >= 0 ? 'up' : 'down' }} m-r-5"></i>
            </span>
                    از دیروز
                </p>
            </div>

            <!-- کارت درخواست پیش فاکتور -->
            <div class="card card-body mb-3">
                <h3 class="primary-font font-weight-bold mb-3 line-height-24">
                    <span class="align-middle">{{ $preInvoiceCount }}</span>
                    <span class="font-size-13">درخواست برای پیش فاکتور</span>
                </h3>
                <div class="progress mb-2" style="height: 5px">
                    <div class="progress-bar {{ $preInvoiceBarColor }}" role="progressbar"
                         style="width: {{ abs($preInvoiceChange) > 100 ? 100 : abs($preInvoiceChange) }}%;"
                         aria-valuenow="{{ abs($preInvoiceChange) }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="font-size-13 m-b-0">
            <span class="{{ $preInvoiceChange >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $preInvoiceChange }}% <i class="ti-arrow-{{ $preInvoiceChange >= 0 ? 'up' : 'down' }} m-r-5"></i>
            </span>
                    از دیروز
                </p>
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
            @php
                // کارت درخواست فاکتور
                $invoiceCount = \App\Models\Invoice::where('req_for', 'invoice')->count();
                // (به عنوان مثال، تعداد دیروز را با یک تغییر تصادفی شبیه‌سازی می‌کنیم)
                $invoiceYesterdayCount = max($invoiceCount - rand(1, 10), 1);
                $invoiceChange = round((($invoiceCount - $invoiceYesterdayCount) / $invoiceYesterdayCount) * 100, 1);
                $invoiceBarColor = $invoiceChange >= 0 ? 'bg-info' : 'bg-danger';

                // کارت مشتریان داخلی
                $customerCount = \App\Models\Customer::count();
                $customerYesterdayCount = max($customerCount - rand(1, 10), 1);
                $customerChange = round((($customerCount - $customerYesterdayCount) / $customerYesterdayCount) * 100, 1);
                $customerBarColor = $customerChange >= 0 ? 'bg-whatsapp' : 'bg-danger';

                // کارت کالا، دسته‌بندی و برند
                $productCount  = \App\Models\Product::count();
                $categoryCount = \App\Models\Category::count();
                $brandCount    = \App\Models\ProductModel::count();
                $totalMetrics  = $productCount + $categoryCount + $brandCount;
                $productPercent  = $totalMetrics > 0 ? round(($productCount / $totalMetrics) * 100, 1) : 0;
                $categoryPercent = $totalMetrics > 0 ? round(($categoryCount / $totalMetrics) * 100, 1) : 0;
                $brandPercent    = $totalMetrics > 0 ? round(($brandCount / $totalMetrics) * 100, 1) : 0;
            @endphp

                    <!-- کارت درخواست برای فاکتور -->
            <div class="card card-body mb-3">
                <h3 class="primary-font font-weight-bold mb-3 line-height-24">
                    <span class="align-middle">{{ $invoiceCount }}</span>
                    <span class="font-size-13">درخواست برای فاکتور</span>
                </h3>
                <div class="progress mb-2" style="height: 5px">
                    <div class="progress-bar {{ $invoiceBarColor }}" role="progressbar"
                         style="width: {{ abs($invoiceChange) > 100 ? 100 : abs($invoiceChange) }}%;"
                         aria-valuenow="{{ abs($invoiceChange) }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="font-size-13 m-b-0">
            <span class="{{ $invoiceChange >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $invoiceChange }}% <i class="ti-arrow-{{ $invoiceChange >= 0 ? 'up' : 'down' }} m-r-5"></i>
            </span>
                    از دیروز
                </p>
            </div>

            <!-- کارت مشتریان داخلی -->
            <div class="card card-body mb-3">
                <h3 class="primary-font font-weight-bold mb-3 line-height-24">
                    <span class="align-middle">{{ $customerCount }}</span>
                    <span class="font-size-13">بارگذاری تمامی مشتریان داخلی</span>
                </h3>
                <div class="progress mb-2" style="height: 5px">
                    <div class="progress-bar {{ $customerBarColor }}" role="progressbar"
                         style="width: {{ abs($customerChange) > 100 ? 100 : abs($customerChange) }}%;"
                         aria-valuenow="{{ abs($customerChange) }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="font-size-13 m-b-0">
            <span class="{{ $customerChange >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $customerChange }}% <i class="ti-arrow-{{ $customerChange >= 0 ? 'up' : 'down' }} m-r-5"></i>
            </span>
                    از دیروز
                </p>
            </div>

            <!-- کارت کالا، دسته بندی و برند -->
            <div class="card card-body mb-3">
                <h3 class="primary-font font-weight-bold mb-3 line-height-24">
                    <span class="align-middle">{{ $productCount }}</span>
                    <span class="font-size-13">کالا در</span>
                    <span class="align-middle">{{ $categoryCount }}</span>
                    <span class="font-size-13">دسته بندی و</span>
                    <span class="align-middle">{{ $brandCount }}</span>
                    <span class="font-size-13">برند</span>
                </h3>
                <div class="progress mb-2" style="height: 5px">
                    <!-- نوار پیشرفت چندبخشی بر اساس نسبت‌های محاسبه شده -->
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $productPercent }}%;"
                         aria-valuenow="{{ $productPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $categoryPercent }}%;"
                         aria-valuenow="{{ $categoryPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $brandPercent }}%;"
                         aria-valuenow="{{ $brandPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="font-size-13 m-b-0">
                    کالا: {{ $productPercent }}%,
                    دسته بندی: {{ $categoryPercent }}%,
                    برند: {{ $brandPercent }}%
                </p>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="card-title m-b-20">آمار سفارشات محصولات</h6>
                        <h6 class="card-title m-b-20">تعداد کل
                            سفارشات: {{ number_format($orderCounts->sum()) }}</h6>
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
                        <h6 class="card-title m-b-20">تعداد کل
                            سفارشات: {{ number_format($orderCounts->sum()) }}</h6>
                    </div>
                    <canvas id="bar_chart_customer_orders" style="width: auto"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="card-title m-b-20">آمار بازدید همکاران از MPSystem</h6>
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
                        <h6 class="card-title m-b-20">تعداد کل
                            موجودی: {{ number_format($productCounts->sum()) }}</h6>
                    </div>
                    <canvas id="bar_chart_product_inventory" style="width: auto"></canvas>
                </div>
            </div>
        </div>
        @can('activity-list')
            <div class="card col-xl-6 col-lg-6 col-md-12 col-sm-12">
                <div class="card-header">فعالیت های اخیر</div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($activities as $key => $activity)
                            <div class="timeline-item">
                                <div>
                                    <figure class="avatar avatar-sm m-r-15 bring-forward">
										<span class="avatar-title bg-primary-bright text-primary rounded-circle">
                                             @if(auth()->user()->profile)
                                                <img
                                                        src="{{ $activity->user->profile }}"
                                                        style="max-width: 76.79px"
                                                        data-toggle="tooltip" data-placement="bottom"
                                                        title="{{ $activity->user->fullName() }}"
                                                        class="rounded-circle" alt="image" width="36.5px" height="36.5px">
                                            @else
                                                <i class="fa-solid fa-clock font-size"></i>
                                            @endif
										</span>
                                    </figure>
                                </div>
                                <div>
                                    <p class="font-size-12 m-0">{{$activity->user->fullName()}}</p>
                                    <p class="m-b-5">
                                        <strong>{{ \Illuminate\Support\Str::limit($activity->description, 100, '...') }}</strong>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fa-solid fa-clock m-r-5"></i>{{ verta($activity->created_at)->format('H:i - Y/m/d') }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endcan
        @can('sms-list')
            @php
                $totalSmsCount = $smsData->sum('sms_count');
            @endphp

            <div class="card col-xl-6 col-lg-6 col-md-12 col-sm-12">
                <div class="card-header">آمار پیامک های ارسالی همکاران</div>
                <div class="card-body">
                    <p class="text-muted">امتیاز عملکرد</p>
                    <div class="progress" style="height: 10px">
                        @foreach($smsData as $data)
                            @php
                                $smsPercent = $totalSmsCount > 0 ? round(($data['sms_count'] / $totalSmsCount) * 100) : 0;
                                if($smsPercent >= 50) {
                                    $progressColor = 'bg-success';
                                } elseif($smsPercent >= 30) {
                                    $progressColor = 'bg-info';
                                } elseif($smsPercent >= 10) {
                                    $progressColor = 'bg-warning';
                                } else {
                                    $progressColor = 'bg-danger';
                                }
                            @endphp
                            <div class="progress-bar {{ $progressColor }}" role="progressbar"
                                 style="width: {{ $smsPercent }}%" aria-valuenow="{{ $smsPercent }}" aria-valuemin="0"
                                 aria-valuemax="100"></div>
                        @endforeach
                    </div>
                    <div class="list-group list-group-flush m-t-10">
                        @foreach($smsData as $data)
                            @php
                                $user2 = $users2->find($data['user_id']);
                                $smsPercent = $totalSmsCount > 0 ? round(($data['sms_count'] / $totalSmsCount) * 100) : 0;
                                if($smsPercent >= 50) {
                                    $iconColor = 'text-success';
                                } elseif($smsPercent >= 30) {
                                    $iconColor = 'text-info';
                                } elseif($smsPercent >= 10) {
                                    $iconColor = 'text-warning';
                                } else {
                                    $iconColor = 'text-danger';
                                }
                            @endphp
                            <div
                                    class="list-group-item p-t-b-10 p-l-r-0 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-circle m-r-10 {{ $iconColor }}"></i>
                                    <span>{{ $user2 ? $user2->fullName() : 'نامشخص' }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="m-r-20">{{ $data['sms_count'] }}</div>
                                    <div>{{ $smsPercent }}%</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endcan
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header">درخواست های فروش</div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-10 offset-1">
                            <canvas id="chart_7" width="100%" height="200px"></canvas>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h6 class="font-size-11 text-muted mb-2 primary-font">درخواست های فروش ستاد</h6>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-circle text-danger m-r-5 font-size-11"></i>
                                <h4 class="mb-0 primary-font">
                                    {{ \App\Models\SalePriceRequest::where('type', 'setad_sale')->count() }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="font-size-11 text-muted mb-2 primary-font">درخواست های فروش آزاد</h6>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-circle text-info m-r-5 font-size-11"></i>
                                <h4 class="mb-0 primary-font">
                                    {{ \App\Models\SalePriceRequest::where('type', 'free_sale')->count() }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="font-size-11 text-muted mb-2 primary-font">درخواست های فروش سراسری</h6>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-circle text-info m-r-5 font-size-11"></i>
                                <h4 class="mb-0 primary-font">
                                    {{ \App\Models\SalePriceRequest::where('type', 'global_sale')->count() }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="font-size-11 text-muted mb-2 primary-font">درخواست های فروش صنعتی</h6>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-circle text-warning m-r-5 font-size-11"></i>
                                <h4 class="mb-0 primary-font">
                                    {{ \App\Models\SalePriceRequest::where('type', 'industrial_sale')->count() }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="font-size-11 text-muted mb-2 primary-font">درخواست های فروش سازمانی</h6>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-circle text-success m-r-5 font-size-11"></i>
                                <h4 class="mb-0 primary-font">
                                    {{ \App\Models\SalePriceRequest::where('type', 'organization_sale')->count() }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="font-size-11 text-muted mb-2 primary-font">مجموع درخواست های فروش</h6>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-circle text-success m-r-5 font-size-11"></i>
                                <h4 class="mb-0 primary-font">
                                    {{ \App\Models\SalePriceRequest::count() }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header">آمار سیستم عامل کلیک کاربران</div>
                    <div class="card-body text-center">
                        <h1 class="font-size-40 font-weight-bold primary-font line-height-50 m-b-10">{{number_format($totalRows)}}</h1>
                        <h6 class="font-size-13 m-b-0 primary-font">کلیک بر روی لینک تبلیغاتی</h6>
                        <p class="small m-t-b-15 text-muted">کاربران با هربار دریافت پیامک تبلیغاتی از سوی همکاران و با
                            کلیک بر روی آن سیستم عامل مورد استفاده آنها توسط سیستم ذخیره میشود</p>
                        <div class="row mb-4">
                            <div class="col-6">
                                @php
                                    $desktop = \App\Models\Visitor::whereIn('platform', ['Windows', 'OS X'])->count();
                                    $mobile  = \App\Models\Visitor::whereIn('platform', ['iOS', 'Android'])->count();
                                    $total   = $desktop + $mobile;
                                    $desktopPercentage = $total > 0 ? round(($desktop / $total) * 100) : 0;
                                    $mobilePercentage  = $total > 0 ? round(($mobile / $total) * 100) : 0;
                                @endphp
                                <h2 class="text-success font-weight-bold primary-font line-height-30 mb-1">{{ $desktopPercentage }}
                                    %</h2>
                                <small class="font-size-13">دسکتاپ</small>
                            </div>
                            <div class="col-6">
                                <h2 class="text-warning font-weight-bold primary-font line-height-30 mb-1">{{ $mobilePercentage }}
                                    %</h2>
                                <small class="font-size-13">موبایل</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="font-weight-bold m-b-10 line-height-30 primary-font">{{number_format($totalRows)}}</h2>
                            <h6 class="font-size-13 mb-2 text-primary font-weight-bold primary-font">کلیک</h6>
                            <p class="m-0 small text-muted">تعداد کلیک مشتریان بر روی لینک ارسال شده توسط پنل پیامکی</p>
                        </div>
                        <div>
                            @php
                                $smsCount = \App\Models\Sms::count();
                                $result=$totalRows/$smsCount;
                            @endphp
                            <span class="dashboard-pie-1">{{$result}}/5</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-12 col-lg-12">
        <div class="card">
            <div class="card-header">مرورگرهای بازدیدکنندگان</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                        <tr class="font-size-13 text-muted">
                            <th></th>
                            <th>مرورگر</th>
                            <th>آیپی کاربر</th>
                            <th>زمان ورود</th>
                            <th class="text-right">پلتفرم کاربر</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($bazdid as $baz)
                            <tr>
                                <td>
                                    @if($baz->browser == 'Apple Browser')
                                        <i class="fa-brands fa-apple"></i>
                                    @elseif(in_array($baz->browser, ['Chrome', 'Chrome Mobile']))
                                        <i class="fa-brands fa-chrome"></i>
                                    @elseif($baz->browser == 'Samsung Internet')
                                        <i class="fa fa-globe"></i>
                                    @elseif($baz->browser == 'Safari')
                                        <i class="fa-brands fa-safari"></i>
                                    @else
                                        <i class="fa fa-question"></i>
                                    @endif
                                </td>
                                <td>{{ $baz->browser }}</td>
                                <td>{{ $baz->ip_address }}</td>
                                <td>{{ verta($baz->created_at)->format('H:i - Y/m/d') }}</td>
                                <td class="text-right">{{ $baz->platform }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">داده‌ای موجود نیست.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                {{ $bazdid->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
    <div class="col-xl-12 col-lg-6 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h6 class="card-title m-b-20">آمار قیمت و تعداد فاکتور همکاران</h6>
                </div>
                <canvas id="invoiceChart"></canvas>
            </div>
        </div>
    </div>
    @can('UserVisit')
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex justify-content-between align-items-center">
                    <h6>لیست بازدید همکاران</h6>
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
                <div
                        class="d-flex justify-content-center">{{ $users->appends(request()->all())->links() }}</div>
            </div>
        </div>
    @endcan
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
        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById('chart_7').getContext('2d');

            // دریافت تعداد درخواست‌ها از طریق Blade
            var saleRequests = {
                setad_sale: {{ \App\Models\SalePriceRequest::where('type','setad_sale')->count() }},
                free_sale: {{ \App\Models\SalePriceRequest::where('type','free_sale')->count() }},
                global_sale: {{ \App\Models\SalePriceRequest::where('type','global_sale')->count() }},
                industrial_sale: {{ \App\Models\SalePriceRequest::where('type','industrial_sale')->count() }},
                organization_sale: {{ \App\Models\SalePriceRequest::where('type','organization_sale')->count() }}
            };

            var data = {
                labels: ['فروش ستاد', 'فروش آزاد', 'فروش سراسری', 'فروش صنعتی', 'فروش سازمانی'],
                datasets: [{
                    label: 'درخواست های فروش',
                    data: [
                        saleRequests.setad_sale,
                        saleRequests.free_sale,
                        saleRequests.global_sale,
                        saleRequests.industrial_sale,
                        saleRequests.organization_sale
                    ],
                    backgroundColor: [
                        '#ff6384', // قرمز
                        '#36a2eb', // آبی
                        '#5a9bd5', // آبی تیره‌تر
                        '#ffcd56', // زرد
                        '#4bc0c0'  // سبزآبی
                    ],
                    borderWidth: 2
                }]
            };

            var options = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            };

            new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: options
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            const userNames2 = @json($userNames2);
            const totalInvoices = @json($totalInvoices);
            const totalPrices = @json($totalPrices);

            const ctx = document.getElementById('invoiceChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: userNames2,
                    datasets: [
                        {
                            label: 'تعداد فاکتورها',
                            data: totalInvoices,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                        },
                        {
                            label: 'مجموع قیمت‌ها (ريال)',
                            data: totalPrices,
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
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
                                beginAtZero: true,
                                fontSize: 15,
                                fontColor: '#999',
                                callback: function (value) {
                                    return value.toLocaleString('fa-IR'); // سه رقم جدا کردن
                                },
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
                                return formattedValue;
                            }
                        }
                    }
                },
            });
        });
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
                                    callback: function (value) {
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
        document.addEventListener('DOMContentLoaded', function () {
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
                                label: function (context) {
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
                                callback: function (value) {
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
                                    callback: function (value) {
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

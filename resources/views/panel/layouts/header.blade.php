<!-- begin::header -->
<div class="header">

    <!-- begin::header logo -->
    <div class="header-logo">
        <a href="/">
            <img class="large-logo" src="/assets/media/image/logo.png" alt="image">
            <img class="small-logo" src="/assets/media/image/logo-sm.png" alt="image">
            <img class="dark-logo" src="/assets/media/image/logo-dark.png" alt="image" width="70px" height="70px">
        </a>
    </div>
    <!-- end::header logo -->

    <!-- begin::header body -->
    <div class="header-body">

        @php
            $segments = Request::segments();
            $mapping = [
                'invoices'=>'پیش فاکتور ها',
    'panel' => 'داشبورد',
    'orders'=>'سفارشات',
    'users' => 'همکاران',
    'activity' => 'فعالیت ها',
    'search' => 'جست و جو',
    'create'=>'ایجاد',
    'edit' => 'ویرایش',
    'show' => 'مشاهده',
    'roles' => 'نقش‌ها',
    'tasks' => 'وظایف',
    'notes' => 'یادداشت‌ها',
    'leaves' => 'مرخصی‌ها',
    'reports' => 'گزارش‌ها',
    'baseinfo' => 'اطلاعات پایه',
    'indicator' => 'شاخص‌ها',
    'inbox' => 'صندوق ورودی نامه ها',
    'indicator' => 'نامه نگاری',
    'suppliers' => 'تأمین‌کنندگان',
    'customers' => 'مشتریان',
    'foreign-customers' => 'مشتریان خارجی',
    'categories' => 'دسته‌بندی‌ها',
    'products' => 'محصولات',
    'price-history' => 'تاریخچه قیمت‌ها',
    'artin-products' => 'محصولات آرتین',
    'other-prices-list' => 'لیست قیمت‌های دیگر',
    'invoices-list' => 'لیست فاکتورها',
    'sale-reports-list' => 'گزارش‌های فروش',
    'price-requests' => 'درخواست‌های قیمت',
    'buy-orders' => 'سفارش‌های خرید',
    'comments' => 'نظرات',
    'delivery-day' => 'روز تحویل',
    'software-updates' => 'تغییرات نرم افزار',
    'sale-price-requests' => 'درخواست‌های قیمت فروش',
    'exchange' => 'ارزها',
    'request'=>'درخواست',
    'productsModel'=>'برندها',
    'tickets' => 'تیکت ها',
    'inventory-reports'=>'گزارش انبار',
    'inventory'=>'انبار',
    'site'=>'سایت',
    'site-orders' => 'سفارشات سایت',
    'site-registered' => 'ثبت نام مشتریان سایت',
    'sale_price_requests'=>'درخواست فروش',
    'Mandegarprice' => 'لیست قیمت ماندگار پارس',
    'order-action' => 'ثبت وضعیت سفارش',
    'mandegar-price' => 'لیست قیمت ماندگار پارس',
];
            $pageTitle = !empty($segments)
                ? ($mapping[$segments[count($segments)-1]] ?? ucfirst($segments[count($segments)-1]))
                : 'داشبورد';
        @endphp

        <div class="header-body-left">
            <h3 class="page-title">@yield('title')</h3>

            <!-- begin::breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href=""></a>
                    </li>
                    @foreach($segments as $key => $segment)
                        @php
                            $translatedSegment = $mapping[$segment] ?? $segment;
                            $url = url(implode('/', array_slice($segments, 0, $key + 1)));
                        @endphp
                        @if($loop->last)
                            <li class="breadcrumb-item active" aria-current="page">{{ $translatedSegment }}</li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ $url }}">{{ $translatedSegment }}</a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
            <!-- end::breadcrumb -->
        </div>

        <div class="header-body-right">
            <!-- begin::navbar main body -->
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <div style="font-size: larger" id="network_sec">
                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="متصل">
                            <i class="fa fa-wifi text-success"></i>
                        </span>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a href="#internalTelModal" class="nav-link" data-toggle="modal">
                        <i class="ti-headphone" data-toggle="tooltip" data-placement="bottom"
                           data-original-title="داخلی همکاران"></i>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="{{ route('logout') }}" class="nav-link"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       data-toggle="tooltip" data-placement="bottom" data-original-title="خروج">
                        <i class="fa fa-power-off"></i>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link" id="darkModeToggle" data-toggle="tooltip"
                       onclick="toggleDark()"
                       data-placement="bottom" data-original-title="حالت شب">
                        <i class="fa fa-moon" id="darkModeIcon"></i>
                    </a>
                </li>
                <li class="nav-item dropdown" id="notification_sec">
                    <a href="#"
                       class="nav-link {{ auth()->user()->unreadNotifications->count() ? 'nav-link-notify' : '' }}"
                       data-toggle="dropdown">
                        <i class="ti-bell" data-toggle="tooltip" data-placement="bottom"
                           data-original-title="اعلانات"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-big">
                        <div class="p-4 text-center" data-backround-image="/assets/media/image/image1.png">
                            <h6 class="m-b-0">اعلان ها</h6>
                            <small class="font-size-13 opacity-7"><span
                                    id="notif_count">{{ auth()->user()->unreadNotifications->count() }}</span> اعلان
                                خوانده نشده</small>
                        </div>
                        <div class="p-3" style="overflow-y: auto; max-height: 400px;">
                            <div class="timeline">
                                @foreach(auth()->user()->unreadNotifications->take(10) as $notification)
                                    <div class="timeline-item">
                                        <div>
                                            <figure class="avatar avatar-state-danger avatar-sm m-r-15 bring-forward">
												<span
                                                    class="avatar-title bg-primary-bright text-primary rounded-circle">
													<i class="fa fa-bell font-size-20"></i>
												</span>
                                            </figure>
                                        </div>
                                        <div>
                                            <p class="m-b-5">
                                                <a href="{{ route('notifications.read', $notification->id) }}">{{ $notification->data['message'] }}</a>
                                            </p>
                                            <small class="text-muted">
                                                <i class="fa fa-clock-o m-r-5"></i>{{ \Carbon\Carbon::parse($notification->created_at)->ago() }}
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="p-3 text-right">
                            <ul class="list-inline small">
                                <li class="list-inline-item">
                                    <a href="{{ route('notifications.read') }}">علامت خوانده شده به همه</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a href="javascript:void(0)" data-sidebar-open="#userProfile" class="nav-link bg-none">
                        <div>
                            <figure class="avatar avatar-state-success avatar-sm">
                                @if(auth()->user()->profile)
                                    <img
                                        src="{{ auth()->user()->profile }}"
                                        style="max-height: 36.79px; max-width: 36.79px"
                                        data-toggle="tooltip" data-placement="bottom"
                                        title="{{ auth()->user()->fullName() }}"
                                        class="rounded-circle" alt="image">
                                @elseif(auth()->user()->gender == 'female')
                                    <img src="{{ asset('assets/media/image/Female.png') }}"
                                         data-toggle="tooltip" data-placement="bottom"
                                         title="{{ auth()->user()->fullName() }}"
                                         class="rounded-circle" alt="image">
                                @elseif(auth()->user()->gender == 'male')
                                    <img src="{{ asset('assets/media/image/Male.png') }}"
                                         data-toggle="tooltip" data-placement="bottom"
                                         title="{{ auth()->user()->fullName() }}"
                                         class="rounded-circle" alt="image">
                                @else
                                    <img src="{{ asset('assets/media/image/inquery.png') }}"
                                         data-toggle="tooltip" data-placement="bottom"
                                         title="{{ auth()->user()->fullName() }}"
                                         class="rounded-circle" alt="image">
                                @endif
                            </figure>
                        </div>
                    </a>
                </li>
            </ul>
            <!-- end::navbar main body -->

            <div class="d-flex align-items-center">

                <!-- begin::navbar navigation toggler -->
                <div class="d-xl-none d-lg-none d-sm-block navigation-toggler">
                    <a href="#">
                        <i class="ti-menu"></i>
                    </a>
                </div>
                <!-- end::navbar navigation toggler -->

                <!-- begin::navbar toggler -->
                <div class="d-xl-none d-lg-none d-sm-block navbar-toggler">
                    <a href="#">
                        <i class="ti-arrow-down"></i>
                    </a>
                </div>
                <!-- end::navbar toggler -->
            </div>
        </div>

    </div>
    <!-- end::header body -->
</div>
<!-- end::header -->

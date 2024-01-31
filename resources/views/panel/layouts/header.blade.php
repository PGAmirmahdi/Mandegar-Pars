<!-- begin::header -->
<div class="header">

    <!-- begin::header logo -->
    <div class="header-logo">
        <a href="/">
            <img class="large-logo" src="/assets/media/image/logo.png" alt="image">
            <img class="small-logo" src="/assets/media/image/logo-sm.png" alt="image">
{{--            <img class="dark-logo" src="assets/media/image/logo-dark.png" alt="image">--}}
        </a>
    </div>
    <!-- end::header logo -->

    <!-- begin::header body -->
    <div class="header-body">

        <div class="header-body-left">

            <h3 class="page-title">داشبورد</h3>

            <!-- begin::breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">داشبورد</a></li>
                    <li class="breadcrumb-item active" aria-current="page">فروش و مدیریت مشتری</li>
                </ol>
            </nav>
            <!-- end::breadcrumb -->

        </div>

        <div class="header-body-right">
            <!-- begin::navbar main body -->
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                     <div style="font-size: larger" id="network_sec">
                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="connected">
                            <i class="fa fa-wifi text-success"></i>
                        </span>
                     </div>
                </li>
                <li class="nav-item dropdown">
                    <a href="#internalTelModal" class="nav-link" data-toggle="modal">
                        <i class="ti-headphone" data-toggle="tooltip" data-placement="bottom" data-original-title="داخلی همکاران"></i>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" data-toggle="tooltip" data-placement="bottom" data-original-title="خروج">
                        <i class="fa fa-power-off"></i>
                    </a>
                </li>
                <li class="nav-item dropdown" id="notification_sec">
                    <a href="#" class="nav-link {{ auth()->user()->unreadNotifications->count() ? 'nav-link-notify' : '' }}" data-toggle="dropdown">
                        <i class="ti-bell" data-toggle="tooltip" data-placement="bottom" data-original-title="اعلانات"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-big">
                        <div class="p-4 text-center" data-backround-image="/assets/media/image/image1.png">
                            <h6 class="m-b-0">اعلان ها</h6>
                            <small class="font-size-13 opacity-7"><span id="notif_count">{{ auth()->user()->unreadNotifications->count() }}</span> اعلان خوانده نشده</small>
                        </div>
                        <div class="p-3" style="overflow-y: auto; max-height: 400px;">
                            <div class="timeline">
                                @foreach(auth()->user()->unreadNotifications as $notification)
                                    <div class="timeline-item">
                                        <div>
                                            <figure class="avatar avatar-state-danger avatar-sm m-r-15 bring-forward">
												<span class="avatar-title bg-primary-bright text-primary rounded-circle">
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
                    <a href="javascript:void(0)" class="nav-link bg-none">
                        <div>
                            <figure class="avatar avatar-state-success avatar-sm">
                                <img src="/assets/media/image/avatar.png" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ auth()->user()->fullName() }}" class="rounded-circle" alt="image">
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

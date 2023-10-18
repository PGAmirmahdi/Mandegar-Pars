<!-- begin::navigation -->
<div class="navigation">
    <div class="navigation-icon-menu">
        <ul>
            <li class="{{ active_sidebar(['panel','users','users/create','users/{user}/edit','roles','roles/create','roles/{role}/edit', 'tasks','tasks/create','tasks/{task}/edit', 'tasks/{task}']) ? 'active' : '' }}" data-toggle="tooltip" title="داشبورد">
                <a href="#navigationDashboards" title="داشبوردها">
                    <i class="icon ti-dashboard"></i>
                </a>
            </li>
            @canany(['categories-list','products-list','printers-list'])
                <li class="{{ active_sidebar(['categories','categories/create','categories/{category}/edit','products','products/create','products/{product}/edit','printers','printers/create','printers/{printer}/edit','coupons','coupons/create','coupons/{coupon}/edit']) ? 'active' : '' }}" data-toggle="tooltip" title="محصولات">
                    <a href="#navigationProducts" title="محصولات">
                        <i class="icon ti-view-list"></i>
                    </a>
                </li>
            @endcanany
            @canany(['invoices-list'])
            <li class="{{ active_sidebar(['invoices','invoices/create','invoices/{invoice}/edit','search/invoices']) ? 'active' : '' }}" data-toggle="tooltip" title="پیش فاکتور">
                <a href="#navigationInvoices" title="پیش فاکتور">
                    <i class="icon ti-shopping-cart"></i>
                </a>
            </li>
            @endcanany
            @canany(['packets-list'])
                <li class="{{ active_sidebar(['packets','packets/create','packets/{packet}/edit','search/packets']) ? 'active' : '' }}" data-toggle="tooltip" title="بسته های ارسالی">
                    <a href="#navigationPackets" title="بسته های ارسالی">
                        <i class="icon ti-package"></i>
                    </a>
                </li>
            @endcanany
            @canany(['customers-list'])
                <li class="{{ active_sidebar(['customers','customers/create','customers/{customer}/edit','search/customers']) ? 'active' : '' }}" data-toggle="tooltip" title="مشتریان">
                    <a href="#navigationCustomers" title="مشتریان">
                        <i class="icon ti-user"></i>
                    </a>
                </li>
            @endcanany
        </ul>
        <ul>
            <li data-toggle="tooltip" title="ویرایش پروفایل">
                <a href="{{ route('users.edit', auth()->id()) }}" class="go-to-page">
                    <i class="icon ti-settings"></i>
                </a>
            </li>
            <li data-toggle="tooltip" title="خروج">
                <a href="{{ route('logout') }}" class="go-to-page" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                    <i class="icon ti-power-off"></i>
                </a>
            </li>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </ul>
    </div>
    <div class="navigation-menu-body">
        <ul id="navigationDashboards" class="{{ active_sidebar(['panel','users','users/create','users/{user}/edit','roles','roles/create','roles/{role}/edit', 'tasks','tasks/create','tasks/{task}/edit', 'tasks/{task}']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">داشبورد</li>
            <li>
                <a class="{{ active_sidebar(['panel']) ? 'active' : '' }}" href="{{ route('panel') }}">پنل</a>
            </li>
            @can('users-list')
                <li>
                    <a class="{{ active_sidebar(['users','users/create','users/{user}/edit']) ? 'active' : '' }}" href="{{ route('users.index') }}">کاربران</a>
                </li>
            @endcan
            @can('roles-list')
                <li>
                    <a class="{{ active_sidebar(['roles','roles/create','roles/{role}/edit']) ? 'active' : '' }}" href="{{ route('roles.index') }}">نقش ها</a>
                </li>
            @endcan
            @can('tasks-list')
                <li>
                    <a class="{{ active_sidebar(['tasks','tasks/create','tasks/{task}/edit', 'tasks/{task}']) ? 'active' : '' }}" href="{{ route('tasks.index') }}">وظایف</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationProducts" class="{{ active_sidebar(['categories','categories/create','categories/{category}/edit','products','products/create','products/{product}/edit','printers','printers/create','printers/{printer}/edit','coupons','coupons/create','coupons/{coupon}/edit']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">محصولات</li>
{{--            @can('categories-list')--}}
{{--                <li>--}}
{{--                    <a class="{{ active_sidebar(['categories','categories/create','categories/{category}/edit']) ? 'active' : '' }}" href="{{ route('categories.index') }}">دسته بندی ها</a>--}}
{{--                </li>--}}
{{--            @endcan--}}
            @can('products-list')
                <li>
                    <a class="{{ active_sidebar(['products','products/create','products/{product}/edit']) ? 'active' : '' }}" href="{{ route('products.index') }}">محصولات</a>
                </li>
            @endcan
            @can('printers-list')
                <li>
                    <a class="{{ active_sidebar(['printers','printers/create','printers/{printer}/edit']) ? 'active' : '' }}" href="{{ route('printers.index') }}">پرینتر ها</a>
                </li>
            @endcan
            @can('coupons-list')
                <li>
                    <a class="{{ active_sidebar(['coupons','coupons/create','coupons/{coupon}/edit']) ? 'active' : '' }}" href="{{ route('coupons.index') }}">کد تخفیف</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationInvoices" class="{{ active_sidebar(['invoices','invoices/create','invoices/{invoice}/edit','search/invoices']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">پیش فاکتور</li>
            @can('invoices-list')
                <li>
                    <a class="{{ active_sidebar(['invoices','invoices/create','invoices/{invoice}/edit','search/invoices']) ? 'active' : '' }}" href="{{ route('invoices.index') }}">پیش فاکتور</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationPackets" class="{{ active_sidebar(['packets','packets/create','packets/{packet}/edit','search/packets']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">بسته های ارسالی</li>
            @can('packets-list')
                <li>
                    <a class="{{ active_sidebar(['packets','packets/create','packets/{packet}/edit','search/packets']) ? 'active' : '' }}" href="{{ route('packets.index') }}">بسته های ارسالی</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationCustomers" class="{{ active_sidebar(['customers','customers/create','customers/{customer}/edit','search/customers']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">مشتریان</li>
            @can('customers-list')
                <li>
                    <a class="{{ active_sidebar(['customers','customers/create','customers/{customer}/edit','search/customers']) ? 'active' : '' }}" href="{{ route('customers.index') }}">مشتریان</a>
                </li>
            @endcan
        </ul>
    </div>
</div>
<!-- end::navigation -->

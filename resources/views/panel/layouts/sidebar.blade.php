<!-- begin::navigation -->
<div class="navigation">
    <div class="navigation-icon-menu" style="overflow-y: auto">
        <ul>
            <li class="{{ active_sidebar(['panel','users','users/create','users/{user}/edit','roles','roles/create','roles/{role}/edit', 'tasks','tasks/create','tasks/{task}/edit', 'tasks/{task}', 'notes','notes/create','notes/{note}/edit','leaves','leaves/create','leaves/{leave}/edit']) ? 'active' : '' }}" data-toggle="tooltip" title="داشبورد">
                <a href="#navigationDashboards" title="داشبوردها">
                    <i class="icon ti-dashboard"></i>
                </a>
            </li>
            @canany(['categories-list','products-list','printers-list','prices-list'])
                <li class="{{ active_sidebar(['categories','categories/create','categories/{category}/edit','products','products/create','products/{product}/edit','search/products','printers','printers/create','printers/{printer}/edit','coupons','coupons/create','coupons/{coupon}/edit','prices-list', 'price-history']) ? 'active' : '' }}" data-toggle="tooltip" title="محصولات">
                    <a href="#navigationProducts" title="محصولات">
                        <i class="icon ti-view-list"></i>
                    </a>
                </li>
            @endcanany
            @canany(['invoices-list'])
            <li class="{{ active_sidebar(['invoices','invoices/create','invoices/{invoice}/edit','search/invoices','factors','factors/create','factors/{factor}/edit','search/factors','sale-reports','sale-reports/create','sale-reports/{sale_report}/edit','search/sale-reports']) ? 'active' : '' }}" data-toggle="tooltip" title="صورتحساب">
                <a href="#navigationInvoices" title="صورتحساب">
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
            @canany(['shops'])
                <li class="{{ active_sidebar(['off-site-products/{website}','off-site-product/{off_site_product}','off-site-product-create/{website}','off-site-products/{off_site_product}/edit',]) ? 'active' : '' }}" data-toggle="tooltip" title="فروشگاه ها">
                    <a href="#navigationShops" title="فروشگاه ها">
                        <i class="icon ti-new-window"></i>
                    </a>
                </li>
            @endcanany
            @canany(['inventory-list','input-reports-list','output-reports-list'])
                <li class="{{ active_sidebar(['inventory','inventory/create','inventory/{inventory}/edit','search/inventory','inventory-reports','inventory-reports/create','inventory-reports/{inventory_report}/edit']) ? 'active' : '' }}" data-toggle="tooltip" title="انبار">
                    <a href="#navigationInventory" title="انبار">
                        <i class="icon ti-check-box"></i>
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
        <ul id="navigationDashboards" class="{{ active_sidebar(['panel','users','users/create','users/{user}/edit','roles','roles/create','roles/{role}/edit', 'tasks','tasks/create','tasks/{task}/edit', 'tasks/{task}', 'notes','notes/create','notes/{note}/edit', 'leaves','leaves/create','leaves/{leave}/edit']) ? 'navigation-active' : '' }}">
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
            @can('notes-list')
                <li>
                    <a class="{{ active_sidebar(['notes','notes/create','notes/{note}/edit']) ? 'active' : '' }}" href="{{ route('notes.index') }}">یادداشت ها</a>
                </li>
            @endcan
            @can('leaves-list')
                <li>
                    <a class="{{ active_sidebar(['leaves','leaves/create','leaves/{leave}/edit']) ? 'active' : '' }}" href="{{ route('leaves.index') }}">درخواست مرخصی</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationProducts" class="{{ active_sidebar(['categories','categories/create','categories/{category}/edit','products','products/create','products/{product}/edit','search/products','printers','printers/create','printers/{printer}/edit','coupons','coupons/create','coupons/{coupon}/edit','prices-list', 'price-history']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">محصولات</li>
{{--            @can('categories-list')--}}
{{--                <li>--}}
{{--                    <a class="{{ active_sidebar(['categories','categories/create','categories/{category}/edit']) ? 'active' : '' }}" href="{{ route('categories.index') }}">دسته بندی ها</a>--}}
{{--                </li>--}}
{{--            @endcan--}}
            @can('products-list')
                <li>
                    <a class="{{ active_sidebar(['products','products/create','products/{product}/edit','search/products']) ? 'active' : '' }}" href="{{ route('products.index') }}">محصولات</a>
                </li>
            @endcan
            @can('prices-list')
                <li>
                    <a class="{{ active_sidebar(['prices-list']) ? 'active' : '' }}" href="{{ route('prices-list') }}">لیست قیمت ها</a>
                </li>
            @endcan
            @can('price-history')
                <li>
                    <a class="{{ active_sidebar(['price-history']) ? 'active' : '' }}" href="{{ route('price-history') }}">تاریخچه قیمت ها</a>
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
        <ul id="navigationInvoices" class="{{ active_sidebar(['invoices','invoices/create','invoices/{invoice}/edit','search/invoices','factors','factors/create','factors/{factor}/edit','search/factors','sale-reports','sale-reports/create','sale-reports/{sale_report}/edit','search/sale-reports']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">صورتحساب</li>
            @can('invoices-list')
                <li>
                    <a class="{{ active_sidebar(['invoices','invoices/create','invoices/{invoice}/edit','search/invoices']) ? 'active' : '' }}" href="{{ route('invoices.index') }}">پیش فاکتور</a>
                </li>
                <li>
                    <a class="{{ active_sidebar(['factors','factors/create','factors/{factor}/edit','search/factors']) ? 'active' : '' }}" href="{{ route('factors.index') }}">فاکتور</a>
                </li>
            @endcan
            @can('sale-reports-list')
                <li>
                    <a class="{{ active_sidebar(['sale-reports','sale-reports/create','sale-reports/{sale_report}/edit','search/sale-reports']) ? 'active' : '' }}" href="{{ route('sale-reports.index') }}">گزارشات فروش</a>
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
        <ul id="navigationShops" class="{{ active_sidebar(['off-site-products/{website}','off-site-product-create/{website}','off-site-products/{off_site_product}/edit','off-site-product/{off_site_product}']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">فروشگاه ها</li>
            <li>
                <a class="{{ active_sidebar(['off-site-products/{website}','off-site-product/{off_site_product}','off-site-product-create/{website}','off-site-products/{off_site_product}/edit']) && request()->website == 'torob' ? 'active' : '' }}" href="{{ route('off-site-products.index', 'torob') }}">
                    <img src="{{ asset('assets/media/image/shop-logo/torob.svg') }}" style="width: 1.5rem">
                    <span class="ml-2">ترب</span>
                </a>
            </li>
            <li>
                <a class="{{ active_sidebar(['off-site-products/{website}','off-site-product/{off_site_product}','off-site-product-create/{website}','off-site-products/{off_site_product}/edit']) && request()->website == 'digikala' ? 'active' : '' }}" href="{{ route('off-site-products.index', 'digikala') }}">
                    <img src="{{ asset('assets/media/image/shop-logo/digikala.png') }}" style="width: 1.5rem">
                    <span class="ml-2">دیجی کالا</span>
                </a>
            </li>
            <li>
                <a class="{{ active_sidebar(['off-site-products/{website}','off-site-product/{off_site_product}','off-site-product-create/{website}','off-site-products/{off_site_product}/edit']) && request()->website == 'emalls' ? 'active' : '' }}" href="{{ route('off-site-products.index', 'emalls') }}">
                    <img src="{{ asset('assets/media/image/shop-logo/emalls.png') }}" style="width: 1.5rem">
                    <span class="ml-2">ایمالز</span>
                </a>
            </li>
        </ul>
        <ul id="navigationInventory" class="{{ active_sidebar(['inventory','inventory/create','inventory/{inventory}/edit','search/inventory','inventory-reports','inventory-reports/create','inventory-reports/{inventory_report}/edit']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">انبار</li>
            @can('inventory-list')
                <li>
                    <a class="{{ active_sidebar(['inventory','inventory/create','inventory/{inventory}/edit','search/inventory']) ? 'active' : '' }}" href="{{ route('inventory.index') }}">کالا ها</a>
                </li>
            @endcan
            @can('input-reports-list')
                <li>
                    <a class="{{ active_sidebar(['inventory-reports','inventory-reports/create','inventory-reports/{inventory_report}/edit']) && request()->type == 'input' ? 'active' : '' }}" href="{{ route('inventory-reports.index', ['type' => 'input']) }}">ورود</a>
                </li>
            @endcan
            @can('output-reports-list')
                <li>
                    <a class="{{ active_sidebar(['inventory-reports','inventory-reports/create','inventory-reports/{inventory_report}/edit']) && request()->type == 'output' ? 'active' : '' }}" href="{{ route('inventory-reports.index', ['type' => 'output']) }}">خروج</a>
                </li>
            @endcan
        </ul>
    </div>
</div>
<!-- end::navigation -->

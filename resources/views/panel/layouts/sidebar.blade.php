<!-- begin::navigation -->
<div class="navigation">
    <div class="navigation-icon-menu" style="overflow-y: auto">
        <ul>
            <li class="{{ active_sidebar(['panel','users','users/create','users/{user}/edit','roles','roles/create','roles/{role}/edit', 'tasks','tasks/create','tasks/{task}/edit', 'tasks/{task}', 'notes','notes/create','notes/{note}/edit','leaves','leaves/create','leaves/{leave}/edit','reports','reports/create','reports/{report}/edit','software-updates','software-updates/create','software-updates/{software_update}/edit']) ? 'active' : '' }}" data-toggle="tooltip" title="داشبورد">
                <a href="#navigationDashboards" title="داشبوردها">
                    <i class="icon ti-dashboard"></i>
                </a>
            </li>
            @canany(['categories-list','products-list','printers-list','prices-list'])
                <li class="{{ active_sidebar(['categories','categories/create','categories/{category}/edit','products','products/create','products/{product}/edit','search/products','printers','printers/create','printers/{printer}/edit','search/printers','coupons','coupons/create','coupons/{coupon}/edit','prices-list', 'price-history', 'artin-products', 'other-prices-list']) ? 'active' : '' }}" data-toggle="tooltip" title="محصولات">
                    <a href="#navigationProducts" title="محصولات">
                        <i class="icon ti-view-list"></i>
                    </a>
                </li>
            @endcanany
            @canany(['invoices-list','sale-reports-list','price-requests','buy-orders','delivery-day'])
            <li class="{{ active_sidebar(['invoices','invoices/create','invoices/{invoice}/edit','search/invoices','sale-reports','sale-reports/create','sale-reports/{sale_report}/edit','search/sale-reports','invoice-action/{invoice}','orders-status/{invoice}','price-requests','price-requests/create','price-requests/{price_request}/edit','price-requests/{price_request}','buy-orders','buy-orders/create','buy-orders/{buy_order}/edit','buy-orders/{buy_order}','search/buy-orders','delivery-days']) ? 'active' : '' }}" data-toggle="tooltip" title="سفارشات و فروش">
                <a href="#navigationInvoices" title="سفارشات و فروش">
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
            @canany(['customers-list','foreign-customers-list'])
                <li class="{{ active_sidebar(['customers','customers/create','customers/{customer}/edit','search/customers','foreign-customers','foreign-customers/create','foreign-customers/{foreign_customer}/edit','search/foreign-customers']) ? 'active' : '' }}" data-toggle="tooltip" title="مشتریان">
                    <a href="#navigationCustomers" title="مشتریان">
                        <i class="icon ti-user"></i>
                    </a>
                </li>
            @endcanany
            @canany(['tickets-list','sms-histories'])
                <li class="{{ active_sidebar(['tickets','tickets/create','tickets/{ticket}/edit','search/tickets','sms-histories','sms-histories/{sms_history}']) ? 'active' : '' }}" data-toggle="tooltip" title="پشتیبانی و تیکت">
                    <a href="#navigationTickets" title="پشتیبانی و تیکت">
                        <i class="icon ti-comment-alt"></i>
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
                <li class="{{ active_sidebar(['inventory','inventory/create','inventory/{inventory}/edit','search/inventory','inventory-reports','inventory-reports/create','inventory-reports/{inventory_report}/edit','warehouses','warehouses/create','warehouses/{warehouse}/edit','search/inventory-reports','guarantees','guarantees/create','guarantees/{guarantee}/edit']) ? 'active' : '' }}" data-toggle="tooltip" title="انبار">
                    <a href="#navigationInventory" title="انبار">
                        <i class="icon ti-package "></i>
                    </a>
                </li>
            @endcanany
            @can('exit-door')
                <li class="{{ active_sidebar(['exit-door','exit-door/create','exit-door/{exit_door}/edit','search/exit-door']) ? 'active' : '' }}" data-toggle="tooltip" title="درب خروج">
                    <a href="#navigationExitDoor" title="درب خروج">
                        <i class="icon ti-check-box"></i>
                    </a>
                </li>
            @endcan
{{--            @can('bot-manager')--}}
{{--                <li class="{{ active_sidebar(['bot-profile']) ? 'active' : '' }}" data-toggle="tooltip" title="ربات تلگرام">--}}
{{--                    <a href="#navigationBot" title="ربات تلگرام">--}}
{{--                        <i class="icon fa fa-robot"></i>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            @endcan--}}
        </ul>
        <ul>
            <li data-toggle="tooltip" title="نسخه های برنامه">
                <a href="{{ route('app.versions') }}" class="go-to-page">
                    <i class="fa fa-code icon"></i>
                </a>
            </li>
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
        <ul id="navigationDashboards" class="{{ active_sidebar(['panel','users','users/create','users/{user}/edit','roles','roles/create','roles/{role}/edit', 'tasks','tasks/create','tasks/{task}/edit', 'tasks/{task}', 'notes','notes/create','notes/{note}/edit', 'leaves','leaves/create','leaves/{leave}/edit','reports','reports/create','reports/{report}/edit','software-updates','software-updates/create','software-updates/{software_update}/edit']) ? 'navigation-active' : '' }}">
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
            @can('reports-list')
                <li>
                    <a class="{{ active_sidebar(['reports','reports/create','reports/{report}/edit']) ? 'active' : '' }}" href="{{ route('reports.index') }}">گزارشات روزانه</a>
                </li>
            @endcan
            @can('software-updates-list')
                <li>
                    <a class="{{ active_sidebar(['software-updates','software-updates/create','software-updates/{software_update}/edit']) ? 'active' : '' }}" href="{{ route('software-updates.index') }}">تغییرات نرم افزار</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationProducts" class="{{ active_sidebar(['categories','categories/create','categories/{category}/edit','products','products/create','products/{product}/edit','search/products','printers','printers/create','printers/{printer}/edit','coupons','coupons/create','coupons/{coupon}/edit','prices-list', 'price-history','search/printers','artin-products','other-prices-list']) ? 'navigation-active' : '' }}">
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
            @can('artin-products-list')
                <li>
                    <a class="{{ active_sidebar(['artin-products']) ? 'active' : '' }}" href="{{ route('artin.products') }}">محصولات آرتین</a>
                </li>
            @endcan
            @can('prices-list')
                <li>
                    <a class="{{ active_sidebar(['prices-list']) ? 'active' : '' }}" href="{{ route('prices-list') }}">لیست قیمت ماندگار پارس</a>
                </li>
                <li>
                    <a class="{{ active_sidebar(['other-prices-list']) ? 'active' : '' }}" href="{{ route('other-prices-list') }}">لیست قیمت تامین کنندگان</a>
                </li>
            @endcan
            @can('price-history')
                <li>
                    <a class="{{ active_sidebar(['price-history']) ? 'active' : '' }}" href="{{ route('price-history') }}">تاریخچه قیمت ها</a>
                </li>
            @endcan
            @can('printers-list')
                <li>
                    <a class="{{ active_sidebar(['printers','printers/create','printers/{printer}/edit','search/printers']) ? 'active' : '' }}" href="{{ route('printers.index') }}">پرینتر ها</a>
                </li>
            @endcan
            @can('coupons-list')
                <li>
                    <a class="{{ active_sidebar(['coupons','coupons/create','coupons/{coupon}/edit']) ? 'active' : '' }}" href="{{ route('coupons.index') }}">کد تخفیف</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationInvoices" class="{{ active_sidebar(['invoices','invoices/create','invoices/{invoice}/edit','search/invoices','factors','factors/create','factors/{factor}/edit','search/factors','sale-reports','sale-reports/create','sale-reports/{sale_report}/edit','search/sale-reports', 'invoice-action/{invoice}','orders-status/{invoice}','price-requests','price-requests/create','price-requests/{price_request}/edit','price-requests/{price_request}','buy-orders','buy-orders/create','buy-orders/{buy_order}/edit','buy-orders/{buy_order}','search/buy-orders','delivery-days','cheque','cheque/create','cheque/{cheque}/edit','cheque/{cheque}']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">سفارشات و فروش</li>
            @can('invoices-list')
                <li>
                    <a class="{{ active_sidebar(['invoices','invoices/create','invoices/{invoice}/edit','search/invoices','invoice-action/{invoice}','orders-status/{invoice}']) ? 'active' : '' }}" href="{{ route('invoices.index') }}">سفارشات فروش</a>
                </li>
            @endcan
            @can('buy-orders-list')
                <li>
                    <a class="{{ active_sidebar(['buy-orders','buy-orders/create','buy-orders/{buy_order}/edit','buy-orders/{buy_order}','search/buy-orders']) ? 'active' : '' }}" href="{{ route('buy-orders.index') }}">سفارشات خرید</a>
                </li>
            @endcan
            @can('delivery-day')
                <li>
                    <a class="{{ active_sidebar(['delivery-days']) ? 'active' : '' }}" href="{{ route('delivery-days.index') }}">روز های تحویل سفارش</a>
                </li>
            @endcan
            @can('sale-reports-list')
                <li>
                    <a class="{{ active_sidebar(['sale-reports','sale-reports/create','sale-reports/{sale_report}/edit','search/sale-reports']) ? 'active' : '' }}" href="{{ route('sale-reports.index') }}">گزارشات فروش</a>
                </li>
            @endcan
            @can('price-requests-list')
                <li>
                    <a class="{{ active_sidebar(['price-requests','price-requests/create','price-requests/{price_request}/edit','price-requests/{price_request}']) ? 'active' : '' }}" href="{{ route('price-requests.index') }}">درخواست قیمت</a>
                </li>
            @endcan
            @can('cheque-check-list')
                <li>
                    <a class="{{ active_sidebar(['cheque','cheque/create','cheque/{cheque}/edit','cheque/{cheque}']) ? 'active' : '' }}" href="{{ route('cheque.index') }}">درخواست وضعیت چک</a>
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
        <ul id="navigationCustomers" class="{{ active_sidebar(['customers','customers/create','customers/{customer}/edit','search/customers','foreign-customers','foreign-customers/create','foreign-customers/{foreign_customer}/edit','search/foreign-customers']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">مشتریان</li>
            @can('customers-list')
                <li>
                    <a class="{{ active_sidebar(['customers','customers/create','customers/{customer}/edit','search/customers']) ? 'active' : '' }}" href="{{ route('customers.index') }}">مشتریان</a>
                </li>
            @endcan
            @can('foreign-customers-list')
                <li>
                    <a class="{{ active_sidebar(['foreign-customers','foreign-customers/create','foreign-customers/{foreign_customer}/edit','search/foreign-customers']) ? 'active' : '' }}" href="{{ route('foreign-customers.index') }}">مشتریان خارجی</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationTickets" class="{{ active_sidebar(['tickets','tickets/create','tickets/{ticket}/edit','search/tickets','sms-histories','sms-histories/{sms_history}']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">پشتیبانی و تیکت</li>
            @can('tickets-list')
                <li>
                    <a class="{{ active_sidebar(['tickets','tickets/create','tickets/{ticket}/edit','search/tickets']) ? 'active' : '' }}" href="{{ route('tickets.index') }}">تیکت ها</a>
                </li>
            @endcan
            @can('sms-histories')
                <li>
                    <a class="{{ active_sidebar(['sms-histories','sms-histories/{sms_history}']) ? 'active' : '' }}" href="{{ route('sms-histories.index') }}">پیام های ارسال شده</a>
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
        <ul id="navigationInventory" class="{{ active_sidebar(['inventory','inventory/create','inventory/{inventory}/edit','search/inventory','inventory-reports','inventory-reports/create','inventory-reports/{inventory_report}/edit','warehouses','warehouses/create','warehouses/{warehouse}/edit','search/inventory-reports','guarantees','guarantees/create','guarantees/{guarantee}/edit']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">انبار</li>
            @can('guarantees-list')
                <li>
                    <a class="{{ active_sidebar(['guarantees','guarantees/create','guarantees/{guarantee}/edit']) ? 'active' : '' }}" href="{{ route('guarantees.index') }}">گارانتی ها</a>
                </li>
            @endcan
            @can('warehouses-list')
                <li>
                    <a class="{{ active_sidebar(['warehouses','warehouses/create','warehouses/{warehouse}/edit']) ? 'active' : '' }}" href="{{ route('warehouses.index') }}">انبار</a>
                </li>
            @endcan
            @if(request()->warehouse_id)
                @can('inventory-list')
                    <li>
                        <a class="{{ active_sidebar(['inventory','inventory/create','inventory/{inventory}/edit','search/inventory']) ? 'active' : '' }}" href="{{ route('inventory.index', ['warehouse_id' => request()->warehouse_id]) }}">کالا ها</a>
                    </li>
                @endcan
                @can('input-reports-list')
                    <li>
                        <a class="{{ active_sidebar(['inventory-reports','inventory-reports/create','inventory-reports/{inventory_report}/edit','search/inventory-reports']) && request()->type == 'input' ? 'active' : '' }}" href="{{ route('inventory-reports.index', ['type' => 'input', 'warehouse_id' => request()->warehouse_id]) }}">ورود</a>
                    </li>
                @endcan
                @can('output-reports-list')
                    <li>
                        <a class="{{ active_sidebar(['inventory-reports','inventory-reports/create','inventory-reports/{inventory_report}/edit','search/inventory-reports']) && request()->type == 'output' ? 'active' : '' }}" href="{{ route('inventory-reports.index', ['type' => 'output', 'warehouse_id' => request()->warehouse_id]) }}">خروج</a>
                    </li>
                @endcan
            @endif
        </ul>
        <ul id="navigationExitDoor" class="{{ active_sidebar(['exit-door','exit-door/create','exit-door/{exit_door}/edit','search/exit-door']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">درب خروج</li>
            @can('exit-door')
                <li>
                    <a class="{{ active_sidebar(['exit-door','exit-door/create','exit-door/{exit_door}/edit','search/exit-door']) ? 'active' : '' }}" href="{{ route('exit-door.index') }}">ثبت خروج</a>
                </li>
            @endcan
        </ul>
{{--        <ul id="navigationBot" class="{{ active_sidebar(['bot-profile']) ? 'navigation-active' : '' }}">--}}
{{--            <li class="navigation-divider">ربات تلگرام</li>--}}
{{--            <li>--}}
{{--                <a class="{{ active_sidebar(['bot-profile']) ? 'active' : '' }}" href="{{ route('bot.profile') }}">مشخصات ربات</a>--}}
{{--            </li>--}}
{{--        </ul>--}}
    </div>
</div>
<!-- end::navigation -->

<!-- begin::navigation -->
<div class="navigation">
    <div class="navigation-icon-menu"
         style="overflow-y: auto; @if(auth()->user()->family == 'رسولی') background-color: #dba6fc !important; @endif">
        <ul>
            <li class="{{ active_sidebar(['activity','search/activity','panel','users','users/create','users/{user}/edit','roles','roles/create','roles/{role}/edit', 'tasks','tasks/create','tasks/{task}/edit', 'tasks/{task}', 'notes','notes/create','notes/{note}/edit','leaves','leaves/create','leaves/{leave}/edit','reports','reports/create','reports/{report}/edit','software-updates','software-updates/create','software-updates/{software_update}/edit','baseinfo','baseinfo/create','baseinfo/{baseinfo}/edit']) ? 'active' : '' }}"
                data-toggle="tooltip" title="داشبورد">
                <a href="#navigationDashboards" title="داشبوردها">
                    <i class="icon ti-dashboard"></i>
                </a>
            </li>
            @canany(['categories-list','products-list','printers-list','prices-list','foreign-customers-list','customers-list','debtors-list'])
                <li class="{{ active_sidebar(['request/products','debtors/{debtor}','debtors','debtors/create','debtors/{debtor}/edit','search/debtors','analyse/show/{date}','analyse/*','analyse','analyse/create','analyse/{analyse}/edit','search/analyse','customers','customers/create','customers/{customer}/edit','customers/{customer}','search/customers','foreign-customers','foreign-customers/create','foreign-customers/{foreign_customer}/edit','search/foreign-customers','productsModel','productsModel/create','productsModel/{productsModel}/edit','categories','categories/create','categories/{category}/edit','products','products/create','products/{product}/edit','search/products','printers','printers/create','printers/{printer}/edit','search/printers','coupons','coupons/create','coupons/{coupon}/edit','prices-list', 'price-history','price-history-search', 'artin-products', 'other-prices-list']) ? 'active' : '' }}"
                    data-toggle="tooltip" title="عملیات پایه">
                    <a href="#navigationProducts" title="عملیات پایه">
                        <i class="icon ti-view-list"></i>
                    </a>
                </li>
            @endcanany
            @canany(['invoices-list','sale-reports-list','price-requests','buy-orders','delivery-day','sale-price-requests'])
                <li class="{{ active_sidebar(['sale_price_requests','sale_price_requests/create','sale_price_requests/{sale_price_request}/edit','sale_price_requests/{sale_price_request}','orders','orders/create','orders/{order}/edit','search/orders','order-action/{orders}','customer-orders-status/{orders}','order-action/{order}','setad-fee','setad-fee/create','setad-fee/{order}/action','setad-fee/{setad_fee}/edit','setad-fee/{setad_fee}','costs/{cost}/accounting','costs','costs/create','costs/{cost}/edit','invoices','invoices/create','invoices/{invoice}/edit','search/invoices','sale-reports','sale-reports/create','sale-reports/{sale_report}/edit','search/sale-reports','invoice-action/{invoice}','orders-status/{invoice}','price-requests','price-requests/create','price-requests/{price_request}/edit','price-requests/{price_request}','buy-orders','buy-orders/create','buy-orders/{buy_order}/edit','buy-orders/{buy_order}','search/buy-orders','delivery-days']) ? 'active' : '' }}"
                    data-toggle="tooltip" title="سفارشات">
                    <a href="#navigationInvoices" title="سفارشات">
                        <i class="icon ti-shopping-cart"></i>
                    </a>
                </li>
            @endcanany
            @canany(['packets-list','transporters-list'])
                <li class="{{ active_sidebar(['costs/{cost}/finalaccountantupdate','panel/transports/{id}/finalaccounting','transports/{transport}/finalaccounting','transports/{transport}/storeBijak','transports/{transport}/bijak','transports/{transport}/accountantupdate','transports/{transport}/accounting','transports','transports/create','transports/{transport}/edit','search/transports','transporters','transporters/create','transporters/{transporter}/edit','search/transporters','packets','packets/create','packets/{packet}/edit','search/packets']) ? 'active' : '' }}"
                    data-toggle="tooltip" title="بسته های ارسالی">
                    <a href="#navigationPackets" title="بسته های ارسالی">
                        <i class="icon ti-package"></i>
                    </a>
                </li>
            @endcanany
            @canany(['sms-list','whatsapp-list'])
                <li class="{{ active_sidebar(['sms','sms/create','sms/{sms}/edit','sms.search','sms/{sms}','whatsapp','whatsapp/create','whatsapp/createGroup','whatsapp/{whatsapp}']) ? 'active' : '' }}"
                    data-toggle="tooltip" title="پنل">
                    <a href="#navigationCustomers" title="پنل">
                        <i class="icon ti-user"></i>
                    </a>
                </li>
            @endcanany
            @canany(['tickets-list','sms-histories','sms'])
                <li class="{{ active_sidebar(['tickets','tickets/create','tickets/{ticket}/edit','search/tickets','sms-histories','sms-histories/{sms_history}','chat_messages','chat_messages/create','chat_messages/{chat_message}/edit','search/chat_messages']) ? 'active' : '' }}"
                    data-toggle="tooltip" title="پشتیبانی و تیکت">
                    <a href="#navigationTickets" title="پشتیبانی و تیکت">
                        <i class="icon ti-comment-alt"></i>
                    </a>
                </li>
            @endcanany
            @canany(['shops'])
                <li class="{{ active_sidebar(['off-site-products/{website}','off-site-product/{off_site_product}','off-site-product-create/{website}','off-site-products/{off_site_product}/edit',]) ? 'active' : '' }}"
                    data-toggle="tooltip" title="فروشگاه ها">
                    <a href="#navigationShops" title="فروشگاه ها">
                        <i class="icon ti-new-window"></i>
                    </a>
                </li>
            @endcanany
            @canany(['inventory-list','input-reports-list','output-reports-list'])
                <li class="{{ active_sidebar(['inventory','inventory/create','inventory/{inventory}/edit','search/inventory','inventory-reports','inventory-reports/create','inventory-reports/{inventory_report}/edit','warehouses','warehouses/create','warehouses/{warehouse}/edit','search/inventory-reports','guarantees','guarantees/create','guarantees/{guarantee}/edit']) ? 'active' : '' }}"
                    data-toggle="tooltip" title="انبار">
                    <a href="#navigationInventory" title="انبار">
                        <i class="icon ti-package "></i>
                    </a>
                </li>
            @endcanany
            @can('exit-door')
                <li class="{{ active_sidebar(['exit-door','exit-door/create','exit-door/{exit_door}/edit','search/exit-door']) ? 'active' : '' }}"
                    data-toggle="tooltip" title="درب خروج">
                    <a href="#navigationExitDoor" title="درب خروج">
                        <i class="icon ti-check-box"></i>
                    </a>
                </li>
            @endcan
            @can('folder-list')
                <li class="{{ active_sidebar(['files','files/create','files/create-folder','files/folder/{folder}']) ? 'active' : '' }}"
                    data-toggle="tooltip" title="مدیریت فایل">
                    <a href="#navigationFileControl" title="مدیریت فایل">
                        <i class="icon ti-folder"></i>
                    </a>
                </li>
            @endcan
            @can('Telegram-bot')
                <li class="{{ active_sidebar(['bot-profile']) ? 'active' : '' }}" data-toggle="tooltip"
                    title="ربات تلگرام">
                    <a href="#navigationBot" title="ربات تلگرام">
                        <i class="icon fa fa-robot"></i>
                    </a>
                </li>
            @endcan
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
        <ul id="navigationDashboards"
            class="{{ active_sidebar(['activity','search/activity','panel','users','users/create','users/{user}/edit','roles','roles/create','roles/{role}/edit', 'tasks','tasks/create','tasks/{task}/edit', 'tasks/{task}', 'notes','notes/create','notes/{note}/edit', 'leaves','leaves/create','leaves/{leave}/edit','reports','reports/create','reports/{report}/edit','software-updates','software-updates/create','software-updates/{software_update}/edit','baseinfo','baseinfo/create','baseinfo/{baseinfo}/edit']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">داشبورد</li>
            <li>
                <a class="{{ active_sidebar(['panel']) ? 'active' : '' }}" href="{{ route('panel') }}">پنل</a>
            </li>
            @can('users-list')
                <li>
                    <a class="{{ active_sidebar(['users','users/create','users/{user}/edit']) ? 'active' : '' }}"
                       href="{{ route('users.index') }}">همکاران</a>
                </li>
            @endcan
            @can('roles-list')
                <li>
                    <a class="{{ active_sidebar(['roles','roles/create','roles/{role}/edit']) ? 'active' : '' }}"
                       href="{{ route('roles.index') }}">نقش ها</a>
                </li>
            @endcan
            @can('activity-list')
                <li>
                    <a class="{{ active_sidebar(['activity','search/activity']) ? 'active' : '' }}"
                       href="{{ route('activity') }}">فعالیت ها</a>
                </li>
            @endcan
            @can('tasks-list')
                <li>
                    <a class="{{ active_sidebar(['tasks','tasks/create','tasks/{task}/edit', 'tasks/{task}']) ? 'active' : '' }}"
                       href="{{ route('tasks.index') }}">وظایف</a>
                </li>
            @endcan
            @can('notes-list')
                <li>
                    <a class="{{ active_sidebar(['notes','notes/create','notes/{note}/edit']) ? 'active' : '' }}"
                       href="{{ route('notes.index') }}">یادداشت ها</a>
                </li>
            @endcan
            @can('leaves-list')
                <li>
                    <a class="{{ active_sidebar(['leaves','leaves/create','leaves/{leave}/edit']) ? 'active' : '' }}"
                       href="{{ route('leaves.index') }}">درخواست مرخصی</a>
                </li>
            @endcan
            @can('reports-list')
                <li>
                    <a class="{{ active_sidebar(['reports','reports/create','reports/{report}/edit']) ? 'active' : '' }}"
                       href="{{ route('reports.index') }}">گزارشات روزانه</a>
                </li>
            @endcan
            @can('software-updates-list')
                <li>
                    <a class="{{ active_sidebar(['software-updates','software-updates/create','software-updates/{software_update}/edit']) ? 'active' : '' }}"
                       href="{{ route('software-updates.index') }}">تغییرات نرم افزار</a>
                </li>
            @endcan
            <li>
                <a class="{{ active_sidebar(['product_models','product_models/create','product_models/{product_model}/edit','baseinfo','baseinfo/create','baseinfo/{baseinfo}/edit']) ? 'active' : '' }}"
                   href="{{ route('baseinfo.index') }}">اطلاعات</a>
            </li>
        </ul>
        <ul id="navigationProducts"
            class="{{ active_sidebar(['debtors','debtors/create','debtors/{debtor}','debtors/{debtor}/edit','search/debtors','analyse/show/{date}','analyse/*','analyse','analyse/create','analyse/{analyse}/edit','search/analyse','foreign-customers','foreign-customers/create','foreign-customers/{foreign_customer}/edit','customers/{customer}','search/foreign-customers','customers','customers/create','customers/{customer}/edit','search/customers','productsModel','productsModel/create','productsModel/{productsModel}/edit','categories','categories/create','categories/{category}/edit','products','products/create','products/{product}/edit','search/products','printers','printers/create','printers/{printer}/edit','coupons','coupons/create','coupons/{coupon}/edit','prices-list', 'price-history','price-history-search','search/printers','artin-products','other-prices-list','request/products']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">عملیات پایه</li>
            @can('products-list')
                <li>
                    <a class="{{ active_sidebar(['products','products/create','products/{product}/edit','search/products','request/products']) ? 'active' : '' }}"
                       href="{{ route('products.index') }}">بارگذاری تمامی کالاها</a>
                </li>
            @endcan
            @can('artin-products-list')
                <li>
                    <a class="{{ active_sidebar(['artin-products']) ? 'active' : '' }}"
                       href="{{ route('artin.products') }}">کالاهای آرتین</a>
                </li>
            @endcan
            @can('categories-list')
                <li>
                    <a class="{{ active_sidebar(['categories','categories/create','categories/{category}/edit']) ? 'active' : '' }}"
                       href="{{ route('categories.index') }}">دسته بندی ها</a>
                </li>
            @endcan
            @can('productsModel-list')
                <li>
                    <a class="{{ active_sidebar(['productsModel','productsModel/create','productsModel/{productsModel}/edit']) ? 'active' : '' }}"
                       href="{{ route('productsModel.index') }}">برند ها</a>
                </li>
            @endcan
            @can('customers-list')
                <li>
                    <a class="{{ active_sidebar(['customers','customers/create','customers/{customer}','customers/{customer}/edit','search/customers']) ? 'active' : '' }}"
                       href="{{ route('customers.index') }}">بارگذاری تمامی مشتریان داخلی</a>
                </li>
            @endcan
            @can('foreign-customers-list')
                <li>
                    <a class="{{ active_sidebar(['foreign-customers','foreign-customers/create','foreign-customers/{foreign_customer}/edit','search/foreign-customers']) ? 'active' : '' }}"
                       href="{{ route('foreign-customers.index') }}">بارگذاری تمامی مشتریان خارجی</a>
                </li>
            @endcan
            @can('debtors-list')
                <li>
                    <a class="{{ active_sidebar(['debtors','debtors/create','debtors/{debtor}/edit','debtors/{debtor}','search/debtors']) ? 'active' : '' }}"
                       href="{{ route('debtors.index') }}">لیست بدهکاران</a>
                </li>
            @endcan
            @can('printers-list')
                <li>
                    <a class="{{ active_sidebar(['printers','printers/create','printers/{printer}/edit','search/printers']) ? 'active' : '' }}"
                       href="{{ route('printers.index') }}">پرینتر ها</a>
                </li>
            @endcan
            <li>
                <a class="{{ active_sidebar(['analyse/show/{date}','analyse','analyse/create','analyse/*','analyse/{analyse}/edit','search/analyse']) ? 'active' : '' }}"
                   href="{{ route('analyse.index') }}">مدیریت آنالیز کالا تطبیقی</a>
            </li>
            @can('prices-list')
                {{--                <li>--}}
                {{--                    <a class="{{ active_sidebar(['prices-list']) ? 'active' : '' }}" href="{{ route('prices-list') }}">لیست قیمت ماندگار پارس</a>--}}
                {{--                </li>--}}
                <li>
                    <a class="{{ active_sidebar(['other-prices-list']) ? 'active' : '' }}"
                       href="{{ route('other-prices-list') }}">لیست قیمت کف بازار</a>
                </li>
            @endcan
            @can('price-history')
                <li>
                    <a class="{{ active_sidebar(['price-history','price-history-search']) ? 'active' : '' }}"
                       href="{{ route('price-history') }}">عملیات آرشیو قیمت ها</a>
                </li>
            @endcan
            @can('coupons-list')
                <li>
                    <a class="{{ active_sidebar(['coupons','coupons/create','coupons/{coupon}/edit']) ? 'active' : '' }}"
                       href="{{ route('coupons.index') }}">کد تخفیف</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationInvoices"
            class="{{ active_sidebar(['sale_price_requests/action/{sale_price_request}','sale_price_requests','sale_price_requests/create','sale_price_requests/{sale_price_request}/edit','sale_price_requests/{sale_price_request}','orders','orders/create','orders/{order}/edit','search/orders','order-action/{orders}','customer-orders-status/{orders}','panel/customers/{customer}','order-action/{order}','setad-fee','setad-fee/create','setad-fee/{order}/action','setad-fee/{setad_fee}/edit','setad-fee/{setad_fee}','costs/{cost}/accounting','costs','costs/create','costs/{cost}/edit','invoices','invoices/create','invoices/{invoice}/edit','search/invoices','factors','factors/create','factors/{factor}/edit','search/factors','sale-reports','sale-reports/create','sale-reports/{sale_report}/edit','search/sale-reports', 'invoice-action/{invoice}','orders-status/{invoice}','price-requests','price-requests/create','price-requests/{price_request}/edit','price-requests/{price_request}','buy-orders','buy-orders/create','buy-orders/{buy_order}/edit','buy-orders/{buy_order}','search/buy-orders','delivery-days','cheque','cheque/create','cheque/{cheque}/edit','cheque/{cheque}']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">سفارشات</li>
            @can('costs-list')
                <li>
                    <a class="{{ active_sidebar(['costs/{cost}/accounting','costs','costs/create','costs/{cost}/edit']) ? 'active' : '' }}"
                       href="{{ route('costs.index') }}">بهای تمام شده</a>
                </li>
            @endcan
            @can('invoices-list')
                <li>
                    <a class="{{ active_sidebar(['invoices','invoices/create','invoices/{invoice}/edit','search/invoices','invoice-action/{invoice}','orders-status/{invoice}']) ? 'active' : '' }}"
                       href="{{ route('invoices.index') }}">پیش فاکتور ها</a>
                </li>
            @endcan
            @can('customer-order-list')
                <li>
                    <a class="{{ active_sidebar(['orders','orders/create','orders/{order}/edit','search/orders','order-action/{orders}','customer-orders-status/{orders}','order-action/{order}']) ? 'active' : '' }}"
                       href="{{ route('orders.index') }}">سفارش مشتری</a>
                </li>
            @endcan
            @can('setad-fee-list')
                <li>
                    <a class="{{ active_sidebar(['setad-fee','setad-fee/create','setad-fee/{order}/action','setad-fee/{setad_fee}/edit','setad-fee/{setad_fee}']) ? 'active' : '' }}"
                       href="{{ route('setad-fee.index') }}">کارمزد ستاد</a>
                </li>
            @endcan
            @can('buy-orders-list')
                <li>
                    <a class="{{ active_sidebar(['buy-orders','buy-orders/create','buy-orders/{buy_order}/edit','buy-orders/{buy_order}','search/buy-orders']) ? 'active' : '' }}"
                       href="{{ route('buy-orders.index') }}">سفارشات خرید</a>
                </li>
            @endcan
            @can('delivery-day')
                <li>
                    <a class="{{ active_sidebar(['delivery-days']) ? 'active' : '' }}"
                       href="{{ route('delivery-days.index') }}">روز های تحویل سفارش</a>
                </li>
            @endcan
            @can('sale-reports-list')
                <li>
                    <a class="{{ active_sidebar(['sale-reports','sale-reports/create','sale-reports/{sale_report}/edit','search/sale-reports']) ? 'active' : '' }}"
                       href="{{ route('sale-reports.index') }}">گزارشات فروش</a>
                </li>
            @endcan
            {{--                <li>--}}
            {{--                    <a--}}
            {{--                        class="{{ active_sidebar(['sale_price_requests', 'sale_price_requests/create', 'sale_price_requests/{sale_price_request}/edit', 'sale_price_requests/{sale_price_request}']) ? 'active' : '' }}"--}}
            {{--                        href="{{ route('sale_price_requests.index') }}">--}}
            {{--                        {{ auth()->user()->role->name == 'setad_sale' || auth()->user()->role->name == 'internet_sale' || auth()->user()->role->name == 'free_sale' || auth()->user()->role->name == 'industrial_sale' || auth()->user()->role->name == 'global_sale' || auth()->user()->role->name == 'organization_sale'--}}
            {{--                            ? ' درخواست ' . auth()->user()->role->label--}}
            {{--                            : 'درخواست های فروش' }}--}}
            {{--                    </a>--}}
            {{--                </li>--}}
            @php
                $roles = [
                    'free_sale' => 'درخواست فروش آزاد',
                    'global_sale' => 'درخواست فروش سراسری',
                    'setad_sale' => 'درخواست فروش ستاد',
                    'organization_sale' => 'درخواست فروش سازمانی',
                    'industrial_sale' => 'درخواست فروش صنعتی'
                ];
                $userRole = auth()->user()->role->name;
            @endphp

            @if(in_array($userRole, array_keys($roles)))
                <li>
                    <a class="{{ active_sidebar(['sale_price_requests', 'sale_price_requests/action/{sale_price_request}', 'sale_price_requests/create', 'sale_price_requests/{sale_price_request}/edit', 'sale_price_requests/{sale_price_request}']) ? 'active' : '' }}"
                       href="{{ url('/panel/sale_price_requests?type=' . $userRole) }}">{{ $roles[$userRole] }}</a>
                </li>
            @elseif(in_array($userRole, ['ceo', 'admin', 'office-manager']))
                @foreach($roles as $key => $label)
                    <li>
                        <a class="{{ request()->query('type') === $key || active_sidebar(['sale_price_requests/action/{sale_price_request}']) ? 'active' : '' }}"
                           href="{{ url('/panel/sale_price_requests?type=' . $key) }}">{{ $label }}</a>
                    </li>
                @endforeach
            @endif
        @can('price-requests-list')
                <li>
                    <a class="{{ active_sidebar(['price-requests','price-requests/create','price-requests/{price_request}/edit','price-requests/{price_request}']) ? 'active' : '' }}"
                       href="{{ route('price-requests.index') }}">درخواست قیمت</a>
                </li>
            @endcan
            @can('cheque-check-list')
                <li>
                    <a class="{{ active_sidebar(['cheque','cheque/create','cheque/{cheque}/edit','cheque/{cheque}']) ? 'active' : '' }}"
                       href="{{ route('cheque.index') }}">درخواست وضعیت چک</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationPackets"
            class="{{ active_sidebar(['panel/transports/{id}/finalaccounting','transports/{transport}/finalaccountantupdate','transports/{transport}/finalaccounting','transports/{transport}/storeBijak','transports/{transport}/bijak','transports/{transport}/accountantupdate','transports/{transport}/accounting','transports','transports/create','transports/{transport}/edit','search/transports','transporters','transporters/create','transporters/{transporter}/edit','search/transporters','packets','packets/create','packets/{packet}/edit','search/packets']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">بسته های ارسالی</li>
            @can('packets-list')
                <li>
                    <a class="{{ active_sidebar(['packets','packets/create','packets/{packet}/edit','search/packets']) ? 'active' : '' }}"
                       href="{{ route('packets.index') }}">بسته های ارسالی</a>
                </li>
            @endcan
            @can('transporters-list')
                <li>
                    <a class="{{ active_sidebar(['transporters','transporters/create','transporters/{transporter}/edit','search/transporters']) ? 'active' : '' }}"
                       href="{{ route('transporters.index') }}">تعاونی ارسال بار</a>
                </li>
            @endcan
            @can('transport-list')
                <li>
                    <a class="{{ active_sidebar(['panel/transports/{id}/finalaccounting','transports/{transport}/finalaccountantupdate','transports/{transport}/finalaccounting','transports/{transport}/storeBijak','transports/{transport}/bijak','transports/{transport}/accountantupdate','transports/{transport}/accounting','transports','transports/create','transports/{transport}/edit','search/transports']) ? 'active' : '' }}"
                       href="{{ route('transports.index') }}">حمل و نقل</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationCustomers"
            class="{{ active_sidebar(['sms','sms/create','sms/{sms}/edit','sms.search','sms/{sms}','whatsapp','whatsapp/create','whatsapp/createGroup','whatsapp/{whatsapp}']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">پنل</li>
            @can('sms-list')
                <li>
                    <a class="{{ active_sidebar(['sms','sms/create','sms/{sms}']) ? 'active' : '' }}"
                       href="{{ route('sms.index') }}">پنل پیامکی</a>
                </li>
            @endcan
            @can('whatsapp-list')
                <li>
                    <a class="{{ active_sidebar(['whatsapp','whatsapp/create','whatsapp/createGroup','whatsapp/{whatsapp}']) ? 'active' : '' }}"
                       href="{{ route('whatsapp.index') }}">پنل واتساپی</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationTickets"
            class="{{ active_sidebar(['tickets','tickets/create','tickets/{ticket}/edit','search/tickets','sms-histories','sms-histories/{sms_history}','chat_messages','chat_messages/create','chat_messages/{chat_message}/edit','search/chat_messages']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">پشتیبانی و تیکت</li>
            <li>
                <a class="{{ active_sidebar(['chat_messages','chat_messages/create','chat_messages/{chat_message}/edit','search/chat_messages']) ? 'active' : '' }}"
                   href="{{ route('chat_messages.index') }}">ام پی چت</a>
            </li>
            @can('tickets-list')
                <li>
                    <a class="{{ active_sidebar(['tickets','tickets/create','tickets/{ticket}/edit','search/tickets']) ? 'active' : '' }}"
                       href="{{ route('tickets.index') }}">تیکت ها</a>
                </li>
            @endcan
            @can('sms-histories')
                <li>
                    <a class="{{ active_sidebar(['sms-histories','sms-histories/{sms_history}']) ? 'active' : '' }}"
                       href="{{ route('sms-histories.index') }}">پیام های ارسال شده</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationShops"
            class="{{ active_sidebar(['off-site-products/{website}','off-site-product-create/{website}','off-site-products/{off_site_product}/edit','off-site-product/{off_site_product}']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">فروشگاه ها</li>
            <li>
                <a class="{{ active_sidebar(['off-site-products/{website}','off-site-product/{off_site_product}','off-site-product-create/{website}','off-site-products/{off_site_product}/edit']) && request()->website == 'torob' ? 'active' : '' }}"
                   href="{{ route('off-site-products.index', 'torob') }}">
                    <img src="{{ asset('assets/media/image/shop-logo/torob.svg') }}" style="width: 1.5rem">
                    <span class="ml-2">ترب</span>
                </a>
            </li>
            <li>
                <a class="{{ active_sidebar(['off-site-products/{website}','off-site-product/{off_site_product}','off-site-product-create/{website}','off-site-products/{off_site_product}/edit']) && request()->website == 'digikala' ? 'active' : '' }}"
                   href="{{ route('off-site-products.index', 'digikala') }}">
                    <img src="{{ asset('assets/media/image/shop-logo/digikala.png') }}" style="width: 1.5rem">
                    <span class="ml-2">دیجی کالا</span>
                </a>
            </li>
            <li>
                <a class="{{ active_sidebar(['off-site-products/{website}','off-site-product/{off_site_product}','off-site-product-create/{website}','off-site-products/{off_site_product}/edit']) && request()->website == 'emalls' ? 'active' : '' }}"
                   href="{{ route('off-site-products.index', 'emalls') }}">
                    <img src="{{ asset('assets/media/image/shop-logo/emalls.png') }}" style="width: 1.5rem">
                    <span class="ml-2">ایمالز</span>
                </a>
            </li>
        </ul>
        <ul id="navigationInventory"
            class="{{ active_sidebar(['inventory','inventory/create','inventory/{inventory}/edit','search/inventory','inventory-reports','inventory-reports/create','inventory-reports/{inventory_report}/edit','warehouses','warehouses/create','warehouses/{warehouse}/edit','search/inventory-reports','guarantees','guarantees/create','guarantees/{guarantee}/edit']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">انبار</li>
            @can('guarantees-list')
                <li>
                    <a class="{{ active_sidebar(['guarantees','guarantees/create','guarantees/{guarantee}/edit']) ? 'active' : '' }}"
                       href="{{ route('guarantees.index') }}">گارانتی ها</a>
                </li>
            @endcan
            @can('warehouses-list')
                <li>
                    <a class="{{ active_sidebar(['warehouses','warehouses/create','warehouses/{warehouse}/edit']) ? 'active' : '' }}"
                       href="{{ route('warehouses.index') }}">انبار</a>
                </li>
            @endcan
            @if(request()->warehouse_id)
                @can('inventory-list')
                    <li>
                        <a class="{{ active_sidebar(['inventory','inventory/create','inventory/{inventory}/edit','search/inventory']) ? 'active' : '' }}"
                           href="{{ route('inventory.index', ['warehouse_id' => request()->warehouse_id]) }}">کالا
                            ها</a>
                    </li>
                @endcan
                @can('input-reports-list')
                    <li>
                        <a class="{{ active_sidebar(['inventory-reports','inventory-reports/create','inventory-reports/{inventory_report}/edit','search/inventory-reports']) && request()->type == 'input' ? 'active' : '' }}"
                           href="{{ route('inventory-reports.index', ['type' => 'input', 'warehouse_id' => request()->warehouse_id]) }}">ورود</a>
                    </li>
                @endcan
                @can('output-reports-list')
                    <li>
                        <a class="{{ active_sidebar(['inventory-reports','inventory-reports/create','inventory-reports/{inventory_report}/edit','search/inventory-reports']) && request()->type == 'output' ? 'active' : '' }}"
                           href="{{ route('inventory-reports.index', ['type' => 'output', 'warehouse_id' => request()->warehouse_id]) }}">خروج</a>
                    </li>
                @endcan
            @endif
        </ul>
        <ul id="navigationExitDoor"
            class="{{ active_sidebar(['exit-door','exit-door/create','exit-door/{exit_door}/edit','search/exit-door']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">درب خروج</li>
            @can('exit-door')
                <li>
                    <a class="{{ active_sidebar(['exit-door','exit-door/create','exit-door/{exit_door}/edit','search/exit-door']) ? 'active' : '' }}"
                       href="{{ route('exit-door.index') }}">ثبت خروج</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationFileControl"
            class="{{ active_sidebar(['files','files/create','files/create-folder','files/folder/{folder}']) ? 'navigation-active' : '' }}">
            <li class="navigation-divider">مدیریت فایل</li>
            @can('folder-list')
                <li>
                    <a class="{{ active_sidebar(['files','files/create','files/create-folder','files/folder/{folder}']) ? 'active' : '' }}"
                       href="{{ route('files.index') }}">مدیریت فایل</a>
                </li>
            @endcan
        </ul>
        <ul id="navigationBot" class="{{ active_sidebar(['bot-profile']) ? 'navigation-active' : '' }}">
            @can('Telegram-bot')
                <li class="navigation-divider">ربات تلگرام</li>
                <li>
                    <a class="{{ active_sidebar(['bot-profile']) ? 'active' : '' }}" href="{{ route('bot.profile') }}">مشخصات
                        ربات</a>
                </li>
            @endcan
        </ul>
    </div>
</div>
<!-- end::navigation -->

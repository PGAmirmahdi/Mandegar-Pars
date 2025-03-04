<?php

use App\Events\TestEvent;
use App\Http\Controllers\Api\v1\WhatsappController;
use App\Http\Controllers\Panel\ActivityController;
use App\Http\Controllers\Panel\AnalyseController;
use App\Http\Controllers\Panel\AnalysisController;
use App\Http\Controllers\Panel\ArtinController;
use App\Http\Controllers\Panel\BaseinfoController;
use App\Http\Controllers\Panel\BotController;
use App\Http\Controllers\Panel\BuyOrderCommentController;
use App\Http\Controllers\Panel\BuyOrderController;
use App\Http\Controllers\Panel\CategoryController;
use App\Http\Controllers\Panel\ChatController;
use App\Http\Controllers\Panel\ChatsGPTController;
use App\Http\Controllers\Panel\ChequeController;
use App\Http\Controllers\Panel\CostController;
use App\Http\Controllers\Panel\CouponController;
use App\Http\Controllers\Panel\CustomerController;
use App\Http\Controllers\Panel\DebtorController;
use App\Http\Controllers\Panel\DeliveryDayController;
use App\Http\Controllers\Panel\ExchangeController;
use App\Http\Controllers\Panel\ExitDoorController;
use App\Http\Controllers\Panel\FactorController;
use App\Http\Controllers\Panel\FileController;
use App\Http\Controllers\Panel\ForeignCustomerController;
use App\Http\Controllers\Panel\GuaranteeController;
use App\Http\Controllers\Panel\IndicatorController;
use App\Http\Controllers\Panel\InputController;
use App\Http\Controllers\Panel\InventoryController;
use App\Http\Controllers\Panel\InventoryReportController;
use App\Http\Controllers\Panel\InvoiceController;
use App\Http\Controllers\Panel\LeaveController;
use App\Http\Controllers\Panel\MandegarPriceController;
use App\Http\Controllers\Panel\NoteController;
use App\Http\Controllers\Panel\OffSiteProductController;
use App\Http\Controllers\Panel\OrderController;
use App\Http\Controllers\Panel\OrderStatusController;
use App\Http\Controllers\Panel\PacketController;
use App\Http\Controllers\Panel\PriceController;
use App\Http\Controllers\Panel\PriceRequestController;
use App\Http\Controllers\Panel\PrinterController;
use App\Http\Controllers\Panel\ProductController;
use App\Http\Controllers\Panel\ProductModelController;
use App\Http\Controllers\Panel\ReportController;
use App\Http\Controllers\Panel\RoleController;
use App\Http\Controllers\Panel\SaleReportController;
use App\Http\Controllers\Panel\ScrapController;
use App\Http\Controllers\Panel\SetadFeeController;
use App\Http\Controllers\Panel\SalePriceRequestController;
use App\Http\Controllers\Panel\ShopController;
use App\Http\Controllers\Panel\SMSController;
use App\Http\Controllers\Panel\SmsHistoryController;
use App\Http\Controllers\Panel\SoftwareUpdateController;
use App\Http\Controllers\Panel\SupplierController;
use App\Http\Controllers\Panel\TaskController;
use App\Http\Controllers\Panel\TicketController;
use App\Http\Controllers\Panel\TransportController;
use App\Http\Controllers\Panel\TransporterController;
use App\Http\Controllers\Panel\UserController;
use App\Http\Controllers\Panel\WarehouseController;
use App\Http\Controllers\PanelController;
use App\Models\MandegarPrice;
use App\Models\User;
use App\Models\UserVisit;
use App\Models\Visitor;
use App\Notifications\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use PDF as PDF;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->to('/panel');
    }
    return view('auth.login');
});
Route::get('notif', function () {
//    dd(\auth()->id());
    $title = 'ثبت کالا';
    $message = "یک درخواست ثبت کالا توسط " . 'مجید' . " ایجاد شد.";
    $url = route('products.index');
    $user = User::whereId(195)->first();
//    $user = User::whereId(173)->first();
    Notification::send($user, new SendMessage($title, $message, $url));

});
Route::get("is_sale_manager",function (){
   return \auth()->user()->isSalesManager();
});
Route::get('rolessssssss', function (){
    $userRole = \App\Models\Role::findOrFail(3); // یافتن نقش با آیدی 5

// ایجاد نقش جدید
    $newRole = \App\Models\Role::create([
        'label' => 'فروش ستاد',
        'name' => 'setad_sale',
    ]);

// افزودن دسترسی‌ها به نقش جدید
    if ($userRole->permissions) {
        foreach ($userRole->permissions as $permission) {
            $newRole->permissions()->attach($permission->id);
        }
    }

// بازگرداندن دسترسی‌های نقش جدید برای اطمینان از اختصاص موفق
    return $newRole->permissions;
});
Route::get('test/{id?}', function ($id = null) {
    return \auth()->loginUsingId($id);
//
//    broadcast(new TestEvent(json_encode('dsfsdf')));
//    event(new TestEvent(json_encode('dsfsdf')));
//
////     send sms to customers (install app)
//    set_time_limit(1000000000000000000);
//    $phones_sent = \App\Models\SmsHistory::whereBetween('created_at', ['2024-05-11 12:00:00','2024-05-11 13:59:59'])->pluck('phone')->unique();
//    $phones = App\Models\Customer::whereNotIn('phone1',$phones_sent)->where('phone1','like','09_________')->pluck('phone1')->unique();
//    dd($phones);
//
//    $amount = '5000';
//
//    foreach ($phones as $phone){
//        sendSMS(215126, $phone, [$amount]);
//    }
////     END send sms to customers (install app)
//
//    return \auth()->loginUsingId($id);
//
//    foreach (\App\Models\InventoryReport::where('factor_id', '!=', null)->get() as $item){
//        $item->update(['invoice_id' => $item->factor->invoice_id]);
//    }
});

// import excel
//Route::match(['get','post'],'import-excel', function (Request $request){
//    if ($request->method() == 'POST'){
//        Excel::import(new \App\Imports\PublicImport, $request->file);
//        return back();
//    }else{
//        return view('panel.public-import');
//    }
//})->name('import-excel');

Route::middleware('auth')->prefix('/panel')->group(function () {
    Route::match(['get', 'post'], '/', [PanelController::class, 'index'])->name('panel');
    Route::post('send-sms', [PanelController::class, 'sendSMS'])->name('sendSMS');
    Route::post('najva_token', [PanelController::class, 'najva_token_store']);
    Route::post('saveFcmToken', [PanelController::class, 'saveFCMToken']);
    Route::get('/sales-data', [PanelController::class, 'getSalesData'])->name('sales.data');
    Route::put('/users/{user}/upload-profile', [UserController::class, 'uploadProfile'])->name('users.uploadProfile');
    Route::put('/users/{user}/upload-sign-image', [UserController::class, 'uploadSignImage'])->name('users.uploadSignImage');


    // Users
    Route::resource('users', UserController::class)->except('show');
    Route::get('user/search', [UserController::class, 'search'])->name('User.search');
    Route::get('/file/user/{filename}', [UserController::class, 'userFile'])->name('us.file.show');

    Route::resource('chat_messages', ChatsGPTController::class)->except('edit', 'delete', 'destroy');
    // Roles
    Route::resource('roles', RoleController::class)->except('show');

    // Categories
    Route::resource('categories', CategoryController::class)->except('show');

    // ProductModel
    Route::resource('productsModel', ProductModelController::class)->except('show');

    // Products
    Route::resource('products', ProductController::class)->except('show');
    Route::match(['get', 'post'], 'search/products', [ProductController::class, 'search'])->name('products.search');
    Route::match(['get', 'post'], 'search2/products', [ProductController::class, 'search2'])->name('products.search2');
    Route::match(['get','post'],'request/products', [ProductController::class, 'request'])->name('products.request');
    Route::post('excel/products', [ProductController::class, 'excel'])->name('products.excel');
    Route::post('/get-models-by-category', [ProductController::class, 'getModelsByCategory'])->name('get.models.by.category');
    Route::post('/products/check-duplicate', [ProductController::class, 'checkDuplicate'])->name('products.check_duplicate');

    // Customer Order
    Route::resource('/orders', OrderController::class);
    Route::get('get-customer-order-status/{id}', [OrderController::class, 'getCustomerOrderStatus'])->name('order.get.customer.order.status');
    Route::get('get-customer-order/{code}', [OrderController::class, 'getCustomerOrder'])->name('order.get.customer.order');
    Route::get('order-action/{order}', [OrderController::class, 'orderAction'])->name('order.action');
    Route::post('order-action/{invoice}', [OrderController::class, 'actionStore'])->name('order.action.store');
    Route::put('order-invoice-file/{order_action}/delete', [OrderController::class, 'deleteInvoiceFile'])->name('order.invoice.action.delete');
    Route::put('order-factor-file/{order_action}/delete', [OrderController::class, 'deleteFactorFile'])->name('order.factor.action.delete');

    // Orders
    Route::resource('/orders', OrderController::class);
    Route::get('order-action/{order}', [OrderController::class, 'orderAction'])->name('order.action');
    Route::post('order-action/{invoice}', [OrderController::class, 'actionStore'])->name('order.action.store');
    Route::put('order-invoice-file/{order_action}/delete', [OrderController::class, 'deleteInvoiceFile'])->name('order.invoice.action.delete');
    Route::put('order-factor-file/{order_action}/delete', [OrderController::class, 'deleteFactorFile'])->name('order.factor.action.delete');
//    Route::match(['get', 'post'], '/order/search/orders', [OrderController::class, 'search'])->name('orders.search');
    Route::post('excel/orders', [OrderController::class, 'excel'])->name('orders.excel');

    // Setad
    Route::resource('setad-fee', SetadFeeController::class);
    Route::get('search-setad-fee/{order}', [SetadFeeController::class, 'search']);
    Route::get('setad-fee/{order}/action', [SetadFeeController::class, 'action'])->name('setad-fee.action');
    Route::post('setad-fee/{order}/action/store', [SetadFeeController::class, 'actionStore'])->name('setad-fee.store.action');
    Route::put('receipt-file/{id}/delete', [SetadFeeController::class, 'deleteReceiptFile'])->name('receipt.action.delete');

    // Printers
    Route::resource('printers', PrinterController::class)->except('show');
    Route::match(['get', 'post'], 'search/printers', [PrinterController::class, 'search'])->name('printers.search');


    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::match(['get', 'post'], 'search/invoices', [InvoiceController::class, 'search'])->name('invoices.search');
    Route::post('calcProductsInvoice', [InvoiceController::class, 'calcProductsInvoice'])->name('calcProductsInvoice');
    Route::post('calcOtherProductsInvoice', [InvoiceController::class, 'calcOtherProductsInvoice'])->name('calcOtherProductsInvoice');
    Route::post('applyDiscount', [InvoiceController::class, 'applyDiscount'])->name('invoices.applyDiscount');
    Route::post('excel/invoices', [InvoiceController::class, 'excel'])->name('invoices.excel');
    Route::get('change-status-invoice/{invoice}', [InvoiceController::class, 'changeStatus'])->name('invoices.changeStatus');
    Route::post('downloadPDF', [InvoiceController::class, 'downloadPDF'])->name('invoices.download');
    Route::get('invoice-action/{invoice}', [InvoiceController::class, 'action'])->name('invoice.action');
    Route::post('invoice-action/{invoice}', [InvoiceController::class, 'actionStore'])->name('invoice.action.store');
    Route::put('invoice-file/{invoice_action}/delete', [InvoiceController::class, 'deleteInvoiceFile'])->name('invoice.action.delete');
    Route::put('factor-file/{invoice_action}/delete', [InvoiceController::class, 'deleteFactorFile'])->name('factor.action.delete');


    // Coupons
    Route::resource('coupons', CouponController::class)->except('show');

    // Exchange Price
    Route::resource('exchange', ExchangeController::class)->except('show','edit','update','destroy');
    Route::get('exchange/details/{item}', [ExchangeController::class, 'showDetails'])->name('exchange.details');

    // Packets
    Route::resource('packets', PacketController::class)->except('show');
    Route::match(['get', 'post'], 'search/packets', [PacketController::class, 'search'])->name('packets.search');
    Route::post('excel/packets', [PacketController::class, 'excel'])->name('packets.excel');
    Route::post('get-post-status', [PacketController::class, 'getPostStatus'])->name('get-post-status');
    Route::match(['get','post'],'delivery-verify', [PacketController::class, 'deliveryVerify'])->name('delivery-verify');

    // Customers
    Route::resource('customers', CustomerController::class);
    Route::post('get-customer-info/{customer}', [CustomerController::class, 'getCustomerInfo'])->name('getCustomerInfo');
    Route::match(['get', 'post'], 'search/customers', [CustomerController::class, 'search'])->name('customers.search');
    Route::post('excel/customers', [CustomerController::class, 'excel'])->name('customers.excel');
    Route::get('relevant-customers', [CustomerController::class, 'getRelevantCustomers'])->name('customers.relevant');

    // Suppliers
    Route::resource('suppliers', SupplierController::class);
    Route::post('excel/suppliers', [SupplierController::class, 'excel'])->name('suppliers.excel');
    Route::match(['get', 'post'], 'search/suppliers', [SupplierController::class, 'search'])->name('suppliers.search');
    Route::post('get-supplier-info/{supplier}', [SupplierController::class, 'getSupplierInfo'])->name('getSupplierInfo');
    Route::get('relevant-suppliers', [SupplierController::class, 'getRelevantSuppliers'])->name('suppliers.relevant');

    // Notifications
    Route::get('read-notifications/{notification?}', [PanelController::class, 'readNotification'])->name('notifications.read');

    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::post('task/change-status', [TaskController::class, 'changeStatus']);
    Route::post('task/add-desc', [TaskController::class, 'addDescription']);
    Route::post('task/get-desc', [TaskController::class, 'getDescription']);

    // Notes
    Route::get('notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
    Route::post('notes/delete', [NoteController::class, 'delete'])->name('notes.destroy');
//    Route::post('note/change-status', [NoteController::class, 'changeStatus']);

    // Leaves
    Route::resource('leaves', LeaveController::class)->except('show')->parameters(['leaves' => 'leave']);
    Route::post('get-leave-info', [LeaveController::class, 'getLeaveInfo']);

    // Price List
    Route::get('prices-list', [PriceController::class, 'index'])->name('prices-list');
    Route::match(['get', 'post'], 'other-prices-list', [PriceController::class, 'otherList'])->name('other-prices-list');
    Route::post('update-price', [PriceController::class, 'updatePrice'])->name('updatePrice');
    Route::post('update-price2', [PriceController::class, 'updatePrice2'])->name('updatePrice2');
    Route::post('add-model', [PriceController::class, 'addModel'])->name('addModel');
    Route::post('add-seller', [PriceController::class, 'addSeller'])->name('addSeller');
    Route::post('remove-seller', [PriceController::class, 'removeSeller'])->name('removeSeller');
    Route::post('remove-model', [PriceController::class, 'removeModel'])->name('removeModel');
    Route::get('prices-list/pdf/{type}', [PriceController::class, 'priceList'])->name('prices-list-pdf');
    Route::post('/prices/chart-data', [PriceController::class, 'getPriceChartData'])->name('prices.chart.data');
    Route::get('Mandegarprice', [MandegarPriceController::class, 'index'])->name('Mandegarprice');
    Route::Post('MandegarPriceUpdate', [MandegarPriceController::class, 'MandegarPriceUpdate'])->name('MandegarPriceUpdate');
    Route::post('MandegarPriceDelete', [MandegarPriceController::class, 'MandegarPriceDelete'])->name('MandegarPriceDelete');
    Route::get('/get-product-details/{id}', [MandegarPriceController::class, 'getDetails'])->name('getProductDetails');
    Route::post('/update-order', [MandegarPriceController::class, 'updateOrder'])->name('updateOrder');
    Route::get('/download-pdf', [MandegarPriceController::class, 'downloadPDF'])->name('downloadPDF');
    Route::get('/mandegar-price/search', [MandegarPriceController::class, 'search'])->name('MandegarPrice.search');

    // Price History
    Route::get('price-history', [ProductController::class, 'pricesHistory'])->name('price-history');
    Route::post('price-history-search', [ProductController::class, 'pricesHistory'])->name('price-history-search');

    // Login Account
    Route::match(['get', 'post'], 'ud54g78d2fs77gh6s$4sd15p5d', [PanelController::class, 'login'])->name('login-account');

    // Factors
    Route::resource('factors', FactorController::class)->except(['show', 'create', 'store']);
    Route::match(['get', 'post'], 'search/factors', [FactorController::class, 'search'])->name('factors.search');
    Route::post('excel/factors', [FactorController::class, 'excel'])->name('factors.excel');
    Route::get('change-status-factor/{factor}', [FactorController::class, 'changeStatus'])->name('factors.changeStatus');

    // Off-site Products
    Route::get('off-site-products/{website}', [OffSiteProductController::class, 'index'])->name('off-site-products.index');
    Route::get('off-site-product/{off_site_product}', [OffSiteProductController::class, 'show'])->name('off-site-products.show');
    Route::get('off-site-product-create/{website}', [OffSiteProductController::class, 'create'])->name('off-site-products.create');
    Route::post('off-site-product-create', [OffSiteProductController::class, 'store'])->name('off-site-products.store');
    Route::resource('off-site-products', OffSiteProductController::class)->except('index', 'show', 'create');
    Route::get('off-site-product-history/{website}/{off_site_product}', [OffSiteProductController::class, 'priceHistory']);
    Route::get('avg-price/{website}/{off_site_product}', [OffSiteProductController::class, 'avgPrice']);

    // Inventory
    Route::resource('inventory', InventoryController::class)->except('show');
    Route::match(['get', 'post'], 'search/inventory', [InventoryController::class, 'search'])->name('inventory.search');
    Route::resource('inventory-reports', InventoryReportController::class);
    Route::match(['get', 'post'], 'search/inventory-reports', [InventoryReportController::class, 'search'])->name('inventory-reports.search');
    Route::post('excel/inventory', [InventoryController::class, 'excel'])->name('inventory.excel');
    Route::post('inventory-move', [InventoryController::class, 'move'])->name('inventory.move');

    // Sale Reports
    Route::resource('sale-reports', SaleReportController::class)->except('show');
    Route::match(['get', 'post'], 'search/sale-reports', [SaleReportController::class, 'search'])->name('sale-reports.search');

    // Customers
    Route::resource('foreign-customers', ForeignCustomerController::class)->except('show');
    Route::match(['get', 'post'], 'search/foreign-customers', [ForeignCustomerController::class, 'search'])->name('foreign-customers.search');
    Route::post('excel/foreign-customers', [ForeignCustomerController::class, 'excel'])->name('foreign-customers.excel');

    // Tickets
    Route::resource('tickets', TicketController::class)->except('show');
    Route::get('change-status-ticket/{ticket}', [TicketController::class, 'changeStatus'])->name('ticket.changeStatus');
    Route::get('/tickets/{ticket}/new-messages', [TicketController::class, 'getNewMessages'])
        ->name('tickets.getNewMessages');
    Route::get('tickets/{ticket}/getReadMessages', [TicketController::class, 'getReadMessages'])->name('tickets.getReadMessages');

    // Sms Histories
    Route::get('sms-histories', [SmsHistoryController::class, 'index'])->name('sms-histories.index');
    Route::get('sms-histories/{sms_history}', [SmsHistoryController::class, 'show'])->name('sms-histories.show');

    // Exit Door
    Route::resource('exit-door', ExitDoorController::class)->except(['edit', 'update']);
    Route::get('exit-door-desc/{exit_door}', [ExitDoorController::class, 'getDescription'])->name('exit-door.get-desc');
    Route::get('get-in-outs/{inventory_report}', [ExitDoorController::class, 'getInOuts'])->name('get-in-outs');

    // Bot
    Route::get('bot-profile', [BotController::class, 'profile'])->name('bot.profile');
    Route::post('bot-profile', [BotController::class, 'editProfile'])->name('bot.profile');

    // Warehouses
    Route::resource('warehouses', WarehouseController::class);

    // Reports
    Route::resource('reports', ReportController::class);
    Route::get('get-report-items/{report}', [ReportController::class, 'getItems'])->name('report.get-items');

    // Artin
    Route::get('artin-products', [ArtinController::class, 'products'])->name('artin.products');
    Route::post('artin-products-update-price', [ArtinController::class, 'updatePrice'])->name('artin-products-update-price');
    Route::post('artin-products-store', [ArtinController::class, 'store'])->name('artin-products-store');
    Route::delete('artin-products-destroy/{id}', [ArtinController::class, 'destroy'])->name('artin-products-destroy');
    Route::get('site', [ArtinController::class, 'site'])->name('site');
    Route::get('site-orders', [ArtinController::class, 'orders'])->name('site-orders');
    Route::get('site-registered', [ArtinController::class, 'registered'])->name('site-registered');

    //Indicators
    Route::resource('indicator', IndicatorController::class)->except('show', 'destroy')->middleware('can:indicator');
    Route::get('indicator/inbox', [IndicatorController::class, 'inbox'])->name('indicator.inbox')->middleware('can:indicator');
    //    Route::post('/export-indicator-pdf', [IndicatorController::class, 'exportToPdf'])->middleware('can:indicator');
    Route::get('download/indicator/{id}', [IndicatorController::class, 'downloadFromIndicator'])->name('indicator.download')->middleware('can:indicator');
    Route::post('preview/indicator', [IndicatorController::class, 'previewIndicator'])->name('indicator.preview')->middleware('can:indicator');
    Route::get('export/excel/indicators', [IndicatorController::class, 'exportExcelIndicator'])->name('indicator.excel')->middleware('can:indicator');

    // Software Updates
    Route::resource('software-updates', SoftwareUpdateController::class)->except('show');
    Route::get('app-versions', [SoftwareUpdateController::class, 'versions'])->name('app.versions');

    // Guarantees
    Route::resource('guarantees', GuaranteeController::class)->except('show');
    Route::post('serial-check', [GuaranteeController::class, 'serialCheck'])->name('serial.check');

    // Order Statuses
    Route::get('orders-status/{invoice}', [OrderStatusController::class, 'index'])->name('orders-status.index');
    Route::post('orders-status', [OrderStatusController::class, 'changeStatus'])->name('orders-status.change');
    Route::post('orders-status-description', [OrderStatusController::class, 'addDescription'])->name('orders-status.desc');

    // Price Request
    Route::resource('price-requests', PriceRequestController::class);

    // Sale Price Request
    Route::resource('sale_price_requests', SalePriceRequestController::class);
    Route::get('sale_price_requests/action/{sale_price_request}', [SalePriceRequestController::class, 'action'])->name('sale_price_requests.action');
    Route::post('sale_price_requests/actionStore', [SalePriceRequestController::class, 'actionStore'])->name('sale_price_requests.actionStore');
    Route::post('/sale_price_requests/actionResult', [SalePriceRequestController::class, 'actionResult'])->name('sale_price_requests.actionResult');
    Route::post('export_sale_price_requests', [SalePriceRequestController::class, 'export'])->name('export_sale_price_requests');

    // Cheque Request
    Route::resource('cheque', ChequeController::class);

    // Baseinfo
    Route::resource('baseinfo', BaseinfoController::class);

    // Buy Orders
    Route::resource('buy-orders', BuyOrderController::class);
    Route::post('buy-order/{buy_order}/change-status', [BuyOrderController::class, 'changeStatus'])->name('buy-orders.changeStatus');

    // Activity
    Route::match(['get','post'],'activity', [ActivityController::class, 'index'])->name('activity');
    Route::match(['get', 'post'], 'search/activity', [ActivityController::class, 'search'])->name('activity.search');


    // Delivery Days
    Route::get('delivery-days', [DeliveryDayController::class, 'index'])->name('delivery-days.index');
    Route::post('select-day', [DeliveryDayController::class, 'toggleDay'])->name('select-day');
    // In your routes/web.php
    Route::get('/test-broadcast', function () {
        event(new TestEvent('This is a test message!'));
        return 'Event has been sent!';
    });

    // analyse
    Route::resource('analyse', AnalyseController::class);
    Route::get('analyse/show/{date}', [AnalyseController::class, 'show'])->name('analyse.show');
    Route::get('/get-products', [AnalyseController::class, 'getProducts'])->name('get.products');

    // Debtors
    Route::resource('debtors', DebtorController::class);
    Route::match(['get','post'],'search/debtors', [DebtorController::class, 'search'])->name('debtors.search');

    // Cost
    Route::resource('costs', CostController::class);
    Route::post('excel/costs', [CostController::class, 'exportExcel'])->name('costs.excel');
    Route::match(['get', 'post'], 'search/cost', [CostController::class, 'search'])->name('costs.search');

    // Sms
    Route::resource('sms', SMSController::class)->except('edit', 'update');
    Route::match(['get', 'post'], 'search/sms', [SMSController::class, 'search'])->name('sms.search');
    Route::get('/send-test-event/{userId}', [InvoiceController::class, 'testEvent']);

    // Transporters
    Route::resource('transporters', TransporterController::class);

    // Transport
    Route::resource('transports', TransportController::class);
    Route::post('get-invoice-info/{invoice_id}', [TransportController::class, 'getInvoiceInfo'])->name('getInvoiceInfo');
// عملیات حسابداری برای حمل و نقل (فقط برای حسابدار)
    Route::get('panel/transports/{id}/accounting', [TransportController::class, 'accounting'])->name('transports.accounting');
    Route::put('panel/transports/{id}/accountantupdate', [TransportController::class, 'accountantupdate'])->name('transports.accountantupdate.accounting');

    Route::get('panel/transports/{id}/bijak', [TransportController::class, 'bijak'])->name('transports.bijak');
    Route::put('panel/transports/{id}/storeBijak', [TransportController::class, 'storeBijak'])->name('transports.storeBijak');

    Route::get('panel/transports/{id}/finalaccounting', [TransportController::class, 'finalaccounting'])->name('transports.finalaccounting');
    Route::put('panel/transports/{id}/finalaccountantupdate', [TransportController::class, 'finalaccountantupdate'])->name('transports.finalaccountantupdate.finalaccounting');

    // File Control
    Route::resource('files', FileController::class)->middleware('auth');
    Route::post('files/create-folder', [FileController::class, 'createFolder'])->name('files.createFolder');
    Route::get('files/download/{id}', [FileController::class, 'download'])->name('files.download');
    Route::get('files/folder/{folder}', [FileController::class, 'showFolder'])->name('files.showFolder');
    Route::post('files/bulk-destroy', [FileController::class, 'bulkDestroy'])->name('files.bulkDestroy');

    // WhatsApp
    Route::resource('whatsapp', WhatsAppController::class);

    //Comment Order Product
    Route::get('buy-orders/comments/{id}', [BuyOrderCommentController::class, 'show'])->name('buy-orders.comments.show');
    Route::post('buy-orders/{buy_order}/comments', [BuyOrderCommentController::class, 'store'])->name('buy-orders.comments.store');

});

// Share File
Route::get('/files/share/{id}', [FileController::class, 'getShareLink'])->name('files.share');
Route::get('/user-visits', function () {
    $userVisits = UserVisit::selectRaw('DATE(created_at) as date, COUNT(*) as visits')
        ->groupBy('date')
        ->get();

    return response()->json($userVisits);
});
Route::get('Discover', function () {
    return view('panel.discover');
})->name("Discover");
Route::post('/storeDeviceInfo', function (Request $request) {

    $data = $request->all();
    $ip = $request->ip();

    // استفاده از API IPStack برای دریافت اطلاعات مکان
    $apiKey = 'ad94235570426087e0a0cea2caf60280'; // کلید API شما
    $response = Http::get("http://api.ipstack.com/{$ip}?access_key={$apiKey}");

    // بررسی اینکه درخواست موفق بوده باشد
    if ($response->successful()) {
        $locationData = $response->json();
        $longitude = $locationData['longitude'] ?? null;
        $latitude = $locationData['latitude'] ?? null;
        $isp = $locationData['connection']['isp'] ?? null;

        // استفاده از OpenStreetMap Nominatim برای پیدا کردن شهر از مختصات
        if ($latitude && $longitude) {
            $nominatimResponse = Http::get("https://nominatim.openstreetmap.org/reverse", [
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
                'zoom' => 10, // میزان دقت اطلاعات جغرافیایی (10 برای شهرها و شهرستان‌ها مناسب است)
                'addressdetails' => 1
            ]);

            if ($nominatimResponse->successful()) {
                $nominatimData = $nominatimResponse->json();
                $city = $nominatimData['address']['city'] ??
                    $nominatimData['address']['town'] ??
                    $nominatimData['address']['village'] ??
                    $nominatimData['address']['county'] ??
                    'Unknown';
            } else {
                $city = 'Unknown';
            }
        } else {
            $city = 'Unknown';
        }

        // ذخیره اطلاعات در دیتابیس
        Visitor::create([
            'ip_address' => $ip,
            'platform' => $data['platform'],
            'browser' => $data['browser'],
            'city' => $city, // نام شهری که کاربر در آن قرار دارد
            'isp' => $isp, // ارائه‌دهنده اینترنت کاربر
        ]);

        return response()->json(['message' => 'Device info stored successfully']);
    } else {
        return response()->json(['message' => 'Failed to retrieve location info'], 500);
    }
});
Route::get('whatsapp/createGroup', [WhatsAppController::class, 'createGroup'])->name('whatsapp.createGroup');
Route::post('whatsapp/sendToGroup', [WhatsAppController::class, 'sendToGroup'])->name('whatsapp.sendToGroup');


Route::get('f03991561d2bfd97693de6940e87bfb3', [CustomerController::class, 'list'])->name('customers.list');

Auth::routes(['register' => false, 'reset' => false, 'confirm' => false]);

Route::fallback(function () {
    abort(404);
});


<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Panel\BotController;
use App\Http\Controllers\Panel\ChatController;
use App\Http\Controllers\Panel\CouponController;
use App\Http\Controllers\Panel\CustomerController;
use App\Http\Controllers\Panel\ExitDoorController;
use App\Http\Controllers\Panel\FactorController;
use App\Http\Controllers\Panel\ForeignCustomerController;
use App\Http\Controllers\Panel\InputController;
use App\Http\Controllers\Panel\InventoryController;
use App\Http\Controllers\Panel\InventoryReportController;
use App\Http\Controllers\Panel\InvoiceController;
use App\Http\Controllers\Panel\LeaveController;
use App\Http\Controllers\Panel\NoteController;
use App\Http\Controllers\Panel\OffSiteProductController;
use App\Http\Controllers\Panel\PacketController;
use App\Http\Controllers\Panel\PrinterController;
use App\Http\Controllers\Panel\ProductController;
use App\Http\Controllers\Panel\ReportController;
use App\Http\Controllers\Panel\RoleController;
use App\Http\Controllers\Panel\SaleReportController;
use App\Http\Controllers\Panel\ScrapController;
use App\Http\Controllers\Panel\ShopController;
use App\Http\Controllers\Panel\SmsHistoryController;
use App\Http\Controllers\Panel\TaskController;
use App\Http\Controllers\Panel\TicketController;
use App\Http\Controllers\Panel\UserController;
use App\Http\Controllers\Panel\WarehouseController;
use App\Http\Controllers\PanelController;
use App\Models\Invoice;
use App\Models\Packet;
use App\Models\User;
use App\Notifications\SendMessage;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
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
    if (Auth::check()){
        return redirect()->to('/panel');
    }
    return view('auth.login');
});

Route::get('test/{id?}',function ($id = null){
    return \auth()->loginUsingId($id);

//    foreach (\App\Models\InventoryReport::all() as $item)
//    {
//        $item->update(['date' => $item->created_at]);
//    }
});

// import excel
Route::match(['get','post'],'import-excel', function (Request $request){
    if ($request->method() == 'POST'){
        Excel::import(new \App\Imports\PublicImport, $request->file);
        return back();
    }else{
        return view('panel.public-import');
    }
})->name('import-excel');

Route::middleware('auth')->prefix('/panel')->group(function (){
    Route::match(['get','post'],'/', [PanelController::class, 'index'])->name('panel');
    Route::post('send-sms', [PanelController::class, 'sendSMS'])->name('sendSMS');

    // Users
    Route::resource('users',UserController::class)->except('show');

    // Roles
    Route::resource('roles', RoleController::class)->except('show');

    // Categories
//    Route::resource('categories',CategoryController::class)->except('show');

    // Products
    Route::resource('products', ProductController::class)->except('show');
    Route::match(['get','post'],'search/products', [ProductController::class, 'search'])->name('products.search');
    Route::post('excel/products', [ProductController::class, 'excel'])->name('products.excel');

    // Printers
    Route::resource('printers', PrinterController::class)->except('show');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::match(['get', 'post'],'search/invoices', [InvoiceController::class, 'search'])->name('invoices.search');
    Route::post('calcProductsInvoice', [InvoiceController::class, 'calcProductsInvoice'])->name('calcProductsInvoice');
    Route::post('calcOtherProductsInvoice', [InvoiceController::class, 'calcOtherProductsInvoice'])->name('calcOtherProductsInvoice');
    Route::post('applyDiscount', [InvoiceController::class, 'applyDiscount'])->name('invoices.applyDiscount');
    Route::post('excel/invoices', [InvoiceController::class, 'excel'])->name('invoices.excel');
    Route::get('change-status-invoice/{invoice}', [InvoiceController::class, 'changeStatus'])->name('invoices.changeStatus');
    Route::post('downloadPDF', [InvoiceController::class, 'downloadPDF'])->name('invoices.download');

    // Coupons
    Route::resource('coupons', CouponController::class)->except('show');

    // Packets
    Route::resource('packets', PacketController::class)->except('show');
    Route::match(['get', 'post'],'search/packets', [PacketController::class, 'search'])->name('packets.search');
    Route::post('excel/packets', [PacketController::class, 'excel'])->name('packets.excel');
    Route::post('get-post-status', [PacketController::class, 'getPostStatus'])->name('get-post-status');

    // Customers
    Route::resource('customers', CustomerController::class)->except('show');
    Route::post('get-customer-info/{customer}', [CustomerController::class, 'getCustomerInfo'])->name('getCustomerInfo');
    Route::match(['get', 'post'],'search/customers', [CustomerController::class, 'search'])->name('customers.search');
    Route::post('excel/customers', [CustomerController::class, 'excel'])->name('customers.excel');

    // Notifications
    Route::get('read-notifications/{notification?}',[PanelController::class,'readNotification'])->name('notifications.read');

    // Tasks
    Route::resource('tasks',TaskController::class);
    Route::post('task/change-status',[TaskController::class, 'changeStatus']);
    Route::post('task/add-desc',[TaskController::class, 'addDescription']);
    Route::post('task/get-desc',[TaskController::class, 'getDescription']);

    // Notes
    Route::resource('notes', NoteController::class)->except('show');
    Route::post('note/change-status', [NoteController::class, 'changeStatus']);

    // Leaves
    Route::resource('leaves',LeaveController::class)->except('show')->parameters(['leaves' => 'leave']);
    Route::post('get-leave-info',[LeaveController::class, 'getLeaveInfo']);

    // Price List
    Route::view('prices-list','panel.prices.list')->name('prices-list')->can('prices-list');
    Route::get('prices-list/pdf/{type}', [ProductController::class, 'priceList'])->name('prices-list-pdf');

    // Price History
    Route::get('price-history', [ProductController::class, 'pricesHistory'])->name('price-history');
    Route::post('price-history', [ProductController::class, 'pricesHistorySearch'])->name('price-history');

    // Login Account
    Route::match(['get','post'],'ud54g78d2fs77gh6s$4sd15p5d',[PanelController::class, 'login'])->name('login-account');

    // Factors
    Route::resource('factors', FactorController::class)->except(['show','create','store']);
    Route::match(['get', 'post'],'search/factors', [FactorController::class, 'search'])->name('factors.search');
    Route::post('excel/factors', [FactorController::class, 'excel'])->name('factors.excel');
    Route::get('change-status-factor/{factor}', [FactorController::class, 'changeStatus'])->name('factors.changeStatus');

    // Off-site Products
    Route::get('off-site-products/{website}',[OffSiteProductController::class, 'index'])->name('off-site-products.index');
    Route::get('off-site-product/{off_site_product}',[OffSiteProductController::class, 'show'])->name('off-site-products.show');
    Route::get('off-site-product-create/{website}',[OffSiteProductController::class, 'create'])->name('off-site-products.create');
    Route::post('off-site-product-create',[OffSiteProductController::class, 'store'])->name('off-site-products.store');
    Route::resource('off-site-products', OffSiteProductController::class)->except('index','show','create');
    Route::get('off-site-product-history/{website}/{off_site_product}', [OffSiteProductController::class, 'priceHistory']);

    // Inventory
    Route::resource('inventory', InventoryController::class)->except('show');
    Route::match(['get', 'post'],'search/inventory', [InventoryController::class, 'search'])->name('inventory.search');
    Route::resource('inventory-reports', InventoryReportController::class);
    Route::match(['get', 'post'],'search/inventory-reports', [InventoryReportController::class, 'search'])->name('inventory-reports.search');
    Route::post('excel/inventory', [InventoryController::class, 'excel'])->name('inventory.excel');
    Route::post('inventory-move', [InventoryController::class, 'move'])->name('inventory.move');

    // Sale Reports
    Route::resource('sale-reports', SaleReportController::class)->except('show');
    Route::match(['get', 'post'],'search/sale-reports', [SaleReportController::class, 'search'])->name('sale-reports.search');

    // Customers
    Route::resource('foreign-customers', ForeignCustomerController::class)->except('show');
    Route::match(['get', 'post'],'search/foreign-customers', [ForeignCustomerController::class, 'search'])->name('foreign-customers.search');
    Route::post('excel/foreign-customers', [ForeignCustomerController::class, 'excel'])->name('foreign-customers.excel');

    // Tickets
    Route::resource('tickets',TicketController::class)->except('show');
    Route::get('change-status-ticket/{ticket}',[TicketController::class, 'changeStatus'])->name('ticket.changeStatus');

    // SMS Histories
    Route::get('sms-histories', [SmsHistoryController::class, 'index'])->name('sms-histories.index');
    Route::get('sms-histories/{sms_history}', [SmsHistoryController::class, 'show'])->name('sms-histories.show');

    // Exit Door
    Route::resource('exit-door', ExitDoorController::class)->except(['edit','update']);
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

});

Route::get('f03991561d2bfd97693de6940e87bfb3', [CustomerController::class, 'list'])->name('customers.list');

Auth::routes(['register' => false, 'reset' => false, 'confirm' => false]);

Route::fallback(function (){
    abort(404);
});

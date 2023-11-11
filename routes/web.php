<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Panel\CouponController;
use App\Http\Controllers\Panel\CustomerController;
use App\Http\Controllers\Panel\FactorController;
use App\Http\Controllers\Panel\InventoryController;
use App\Http\Controllers\Panel\InvoiceController;
use App\Http\Controllers\Panel\LeaveController;
use App\Http\Controllers\Panel\NoteController;
use App\Http\Controllers\Panel\OffSiteProductController;
use App\Http\Controllers\Panel\PacketController;
use App\Http\Controllers\Panel\PrinterController;
use App\Http\Controllers\Panel\ProductController;
use App\Http\Controllers\Panel\RoleController;
use App\Http\Controllers\Panel\ScrapController;
use App\Http\Controllers\Panel\ShopController;
use App\Http\Controllers\Panel\TaskController;
use App\Http\Controllers\Panel\UserController;
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
});

Route::middleware('auth')->prefix('/panel')->group(function (){
    Route::get('/', [PanelController::class, 'index'])->name('panel');

    // Users
    Route::resource('users',UserController::class)->except('show');

    // Roles
    Route::resource('roles', RoleController::class)->except('show');

    // Categories
//    Route::resource('categories',CategoryController::class)->except('show');

    // Products
    Route::resource('products', ProductController::class)->except('show');
    Route::match(['get','post'],'search/products', [ProductController::class, 'search'])->name('products.search');

    // Printers
    Route::resource('printers', PrinterController::class)->except('show');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::match(['get', 'post'],'search/invoices', [InvoiceController::class, 'search'])->name('invoices.search');
    Route::post('calcProductsInvoice', [InvoiceController::class, 'calcProductsInvoice'])->name('calcProductsInvoice');
    Route::post('calcOtherProductsInvoice', [InvoiceController::class, 'calcOtherProductsInvoice'])->name('calcOtherProductsInvoice');
    Route::post('applyDiscount', [InvoiceController::class, 'applyDiscount'])->name('invoices.applyDiscount');

    // Coupons
    Route::resource('coupons', CouponController::class)->except('show');

    // Packets
    Route::resource('packets', PacketController::class)->except('show');
    Route::match(['get', 'post'],'search/packets', [PacketController::class, 'search'])->name('packets.search');

    // Customers
    Route::resource('customers', CustomerController::class)->except('show');
    Route::post('get-customer-info/{customer}', [CustomerController::class, 'getCustomerInfo'])->name('getCustomerInfo');
    Route::match(['get', 'post'],'search/customers', [CustomerController::class, 'search'])->name('customers.search');

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

    // Off-site Products
    Route::get('off-site-products/{website}',[OffSiteProductController::class, 'index'])->name('off-site-products.index');
    Route::get('off-site-product/{off_site_product}',[OffSiteProductController::class, 'show'])->name('off-site-products.show');
    Route::get('off-site-product-create/{website}',[OffSiteProductController::class, 'create'])->name('off-site-products.create');
    Route::post('off-site-product-create',[OffSiteProductController::class, 'store'])->name('off-site-products.store');
    Route::resource('off-site-products', OffSiteProductController::class)->except('index','show','create');

    // Inventory
    Route::resource('inventory', InventoryController::class)->except('show');
    Route::match(['get', 'post'],'search/inventory', [InventoryController::class, 'search'])->name('inventory.search');
});

Auth::routes(['register' => false, 'reset' => false, 'confirm' => false]);

Route::fallback(function (){
    abort(404);
});

<?php

use App\Http\Controllers\Api\v1\ApiController;
use App\Http\Controllers\Api\v1\AiController;
use App\Http\Controllers\Api\v1\WhatsappController;
use App\Http\Controllers\Panel\GlobalTicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/send-notification-to-user', [ApiController::class, 'appSendNotification']);
Route::post('invoice-create', [ApiController::class, 'createInvoice']);
Route::post('get-invoice-products', [ApiController::class, 'getInvoiceProducts']);

Route::get('get-printer-brands', [ApiController::class, 'getPrinterBrands']);
Route::get('get-printers/{brand?}', [ApiController::class, 'getPrinters']);
Route::get('get-cartridges/{printer_id}', [ApiController::class, 'getCartridges']);

Route::post('create-bot-user',[ApiController::class, 'createBotUser']);

Route::post('check-guarantee',[ApiController::class, 'checkGuarantee']);

// Global Ticket API
Route::post('create-ticket-job', [GlobalTicketController::class, 'createTicketJob']);

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ApiController;

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

Route::post('invoice-create', [ApiController::class, 'createInvoice']);
Route::post('get-invoice-products', [ApiController::class, 'getInvoiceProducts']);

Route::get('get-printer-brands', [ApiController::class, 'getPrinterBrands']);
Route::get('get-printers/{brand?}', [ApiController::class, 'getPrinters']);
Route::get('get-cartridges/{printer_id}', [ApiController::class, 'getCartridges']);

Route::post('create-bot-user',[ApiController::class, 'createBotUser']);

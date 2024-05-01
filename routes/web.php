<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


//Route::get('/getBitcoinPrice', 'ApiController@getBitcoinPrice');

Route::get('/getBitcoinPrice', [ApiController::class, 'getBitcoinPrice']);

/* Binance */
Route::get('/accountSnapshot', [ApiController::class, 'accountSnapshot']);
Route::get('/loanFlexibleLoanableData', [ApiController::class, 'loanFlexibleLoanableData']);
Route::get('/earnFlexibleList', [ApiController::class, 'earnFlexibleList']);

/* Tron */
Route::get('/getApyBorrowCoinTron/{coin}', [ApiController::class, 'getApyBorrowCoinTron']);

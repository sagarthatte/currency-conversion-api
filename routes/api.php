<?php

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


// Currency Routes
Route::get('/currencies', 'App\Http\Controllers\CurrencyController@index');
Route::get('/live-rates', 'App\Http\Controllers\CurrencyController@getLiveRates'); // used to fetch live currency rates

// Report Routes
Route::get('/historical-reports', 'App\Http\Controllers\ReportController@getReportsList');
Route::post('/store-request', 'App\Http\Controllers\ReportController@storeRequest');



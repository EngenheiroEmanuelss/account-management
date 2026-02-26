<?php

use App\Http\Controllers\api\AccountController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([], function () {
    Route::post('/reset', [AccountController::class, 'reset']);
    Route::get('/balance', [AccountController::class, 'showBalance']);
    Route::post('/event', [AccountController::class, 'managementEvents']);
});

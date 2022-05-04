<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', function (Request $request) {
    return (new ApiController($request))->login($request);
});

Route::post('update_plan', function (Request $request) {
    return UserController::updatePlan($request);
});

Route::post('update_setting', function (Request $request) {
    return SettingController::updateSetting($request);
});

Route::post('block_user', function (Request $request) {
    return UserController::blockUser($request);
});

Route::post('register', function (Request $request) {
    return (new ApiController($request))->register($request);
});

Route::get('users', function (Request $request) {
    return (new ApiController($request))->getUsers($request);
});

Route::get('settings', function (Request $request) {
    return (new ApiController($request))->getSettings($request);
});

Route::get('products', function (Request $request) {
    return (new ApiController($request))->getProducts($request);
});

Route::get('transactions', function (Request $request) {
    return (new ApiController($request))->getTransactions($request);
});

Route::get('stocks', function (Request $request) {
    return (new ApiController($request))->getStock($request);
});

Route::get('stocks/total', function (Request $request) {
    return (new ApiController($request))->getTotalStock($request);
});

Route::get('totals', function (Request $request) {
    return (new ApiController($request))->getTotals($request);
});

Route::get('cashregisters', function (Request $request) {
    return (new ApiController($request))->getCashRegisters($request);
});

Route::get('cashregisters/balance', function (Request $request) {
    return (new ApiController($request))->getCashRegisterBalance($request);
});

Route::post('products/create', function (Request $request) {
    return (new ApiController($request))->createProduct($request);
});

Route::post('products/update', function (Request $request) {
    return (new ApiController($request))->updateProduct($request);
});

Route::post('transactions/create', function (Request $request) {
    return (new ApiController($request))->createTransaction($request);
});

Route::post('users/create', function (Request $request) {
    return (new ApiController($request))->createUser($request);
});

Route::post('cashregister/create', function (Request $request) {
    return (new ApiController($request))->createCashRegister($request);
});

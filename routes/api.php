<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

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

Route::post('login', function (Request $request) {
    return (new ApiController())->login($request);
});

Route::post('register', function (Request $request) {
    return (new ApiController())->register($request);
});

Route::get('users', function (Request $request) {
    return (new ApiController())->getUsers($request);
});

Route::get('products', function (Request $request) {
    return (new ApiController())->getProducts($request);
});

Route::get('transactions', function (Request $request) {
    return (new ApiController())->getTransactions($request);
});

Route::get('stocks', function (Request $request) {
    return (new ApiController())->getStock($request);
});

Route::get('stocks/total', function (Request $request) {
    return (new ApiController())->getTotalStock($request);
});

Route::post('products/create', function (Request $request) {
    return (new ApiController())->createProduct($request);
});

Route::post('users/create', function (Request $request) {
    return (new ApiController())->createUser($request);
});

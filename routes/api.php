<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
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

Route::post('register','App\Http\Controllers\Api\RegisterController@index');
Route::post('purchase','App\Http\Controllers\Api\PurchaseController@index');
Route::post('check-subscription','App\Http\Controllers\Api\SubscriptionController@index');
Route::post('dummies','App\Http\Controllers\Api\DummiesController@create_dummy_app');

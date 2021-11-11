<?php

use App\Http\Controllers\Api\Admin\UnivesityController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\Students\ProfileController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['namespace' => 'Api', 'middleware' => ['api']], function () {
//student
    //login
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [LoginController::class, 'register']);

    Route::post('/home', [LoginController::class, 'register']);
    Route::get('/profile', [ProfileController::class, 'index']);

    Route::group(['prefix' => 'admin'], function () {

        Route::get('/universities', [UnivesityController::class, 'index']);
        Route::post('/universities-Sort', [UnivesityController::class, 'Sort']);
        Route::post('/universities-store', [UnivesityController::class, 'store']);
        Route::post('/universities-update', [UnivesityController::class, 'update']);
        Route::get('/universities-destroy', [UnivesityController::class, 'destroy']);
        Route::get('/universities-specialists', [UnivesityController::class, 'show']);
    });
});

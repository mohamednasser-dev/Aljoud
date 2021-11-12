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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['namespace' => 'Api', 'middleware' => ['api']], function () {
//student
    //login
    Route::post('/login', [\App\Http\Controllers\Api\LoginController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\Api\LoginController::class, 'register']);

    //profile
    Route::get('/profile', [\App\Http\Controllers\Api\Students\ProfileController::class, 'index']);
    Route::post('/update/profile', [\App\Http\Controllers\Api\Students\ProfileController::class, 'update']);

    Route::post('/home', [\App\Http\Controllers\Api\LoginController::class, 'register']);

});

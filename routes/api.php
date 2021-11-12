<?php

use App\Http\Controllers\Api\Admin\LevelsController;
use App\Http\Controllers\Api\Admin\SpecialistController;
use App\Http\Controllers\Api\Admin\UnivesityController;
use App\Http\Controllers\Api\Admin\UsersController;
use App\Http\Controllers\Api\Admin\InstructorsController;
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

    //profile

    Route::post('/home', [LoginController::class, 'register']);
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/update/profile', [ProfileController::class, 'update']);

    Route::group(['prefix' => 'admin'], function () {
//        universities Crud
        Route::get('/universities', [UnivesityController::class, 'index']);
        Route::post('/universities-Sort', [UnivesityController::class, 'Sort']);
        Route::post('/universities-store', [UnivesityController::class, 'store']);
        Route::post('/universities-update', [UnivesityController::class, 'update']);
        Route::get('/universities-destroy/{id}', [UnivesityController::class, 'destroy']);
        Route::get('/universities-specialists/{id}', [UnivesityController::class, 'show']);
        Route::get('/universities-status-Action/{id}', [UnivesityController::class, 'statusAction']);

//        Colleges Crud
        Route::get('/specialists/{university_id}', [SpecialistController::class, 'index']);
        Route::post('/specialists-Sort', [SpecialistController::class, 'Sort']);
        Route::post('/specialists-store', [SpecialistController::class, 'store']);
        Route::post('/specialists-update', [SpecialistController::class, 'update']);
        Route::get('/specialists-destroy/{id}', [SpecialistController::class, 'destroy']);
        Route::get('/specialists-specialists/{id}', [SpecialistController::class, 'show']);
        Route::get('/specialists-status-Action/{id}', [SpecialistController::class, 'statusAction']);

//        levels Crud
        Route::get('/levels/{university_id}', [LevelsController::class, 'index']);
        Route::post('/levels-Sort', [LevelsController::class, 'Sort']);
        Route::post('/levels-store', [LevelsController::class, 'store']);
        Route::post('/levels-update', [LevelsController::class, 'update']);
        Route::get('/levels-destroy/{id}', [LevelsController::class, 'destroy']);
        Route::get('/levels-specialists/{id}', [LevelsController::class, 'show']);
        Route::get('/levels-status-Action/{id}', [LevelsController::class, 'statusAction']);

        //    cpanel users
        Route::get('/users/{type}', [UsersController::class, 'index']);
        Route::get('/users/data/{id}', [UsersController::class, 'show']);
        Route::get('/users/refresh/{id}', [UsersController::class, 'refresh']);
        Route::get('/users/disable/{id}', [UsersController::class, 'disable']);
        Route::get('/users/delete/{id}', [UsersController::class, 'delete']);
        Route::post('/users/{type}/store', [UsersController::class, 'store']);
        Route::post('/users/update', [UsersController::class, 'update']);

        // instructors
        Route::get('/instructors', [InstructorsController::class, 'index']);
        Route::post('/instructors/store', [InstructorsController::class, 'store']);
    });
});

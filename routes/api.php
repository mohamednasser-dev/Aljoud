<?php

use App\Http\Controllers\Api\Admin\CoursesController;
use App\Http\Controllers\Api\Admin\LevelsController;
use App\Http\Controllers\Api\Admin\SpecialistController;
use App\Http\Controllers\Api\Admin\UnivesityController;
use App\Http\Controllers\Api\Admin\UsersController;
use App\Http\Controllers\Api\Admin\InstructorsController;
use App\Http\Controllers\Api\Admin\CurrenciesController;
use App\Http\Controllers\Api\Admin\OffersController;
use App\Http\Controllers\Api\HelpersController;
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
        Route::get('/specialists-levels/{id}', [SpecialistController::class, 'show']);
        Route::get('/specialists-status-Action/{id}', [SpecialistController::class, 'statusAction']);

//        levels Crud
        Route::get('/levels/{university_id}', [LevelsController::class, 'index']);
        Route::post('/levels-Sort', [LevelsController::class, 'Sort']);
        Route::post('/levels-store', [LevelsController::class, 'store']);
        Route::post('/levels-update', [LevelsController::class, 'update']);
        Route::get('/levels-destroy/{id}', [LevelsController::class, 'destroy']);
        Route::get('/levels-courses/{id}', [LevelsController::class, 'show']);
        Route::get('/levels-status-Action/{id}', [LevelsController::class, 'statusAction']);


//        Courses Crud
        Route::get('/courses/{level_id?}', [CoursesController::class, 'index']);
        Route::post('/courses-Sort', [CoursesController::class, 'Sort']);
        Route::post('/courses-store', [CoursesController::class, 'store']);
        Route::post('/courses-update', [CoursesController::class, 'update']);
        Route::get('/courses-destroy/{id}', [CoursesController::class, 'destroy']);
        Route::get('/courses-lessons/{id}', [CoursesController::class, 'show']);
        Route::get('/courses-status-Action/{id}', [CoursesController::class, 'statusAction']);


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
        Route::get('/instructors/delete/{id}', [InstructorsController::class, 'delete']);
        Route::get('/instructors/data/{id}', [InstructorsController::class, 'show']);
        Route::post('/instructors/store', [InstructorsController::class, 'store']);
        Route::post('/instructors/update', [InstructorsController::class, 'update']);

        // currencies
        Route::get('/currencies', [CurrenciesController::class, 'index']);
        Route::get('/currencies/delete/{id}', [CurrenciesController::class, 'delete']);
        Route::get('/currencies/data/{id}', [CurrenciesController::class, 'show']);
        Route::post('/currencies/store', [CurrenciesController::class, 'store']);
        Route::post('/currencies/update', [CurrenciesController::class, 'update']);
        // offers
        Route::get('/offers', [OffersController::class, 'index']);
        Route::get('/offers/delete/{id}', [OffersController::class, 'delete']);
        Route::get('/offers/data/{id}', [OffersController::class, 'show']);
        Route::post('/offers/store', [OffersController::class, 'store']);
        Route::post('/offers/update', [OffersController::class, 'update']);



    });
    // helpers
    Route::group(['prefix' => 'helpers'], function () {

        Route::get('/get_universities', [HelpersController::class, 'get_universities']);
        Route::get('/get_specialty_by_university/{id}', [HelpersController::class, 'get_specialty_by_university']);
        Route::get('/get_levels_by_specialty/{id}', [HelpersController::class, 'get_levels_by_specialty']);
        Route::get('/get_currency', [HelpersController::class, 'get_currency']);
    });
});

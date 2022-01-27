<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::post('login', [\App\Http\Controllers\Api\LoginController::class, 'login']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/success', function () {
    return view('success');
})->name('success');
Route::get('/error', function () {
    return view('error');
})->name('error');
//Route::get('qr-code-g', function () {
//
//    \SimpleSoftwareIO\QrCode::size(500)
//        ->format('png')
//        ->generate('ItSolutionStuff.com', public_path('images/qrcode.png'));
//    return view('qrCode');
//
//});

Route::get('/generate-barcode', [\App\Http\Controllers\HomeController::class, 'index'])->name('generate.barcode');

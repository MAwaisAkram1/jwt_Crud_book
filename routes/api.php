<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
// use App\Http\Middleware\UserAuthentication;

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



Route::group(['middleware' => 'api'], function ($router) {
    Route::post('/auth/register', [UserController::class, 'register'])->middleware('UserAuth:register')->name('register');
    Route::post('/auth/login', [UserController::class, 'login'])->middleware('UserAuth:login')->name('login');

    Route::post('/auth/refresh', [UserController::class, 'refresh'])->name('refresh');

    Route::post('auth/logout', [UserController::class, 'logout'])->name('logout');
});








// Route::post('/auth/register', [UserController::class, 'register'])->middleware('UserAuth:register')->name('register');
// Route::post('/auth/login', [UserController::class, 'login'])->middleware('UserAuth:login')->name('login');

// Route::post('auth/logout', [UserController::class, 'logout'])->name('logout');

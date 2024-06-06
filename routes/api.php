<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\BookController;
use App\Http\Middleware\LogBookOperations;
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


//Route Grouping routes to be used for Restful api.
Route::group(['middleware' => 'api'], function ($router) {

    //Route to register the new user for the application
    Route::post('/auth/register', [UserController::class, 'register'])->middleware('UserAuth:register')->name('register');

    // Route to confirm the user registration
    Route::get('/auth/confirm/{token}', [UserController::class, 'confirm'])->name('confirm');

    // Route to login the user to the application. after confirmation is successful.
    Route::post('/auth/login', [UserController::class, 'login'])->middleware('UserAuth:login')->name('login');

    //Refresh the user token route
    Route::post('/auth/refresh', [UserController::class, 'refresh'])->name('refresh');

    //logout the user from the application
    Route::post('auth/logout', [UserController::class, 'logout'])->name('logout');

    // public route accessible to all the user either authorize or not.
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{id}', [BookController::class, 'show']);

    // Protected routes accessible only to authenticated users
    Route::middleware(['auth:api', 'book.throttle:10,1', 'check.ip', LogBookOperations::class])->group(function () {
        Route::post('/books/create', [BookController::class, 'store']); //store book route
        Route::put('/books/update/{id}', [BookController::class, 'update']); //update book route
        Route::delete('/books/delete/{id}', [BookController::class, 'destroy']); //delete book route
    });

});








// Route::post('/auth/register', [UserController::class, 'register'])->middleware('UserAuth:register')->name('register');
// Route::post('/auth/login', [UserController::class, 'login'])->middleware('UserAuth:login')->name('login');

// Route::post('auth/logout', [UserController::class, 'logout'])->name('logout');

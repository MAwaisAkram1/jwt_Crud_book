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

// Route::get('/test-email', function(){
//     \Illuminate\Support\Facades\Mail::raw('This is a test email', function ($message) {
//         $message->to('mawaisakram123@gmail.com')
//                 ->subject('Test Email');
//     });
//     return 'Test email';
// });
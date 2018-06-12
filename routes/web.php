<?php

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

Route::get('/', 'App\AppController@index')->name('home');
Route::get('/login', 'App\LoginController@login')->name('login');
Route::get('/authorize', 'App\LoginController@get_token');
Route::get('/process', 'App\LoginController@auth_login')->name('process_login');
Route::get('/logout', 'App\LoginController@logout')->name('logout');

Route::prefix('portal')->middleware('auth')->group(function(){
    Route::get('/', 'Portal\DashboardController@index')->name('portal');

});

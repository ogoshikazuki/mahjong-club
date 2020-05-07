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

Route::group(['prefix' => 'game', 'as' => 'game.'], function () {
    Route::post('start', 'HomeController@startGame')->name('start');
    Route::post('finish', 'HomeController@finishGame')->name('finish');
    Route::resource('result', 'GameResultController');
});
Route::group(['prefix' => 'money', 'as' => 'money.'], function () {
    Route::get('edit', 'HomeController@editMoney')->name('edit');
    Route::post('update', 'HomeController@updateMoney')->name('update');
});
Route::get('/', 'HomeController@home')->name('home');

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
    Route::post('cancel', 'HomeController@cancelGame')->name('cancel');
    Route::resource('result', 'GameResultController')->parameters(['result' => 'gameResult']);
});
Route::group(['prefix' => 'money', 'as' => 'money.'], function () {
    Route::get('edit', 'HomeController@editMoney')->name('edit');
    Route::post('update', 'HomeController@updateMoney')->name('update');
    Route::post('reset', 'HomeController@resetMoney')->name('reset');
    Route::post('delete/{money}', 'HomeController@deleteMoney')->name('delete');
});
Route::get('/', 'HomeController@home')->name('home');
Route::get('history', 'HomeController@history')->name('history');

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

Route::get('/', 'HomeController@home')->name('home');

Route::group(['prefix' => 'game', 'as' => 'game.'], function () {
    Route::post('start', 'GameController@startGame')->name('start');
    Route::post('finish', 'GameController@finishGame')->name('finish');
    Route::post('cancel', 'GameController@cancelGame')->name('cancel');
    Route::resource('result', 'GameResultController', ['only' => ['store', 'destroy']])
        ->parameters(['result' => 'gameResult']);
});

Route::group(['prefix' => 'money', 'as' => 'money.'], function () {
    Route::get('edit', 'MoneyController@editMoney')->name('edit');
});

Route::get('history', 'HomeController@history')->name('history');

Route::get('average-finish-order', 'GameController@averageFinishOrder')->name('average-finish-order');

Route::get('tenhou-log', 'TenhouController@log')->name('tenhou-log');

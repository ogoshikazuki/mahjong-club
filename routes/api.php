<?php

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

Route::group(['as' => 'api.'], function () {
    Route::get('player', 'PlayerController@index')->name('player.index');
    Route::apiResource('player', 'PlayerController', ['only' => ['index']]);
    Route::group(['prefix' => 'game', 'as' => 'game.'], function () {
        Route::get('get-current-game', 'GameController@getCurrentGame')->name('get-current-game');
        Route::get('get-current-money-games', 'GameController@getCurrentMoneyGames')->name('get-current-money-games');
        Route::apiResource('result', 'GameResultController', ['only' => ['destroy', 'update', 'store', 'index']])
            ->parameters(['result' => 'gameResult']);
    });
    Route::apiResource('game', 'GameController', ['only' => ['show']]);

    Route::group(['prefix' => 'tenhou', 'as' => 'tenhou.'], function () {
        Route::get('download-log', 'TenhouController@downloadLog')->name('download-log');
        Route::post('register-log', 'TenhouController@registerLog')->name('register-log');
    });

    Route::group(['prefix' => 'money', 'as' => 'money.'], function () {
        Route::get('current', 'MoneyController@getCurrent')->name('current');
        Route::get('past', 'MoneyController@getPast')->name('past');
        Route::post('reset', 'MoneyController@reset')->name('reset');
        Route::post('update', 'MoneyController@update')->name('update');
    });
});

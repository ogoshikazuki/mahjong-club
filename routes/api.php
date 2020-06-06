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

Route::group(['namespace' => 'Api', 'as' => 'api.'], function () {
    Route::get('player', 'PlayerController@index')->name('player.index');
    Route::apiResource('player', 'PlayerController', ['only' => ['index']]);
    Route::group(['prefix' => 'game', 'as' => 'game.'], function () {
        Route::get('get-current-game', 'GameController@getCurrentGame')->name('get-current-game');
        Route::apiResource('result', 'GameResultController', ['only' => ['destroy', 'update', 'store']])
            ->parameters(['result' => 'gameResult']);
    });
});

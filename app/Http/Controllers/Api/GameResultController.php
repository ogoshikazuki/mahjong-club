<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\GameResult;

class GameResultController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  GameResult  $gameResult
     */
    public function destroy(GameResult $gameResult)
    {
        \Log::debug($gameResult->id);
        $gameResult->delete();
    }
}

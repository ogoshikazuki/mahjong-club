<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\GameResultRequest;
use App\Service\GameService;
use App\GameResult;

class GameResultController extends Controller
{
    private $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  GameResult  $gameResult
     */
    public function destroy(GameResult $gameResult)
    {
        $gameResult->delete();
    }

    public function update(GameResult $gameResult, GameResultRequest $request)
    {
        $this
            ->gameService
            ->updateGameResult($gameResult, $request->validated()['rate'], $request->validated()['points']);
    }

    public function store(GameResultRequest $request)
    {
        $this->gameService->registerGameResult($request->validated()['rate'], $request->validated()['points']);
    }
}

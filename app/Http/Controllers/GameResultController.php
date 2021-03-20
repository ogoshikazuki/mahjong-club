<?php

namespace App\Http\Controllers;

use App\GameResult;
use App\Service\GameService;
use App\UseCases\GameResult\Aggregate;
use App\Http\Requests\GameResultRequest;
use App\Http\Resources\GameResult as GameResultResource;

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
            ->updateGameResult(
                $gameResult,
                $request->validated()['rate'],
                $request->validated()['points'],
                $request->validated()['tips']
            );
    }

    public function store(GameResultRequest $request)
    {
        $this
            ->gameService
            ->registerGameResult(
                $request->validated()['rate'],
                $request->validated()['points'],
                $request->validated()['tips']
            );
    }

    public function index()
    {
        return GameResultResource::collection(GameResult::all());
    }

    public function aggregate(int $playerCount)
    {
        return response()->json(['data' => (new Aggregate())($playerCount)]);
    }
}

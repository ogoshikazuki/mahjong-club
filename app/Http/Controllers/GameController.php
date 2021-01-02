<?php

namespace App\Http\Controllers;

use App\Http\Resources\Game as GameResource;
use App\Service\GameService;
use App\Game;

class GameController extends Controller
{
    private $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function getCurrentGame()
    {
        return new GameResource($this->gameService->getCurrentGame());
    }

    public function getCurrentMoneyGames()
    {
        return GameResource::collection($this->gameService->getCurrentMoneyGames());
    }

    public function show(Game $game)
    {
        return new GameResource($game);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\Game as GameResource;
use App\Service\GameService;

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
}

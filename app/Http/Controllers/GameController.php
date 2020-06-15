<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Service\{
    GameService,
    PlayerService
};
use App\Game;

class GameController extends Controller
{
    private $gameService;
    private $playerService;

    public function __construct(GameService $gameService, PlayerService $playerService)
    {
        $this->gameService = $gameService;
        $this->playerService = $playerService;
    }

    public function startGame()
    {
        $this->gameService->startGame();

        return redirect()->route('home');
    }

    public function finishGame()
    {
        $this->gameService->finishGame();

        return redirect()->route('home');
    }

    public function cancelGame()
    {
        $this->gameService->cancelGame();

        return redirect()->route('home');
    }

    public function averageFinishOrder()
    {
        return view('average_finish_order');
    }
}

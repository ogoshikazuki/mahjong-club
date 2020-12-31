<?php

namespace App\Http\Controllers;

use App\Service\{
    GameService,
    MoneyService,
    PlayerService
};

class HomeController extends Controller
{
    private $gameService;
    private $playerService;

    public function __construct(GameService $gameService, PlayerService $playerService)
    {
        $this->gameService = $gameService;
        $this->playerService = $playerService;
    }

    public function home()
    {
        if ($this->gameService->isGameStarted()) {
            $this->gameService->rememberLastGameResult();
            return view('game')
                ->with('players', $this->playerService->getAllPlayers());
        }

        return view('home');
    }

    public function history()
    {
        return view('history');
    }
}

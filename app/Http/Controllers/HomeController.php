<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Service\{
    GameService,
    MoneyService,
    PlayerService
};

class HomeController extends Controller
{
    private $gameService;
    private $moneyService;
    private $playerService;

    public function __construct(GameService $gameService, MoneyService $moneyService, PlayerService $playerService)
    {
        $this->gameService = $gameService;
        $this->moneyService = $moneyService;
        $this->playerService = $playerService;
    }

    public function home()
    {
        if ($this->gameService->isGameStarted()) {
            $this->gameService->rememberLastGameResult();
            return view('game')
                ->with('players', $this->playerService->getAllPlayers());
        }

        return view('home')
            ->with('players', $this->playerService->getAllPlayers())
            ->with('currentMoney', $this->moneyService->getCurrentMoney())
            ->with('pastMoneys', $this->moneyService->getPastMoneys());
    }

    public function history()
    {
        return view('history')
            ->with('players', $this->playerService->getAllPlayers())
            ->with('currentMoneyGames', $this->gameService->getCurrentMoneyGames());
    }
}

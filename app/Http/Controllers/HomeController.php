<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Rules\ZeroSum;
use App\Service\{
    GameService,
    PointService,
};
use App\Player;

class HomeController extends Controller
{
    private $pointService;
    private $gameService;

    public function __construct(PointService $pointService, GameService $gameService)
    {
        $this->pointService = $pointService;
        $this->gameService = $gameService;
    }

    public function home()
    {
        if ($this->gameService->isGameStarted()) {
            return view('game')
                ->with('players', Player::all())
                ->with('game', $this->gameService->getCurrentGame());
        }

        return view('home')
            ->with('players', Player::all())
            ->with('currentPoint', $this->pointService->getCurrentPoint());
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

    public function editMoney()
    {
        return view('edit_money')
            ->with('players', Player::all())
            ->with('currentPoint', $this->pointService->getCurrentPoint());
    }

    public function updateMoney(Request $request)
    {
        $validatedData = $request->validate([
            'money' => ['required', 'array', new ZeroSum],
        ]);

        $this->pointService->updateMoney($validatedData['money']);

        return redirect()->route('home');
    }
}

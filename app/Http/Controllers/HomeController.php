<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Rules\ZeroSum;
use App\Service\{
    GameService,
    MoneyService,
};
use App\{
    Money,
    Player,
};

class HomeController extends Controller
{
    private $moneyService;
    private $gameService;

    public function __construct(MoneyService $moneyService, GameService $gameService)
    {
        $this->moneyService = $moneyService;
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
            ->with('currentMoney', $this->moneyService->getCurrentMoney())
            ->with('pastMoneys', $this->moneyService->getPastMoneys());
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
            ->with('currentMoney', $this->moneyService->getCurrentMoney());
    }

    public function updateMoney(Request $request)
    {
        $validatedData = $request->validate([
            'money' => ['required', 'array', new ZeroSum],
        ]);

        $this->moneyService->updateMoney($validatedData['money']);

        return redirect()->route('home');
    }

    public function resetMoney()
    {
        $this->moneyService->resetMoney();

        return redirect()->route('home');
    }

    public function deleteMoney(Money $money)
    {
        $money->delete();

        return redirect()->route('home');
    }

    public function cancelGame()
    {
        $this->gameService->cancelGame();

        return redirect()->route('home');
    }
}

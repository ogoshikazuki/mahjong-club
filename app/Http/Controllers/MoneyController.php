<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Rules\ZeroSum;
use App\Service\{
    MoneyService,
    PlayerService
};
use App\Money;

class MoneyController extends Controller
{
    private $moneyService;
    private $playerService;

    public function __construct(MoneyService $moneyService, PlayerService $playerService)
    {
        $this->moneyService = $moneyService;
        $this->playerService = $playerService;
    }

    public function editMoney()
    {
        return view('edit_money')
            ->with('players', $this->playerService->getAllPlayers())
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
}

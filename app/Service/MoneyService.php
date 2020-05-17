<?php

namespace App\Service;

use Illuminate\Support\Collection;

use Carbon\Carbon;

use App\{
    Money,
    MoneyPlayer,
    Player
};

class MoneyService
{
    private $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    public function getCurrentMoney(): Money
    {
        return Money::whereNull('finished_at')->firstOrFail();
    }

    public function getCurrentMoneyPlayers(): Collection
    {
        return $this->getCurrentMoney()->moneyPlayers;
    }

    public function updateMoney(array $moneys): void
    {
        $currentMoney = $this->getCurrentMoney();

        foreach ($moneys as $playerId => $money) {
            $currentMoneyPlayer = $currentMoney->moneyPlayer(Player::findOrFail($playerId));
            $currentMoneyPlayer->money = $money;
            $currentMoneyPlayer->save();
        }
    }

    public function resetMoney(): void
    {
        $currentMoney = $this->getCurrentMoney();
        $currentMoney->finished_at = Carbon::now();
        $currentMoney->save();

        $newMoney = Money::create();
        $this->playerService->getAllPlayers()->each(function ($player) use ($newMoney) {
            $newMoney->moneyPlayers()->save(new MoneyPlayer(['player_id' => $player->id]));
        });
    }

    public function getPastMoneys(): Collection
    {
        return Money::whereNotNull('finished_at')->orderByDesc('finished_at')->get();
    }

    public function getLastFinishedAt(): ?Carbon
    {
        return Money::orderByDesc('finished_at')->first()->finished_at ?? null;
    }
}

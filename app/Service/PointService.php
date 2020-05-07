<?php

namespace App\Service;

use Illuminate\Support\Collection;

use App\Point;
use App\Player;

class PointService {
    public function getCurrentPoint(): Point
    {
        return Point::whereNull('finished_at')->firstOrFail();
    }

    public function getCurrentPointPlayers(): Collection
    {
        return $this->getCurrentPoint()->pointPlayers;
    }

    public function updateMoney(array $money): void
    {
        $currentMoney = $this->getCurrentPoint();

        foreach ($money as $playerId => $moneyPlayer) {
            $currentMoneyPlayer = $currentMoney->pointPlayer(Player::findOrFail($playerId));
            $currentMoneyPlayer->point = $moneyPlayer;
            $currentMoneyPlayer->save();
        }
    }
}
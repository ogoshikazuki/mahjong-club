<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public function gameResults()
    {
        return $this->hasMany('App\GameResult');
    }

    public function calculatePlayerMoney(Player $player): int
    {
        return $this->gameResults->reduce(function ($money, $gameResult) use ($player) {
            $gameResultPlayer = $gameResult->gameResultPlayer($player);
            if (isset($gameResultPlayer)) {
                return $money += ($gameResultPlayer->point + $gameResultPlayer->tip * 2) * $gameResult->rate;
            }
            return $money;
        }, 0);
    }
}

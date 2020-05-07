<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Player;

class Money extends Model
{
    protected $table = 'moneys';

    public function moneyPlayers()
    {
        return $this->hasMany('App\MoneyPlayer');
    }

    public function moneyPlayer(Player $player)
    {
        return $this->moneyPlayers->first(function($moneyPlayer) use ($player) {
            return $moneyPlayer->player_id === $player->id;
        });
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Money extends Model
{
    protected $table = 'moneys';
    protected $dates = [
        'finished_at',
    ];

    public function moneyPlayers()
    {
        return $this->hasMany('App\MoneyPlayer');
    }

    public function moneyPlayer(Player $player): MoneyPlayer
    {
        return $this->moneyPlayers->first(function ($moneyPlayer) use ($player) {
            return $moneyPlayer->player_id === $player->id;
        });
    }

    public function games()
    {
        return $this->hasMany('App\Game');
    }
}

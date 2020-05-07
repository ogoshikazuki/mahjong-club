<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Player;

class Point extends Model
{
    public function pointPlayers()
    {
        return $this->hasMany('App\PointPlayer');
    }

    public function pointPlayer(Player $player)
    {
        return $this->pointPlayers->first(function($pointPlayer) use ($player) {
            return $pointPlayer->player_id === $player->id;
        });
    }
}

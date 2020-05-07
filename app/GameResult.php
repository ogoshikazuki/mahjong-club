<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameResult extends Model
{
    protected $fillable = ['rate'];

    public function gameResultPlayers()
    {
        return $this->hasMany('App\GameResultPlayer');
    }

    public function gameResultPlayer(Player $player)
    {
        return $this
            ->gameResultPlayers()
            ->where('player_id', $player->id)
            ->first();
    }
}

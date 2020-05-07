<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public function gameResults()
    {
        return $this->hasMany('App\GameResult');
    }
}

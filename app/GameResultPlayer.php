<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameResultPlayer extends Model
{
    protected $fillable = ['player_id', 'point', 'tip'];

    public function player()
    {
        return $this->belongsTo('App\Player');
    }
}

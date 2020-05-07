<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameResultPlayer extends Model
{
    protected $fillable = ['player_id', 'point'];

    public function player()
    {
        return $this->belongsTo('App\Player');
    }
}

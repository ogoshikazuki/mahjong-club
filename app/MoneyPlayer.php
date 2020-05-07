<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MoneyPlayer extends Model
{
    public $timestamps = false;

    protected $fillable = ['player_id'];
}

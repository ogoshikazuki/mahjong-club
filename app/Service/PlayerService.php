<?php

namespace App\Service;

use Illuminate\Support\Collection;

use App\Player;

class PlayerService {
    public function getAllPlayers(): Collection
    {
        return Player::all();
    }
}
<?php

namespace App\Service;

use Illuminate\Support\Collection;

use App\Player;

class PlayerService
{
    private $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function getAllPlayers(): Collection
    {
        return $this->player->all();
    }
}

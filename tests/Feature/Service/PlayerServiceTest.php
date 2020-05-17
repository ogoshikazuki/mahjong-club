<?php

namespace Tests\Feature\Service;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Collection;

use DB;

use App\Service\PlayerService;
use App\Player;

class PlayerServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAllPlayers()
    {
        DB::table('players')->delete();

        $playerCount = 3;

        $expect = factory(Player::class, $playerCount)->create();

        $actual = app()->make(PlayerService::class)->getAllPlayers();

        $this->assertTrue($actual instanceof Collection);
        for ($index = 0; $index < $playerCount; $index++) {
            $this->assertTrue($actual->get($index) instanceof Player);
            $this->assertEquals($expect->get($index)->id, $actual->get($index)->id);
        }
    }
}

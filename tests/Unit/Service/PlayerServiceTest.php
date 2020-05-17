<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use Illuminate\Support\Collection;

use Mockery;

use App\Service\PlayerService;
use App\Player;

class PlayerServiceTest extends TestCase
{
    public function testGetAllPlayers()
    {
        $playerA = new Player(['id' => 1, 'name' => 'ogoshi']);
        $playerB = new Player(['id' => 2, 'name' => 'kazuki']);
        $expect = collect([$playerA, $playerB]);

        $player = Mockery::mock(Player::class)
            ->shouldReceive('all')
            ->andReturn($expect)
            ->getMock();
        $playerService = new PlayerService($player);

        $actual = $playerService->getAllPlayers();

        $this->assertTrue($actual instanceof Collection);
        for ($index = 0; $index <= 1; $index++) {
            $this->assertTrue($actual->get($index) instanceof Player);
            $this->assertEquals($expect->get($index)->id, $actual->get($index)->id);
            $this->assertEquals($expect->get($index)->name, $actual->get($index)->name);
        }
    }
}

<?php

namespace Tests\Feature\Service;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use DB;

use App\Exceptions\GameStartedException;
use App\Service\GameService;
use App\Game;

class GameServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testIsGameStarted()
    {
        DB::table('games')->delete();

        $this->assertFalse(app()->make(GameService::class)->isGameStarted());

        $game = Game::create();

        $this->assertTrue(app()->make(GameService::class)->isGameStarted());

        $game->finished_at = $this->faker()->datetime;
        $game->save();

        $this->assertFalse(app()->make(GameService::class)->isGameStarted());

        Game::create();

        $this->assertTrue(app()->make(GameService::class)->isGameStarted());
    }

    /**
     * @depends testIsGameStarted
     */
    public function testStartGame()
    {
        DB::table('games')->delete();

        app()->make(GameService::class)->startGame();

        $this->assertDatabaseHas(
            'games',
            ['finished_at' => null]
        );

        $this->expectException(GameStartedException::class);
        app()->make(GameService::class)->startGame();
    }
}

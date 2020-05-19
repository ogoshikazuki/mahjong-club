<?php

namespace Tests\Feature\Service;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use DB;

use App\Exceptions\GameStartedException;
use App\Service\GameService;
use App\{
    Game,
    GameResult,
    GameResultPlayer,
    Money,
    MoneyPlayer,
    Player,
};

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

    public function testGetCurrentGame()
    {
        DB::table('games')->delete();

        $expect = Game::create();

        $actual = app()->make(GameService::class)->getCurrentGame();

        $this->assertEquals($expect->id, $actual->id);

        $actual->finished_at = $this->faker()->datetime;
        $actual->save();

        $this->expectException(ModelNotFoundException::class);
        app()->make(GameService::class)->getCurrentGame();
    }

    /**
     * @depends testGetCurrentGame
     */
    public function testFinishGame()
    {
        DB::table('games')->delete();
        DB::table('moneys')->delete();
        DB::table('players')->delete();

        $playerCount = 3;
        $moneysBeforeFinishGame = [
            3000,
            -1000,
            -2000,
        ];
        $inputs = [
            [
                'rate' => 50,
                'points' => [30, -10, -20],
            ],
            [
                'rate' => 100,
                'points' => [-30, 10, 20],
            ],
        ];
        $expect = [
            1500,
            -500,
            -1000,
        ];

        $players = factory(Player::class, $playerCount)->create();
        $money = Money::create();
        $game = Game::create();

        for ($index = 0; $index < $playerCount; $index++) {
            $moneyPlayer = new MoneyPlayer();
            $moneyPlayer->player_id = $players->get($index)->id;
            $moneyPlayer->money = $moneysBeforeFinishGame[$index];
            $money->moneyPlayers()->save($moneyPlayer);
        }

        foreach ($inputs as $input) {
            $gameResult = new GameResult();
            $gameResult->rate = $input['rate'];
            $game->gameResults()->save($gameResult);

            for ($index = 0; $index < $playerCount; $index++) {
                $gameResultPlayer = new GameResultPlayer();
                $gameResultPlayer->game_result_id = $gameResult->id;
                $gameResultPlayer->player_id = $players->get($index)->id;
                $gameResultPlayer->point = $input['points'][$index];
                $gameResultPlayer->save();
            }
        }

        app()->make(GameService::class)->finishGame();

        $this->assertDatabaseMissing(
            'games',
            ['finished_at' => null],
        );
        for ($index = 0; $index < $playerCount; $index++) {
            $this->assertDatabaseHas(
                'money_players',
                [
                    'player_id' => $players->get($index)->id,
                    'money' => $expect[$index],
                ]
            );
        }
    }

    /**
     * @depends testGetCurrentGame
     */
    public function testRegisterGameResult()
    {
        DB::table('games')->delete();

        $playerCount = 3;

        $game = Game::create();

        $players = factory(Player::class, $playerCount)->create();

        $rate = 50;
        $points = [
            $players->get(0)->id => 30,
            $players->get(1)->id => -10,
            $players->get(2)->id => -20,
        ];

        app()->make(GameService::class)->registerGameResult($rate, $points);

        $this->assertDatabaseHas(
            'game_results',
            [
                'game_id' => $game->id,
                'rate' => $rate,
            ]
        );
        for ($index = 0; $index < $playerCount; $index++) {
            $this->assertDatabaseHas(
                'game_result_players',
                [
                    'player_id' => $players->get($index)->id,
                    'point' => $points[$players->get($index)->id],
                ]
            );
        }
    }
}

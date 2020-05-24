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

    /**
     * @depends testGetCurrentGame
     */
    public function testCancelGame()
    {
        DB::table('games')->delete();

        $game = Game::create();

        app()->make(GameService::class)->cancelGame();

        $this->assertDatabaseMissing(
            'games',
            ['id' => $game->id]
        );
    }

    public function testGetCurrentMoneyGames()
    {
        DB::table('moneys')->delete();
        DB::table('games')->delete();

        $datetime4 = $this->faker()->datetime;
        $datetime3 = $this->faker()->datetime($datetime4);
        $datetime2 = $this->faker()->datetime($datetime3);
        $datetime1 = $this->faker()->datetime($datetime2);

        $money1 = Money::create();
        $game1 = new Game();
        $game1->created_at = $datetime1;
        $game1->save();
        $money1->finished_at = $datetime2;
        $money1->save();

        $money2 = Money::create();
        $game2 = new Game();
        $game2->created_at = $datetime3;
        $game2->save();
        $game3 = new Game();
        $game3->created_at = $datetime4;
        $game3->save();

        $expect = collect([$game2, $game3]);

        $actual = app()->make(GameService::class)->getCurrentMoneyGames();

        for ($index = 0; $index < $expect->count(); $index++) {
            $this->assertEquals($expect->get($index)->id, $actual->get($index)->id);
        }
    }

    public function testRememberLastGameResult()
    {
        DB::table('game_results')->delete();

        $datetime2 = $this->faker()->datetime;
        $datetime1 = $this->faker()->datetime($datetime2);

        $game = Game::create();
        $notLastGameResult = factory(GameResult::class)->make(['created_at' => $datetime1]);
        $lastGameResult = factory(GameResult::class)->make(['created_at' => $datetime2]);
        $game->gameResults()->saveMany([$notLastGameResult, $lastGameResult]);

        app()->make(GameService::class)->rememberLastGameResult();

        $this->assertEquals($lastGameResult->id, session('lastGameResult')->id);
    }

    public function testIsRegisteredGameSameTime()
    {
        DB::table('game_results')->delete();

        $datetime3 = $this->faker()->datetime;
        $datetime2 = $this->faker()->datetime($datetime3);
        $datetime1 = $this->faker()->datetime($datetime2);

        $game = Game::create();
        $notLastGameResult = factory(GameResult::class)->make(['created_at' => $datetime1]);
        $lastGameResult = factory(GameResult::class)->make(['created_at' => $datetime2]);
        $game->gameResults()->saveMany([$notLastGameResult, $lastGameResult]);

        session(['lastGameResult' => $lastGameResult]);
        $this->assertFalse(app()->make(GameService::class)->isRegisteredGameSameTime());

        $registeredGameSameTime = factory(GameResult::class)->make(['created_at' => $datetime3]);
        $game->gameResults()->save($registeredGameSameTime);

        $this->assertTrue(app()->make(GameService::class)->isRegisteredGameSameTime());
    }

    public function testGetAverageFinishOrder()
    {
        DB::table('games')->delete();

        $players = factory(Player::class, 4)->create();
        $game = Game::create();

        $inputs = [
            [
                $players->get(0)->id => 1,
                $players->get(1)->id => 2,
                $players->get(2)->id => -3
            ],
            [
                $players->get(0)->id => 1,
                $players->get(1)->id => 2,
                $players->get(2)->id => 3,
                $players->get(3)->id => -6
            ],
            [
                $players->get(0)->id => 2,
                $players->get(1)->id => -3,
                $players->get(2)->id => 1
            ],
            [
                $players->get(0)->id => 3,
                $players->get(1)->id => -6,
                $players->get(2)->id => 1,
                $players->get(3)->id => 2
            ],
        ];

        $expect = [
            '3people' => [
                $players->get(0)->id => 1.5,
                $players->get(1)->id => 2,
                $players->get(2)->id => 2.5
            ],
            '4people' => [
                $players->get(0)->id => 2,
                $players->get(1)->id => 3,
                $players->get(2)->id => 2,
                $players->get(3)->id => 3
            ],
        ];

        foreach ($inputs as $input) {
            $game
                ->gameResults()
                ->save(factory(GameResult::class)->make())
                ->gameResultPlayers()
                ->saveMany(collect($input)->map(function ($point, $playerId) {
                    return GameResultPlayer::make(['player_id' => $playerId, 'point' => $point]);
                }));
        }

        resolve(GameService::class)
            ->getAverageFinishOrder(3)
            ->each(function ($averageFinishOrder, $playerId) use ($expect) {
                $this->assertEquals($expect['3people'][$playerId], $averageFinishOrder);
            });
        resolve(GameService::class)
            ->getAverageFinishOrder(4)
            ->each(function ($averageFinishOrder, $playerId) use ($expect) {
                $this->assertEquals($expect['4people'][$playerId], $averageFinishOrder);
            });
    }
}

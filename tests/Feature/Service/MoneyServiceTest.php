<?php

namespace Tests\Feature\Service;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Collection;

use DB;

use Carbon\Carbon;
use Faker\Generator as Faker;

use App\Money;
use App\MoneyPlayer;
use App\Player;
use App\Service\MoneyService;

class MoneyServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testGetCurrentMoney()
    {
        DB::table('moneys')->delete();

        $finishedMoney = new Money();
        $finishedMoney->id = 1;
        $finishedMoney->finished_at = new Carbon();
        $finishedMoney->save();

        $expect = new Money();
        $expect->id = 2;
        $expect->save();

        $actual = app()->make(MoneyService::class)->getCurrentMoney();

        $this->assertTrue($actual instanceof Money);
        $this->assertEquals($expect->id, $actual->id);
        $this->assertNull($actual->finished_at);
    }

    /**
     * @depends testGetCurrentMoney
     */
    public function testGetCurrentMoneyPlayers()
    {
        $playerCount = 3;

        $expect = factory(Player::class, $playerCount)->create()->map(function ($player) {
            return factory(MoneyPlayer::class)->make(['player_id' => $player->id]);
        });

        Money::create()
            ->moneyPlayers()
            ->saveMany($expect);

        $actual = app()->make(MoneyService::class)->getCurrentMoneyPlayers();

        $this->assertTrue($actual instanceof Collection);
        for ($index = 0; $index < $playerCount; $index++) {
            $this->assertTrue($actual->get($index) instanceof MoneyPlayer);
            $this->assertEquals($expect->get($index)->id, $actual->get($index)->id);
        }
    }

    public function testUpdateMoney()
    {
        DB::table('moneys')->delete();

        $expect = [3000, -1000, -2000];
        $beforeUpdate = [-3000, 1000, 2000];

        $playerCount = 3;

        $players = factory(Player::class, $playerCount)->create();

        $moneyPlayers = [];
        $argument = [];
        for ($index = 0; $index < $playerCount; $index++) {
            $moneyPlayer = MoneyPlayer::make(['player_id' => $players->get($index)->id]);
            $moneyPlayer->money = $beforeUpdate[$index];
            $moneyPlayers[] = $moneyPlayer;

            $argument[$players->get($index)->id] = $expect[$index];
        }

        Money::create()
            ->moneyPlayers()
            ->saveMany($moneyPlayers);

        app()->make(MoneyService::class)->updateMoney($argument);

        for ($index = 0; $index < $playerCount; $index++) {
            $this->assertDatabaseHas(
                'money_players',
                [
                    'id' => $moneyPlayers[$index]->id,
                    'money' => $expect[$index],
                ]
            );
        }
    }

    /**
     * @depends testGetCurrentMoneyPlayers
     */
    public function testResetMoney()
    {
        DB::table('players')->delete();
        DB::table('moneys')->delete();

        $playerCount = 3;

        $players = factory(Player::class, $playerCount)->create();

        $beforeResetMoney = Money::create();

        $moneyService = app()->make(MoneyService::class);

        $moneyService->resetMoney();

        $this->assertNotNull($beforeResetMoney->refresh()->finished_at);

        $moneyService->getCurrentMoneyPlayers()->each(function ($moneyPlayer) use ($players) {
            $this->assertTrue($players->pluck('id')->contains($moneyPlayer->player_id));
        });
    }

    public function testGetPastMoneys()
    {
        DB::table('moneys')->delete();

        $finishedAt2 = new Carbon(app()->make(Faker::class)->datetime);
        $finishedAt1 = new Carbon(app()->make(Faker::class)->datetime($finishedAt2));
        $this->assertTrue($finishedAt1->lt($finishedAt2));

        $pastMoney1 = new Money();
        $pastMoney1->finished_at = $finishedAt1;
        $pastMoney1->save();
        $pastMoney2 = new Money();
        $pastMoney2->finished_at = $finishedAt2;
        $pastMoney2->save();
        Money::create();

        $pastMoneys = app()->make(MoneyService::class)->getPastMoneys();

        $this->assertEquals($pastMoney2->id, $pastMoneys->shift()->id);
        $this->assertEquals($pastMoney1->id, $pastMoneys->shift()->id);
    }
}

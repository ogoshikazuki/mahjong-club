<?php

namespace Tests\Feature\Service;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Collection;

use DB;

use Carbon\Carbon;

use App\Money;
use App\MoneyPlayer;
use App\Player;
use App\Service\MoneyService;

class MoneyServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testGetCurrentMoney(): Money
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

        return $actual;
    }

    public function testCurrentMoneyPlayers()
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
}

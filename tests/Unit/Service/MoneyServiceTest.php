<?php

namespace Tests\Unit\Service;

use PHPUnit\Framework\TestCase;

use Mockery;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

use App\Money;
use App\MoneyPlayer;
use App\Service\MoneyService;

class MoneyServiceTest extends TestCase
{
    public function testGetCurrentMoney()
    {
        $expect = new Money();
        $expect->id = 1;

        $builder = Mockery::mock(Builder::class)
            ->shouldReceive('firstOrFail')
            ->andReturn($expect)
            ->getMock();

        $money = Mockery::mock(Money::class)
            ->shouldReceive('whereNull')
            ->with('finished_at')
            ->andReturn($builder)
            ->getMock();

        $moneyService = new MoneyService($money);

        $actual = $moneyService->getCurrentMoney();

        $this->assertTrue($actual instanceof Money);
        $this->assertEquals($expect->id, $actual->id);
        $this->assertNull($actual->finished_at);
    }

    public function testCurrentMoneyPlayers()
    {
        $moneyPlayer1 = new MoneyPlayer();
        $moneyPlayer1->id = 1;
        $moneyPlayer2 = new MoneyPlayer();
        $moneyPlayer2->id = 2;
        $expect = collect([$moneyPlayer1, $moneyPlayer2]);

        $moneyData = new Money();
        $moneyData->moneyPlayers = $expect;

        $builder = Mockery::mock(Builder::class)
            ->shouldReceive('firstOrFail')
            ->andReturn($moneyData)
            ->getMock();

        $money = Mockery::mock(Money::class)
            ->shouldReceive('whereNull')
            ->with('finished_at')
            ->andReturn($builder)
            ->getMock();

        $moneyService = new MoneyService($money);

        $actual = $moneyService->getCurrentMoneyPlayers();

        $this->assertTrue($actual instanceof Collection);
        for ($index = 0; $index < 2; $index++) {
            $this->assertTrue($actual->get($index) instanceof MoneyPlayer);
            $this->assertEquals($expect->get($index)->id, $actual->get($index)->id);
        }
    }
}

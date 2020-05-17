<?php

namespace Tests\Unit\Service;

use PHPUnit\Framework\TestCase;

use Mockery;

use Illuminate\Database\Eloquent\Builder;

use App\Money;
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
}

<?php

namespace Tests\Feature\Service;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Mockery;

use App\Service\TenhouService;
use App\{
    Player,
    TenhouName,
};

class TenhouServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testDownloadLog()
    {
        $client = Mockery::mock(Client::class)->shouldReceive('get')->getMock();
        $tenhouService = new TenhouService($client, __DIR__);
        $logs = $tenhouService->downloadLog(new Carbon('2020-08-02'), 'C7667');

        foreach ($logs as $log) {
            $this->assertArrayHasKey('roomNumber', $log);
            $this->assertArrayHasKey('startTime', $log);
            $this->assertArrayHasKey('roomType', $log);
            $this->assertArrayHasKey('gameResults', $log);

            foreach ($log['gameResults'] as $gameResult) {
                $this->assertArrayHasKey('playerName', $gameResult);
                $this->assertArrayHasKey('point', $gameResult);
                $this->assertArrayHasKey('tip', $gameResult);
            }
        }
    }

    public function testConvertLogsIntoGameResults()
    {
        $tenhouNames = factory(Player::class, 3)
            ->create()
            ->map(function (Player $player) {
                return factory(TenhouName::class)->create(['player_id' => $player->id]);
            })
            ->all();

        $points = [30, -10, -20];
        $tips = [3, -1, -2];

        $input = [];
        $expected = [];
        for ($index = 0; $index < count($tenhouNames); $index++) {
            $input[] = ['playerName' => $tenhouNames[$index]->name, 'point' => $points[$index], 'tip' => $tips[$index]];
            $expected[$tenhouNames[$index]->player_id] = ['point' => $points[$index], 'tip' => $tips[$index]];
        }

        $tenhouService = resolve(TenhouService::class);
        $gameResults = $tenhouService->convertLogsIntoGameResults([$input]);

        foreach ($gameResults as $gameResult) {
            $this->assertEquals(50, $gameResult['rate']);

            foreach ($gameResult['points'] as $playerId => $point) {
                $this->assertEquals($expected[$playerId]['point'], $point);
            }

            foreach ($gameResult['tips'] as $playerId => $tip) {
                $this->assertEquals($expected[$playerId]['tip'], $tip);
            }
        }
    }
}

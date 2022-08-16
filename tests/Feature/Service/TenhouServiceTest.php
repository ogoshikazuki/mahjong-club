<?php

namespace Tests\Feature\Service;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
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

        $clientException = Mockery::mock(ClientException::class);
        $client = Mockery::mock(Client::class)->shouldReceive('get')->andThrow($clientException)->getMock();
        $tenhouService = new TenhouService($client, __DIR__);
        $logs = $tenhouService->downloadLog(new Carbon('2020-08-02'), 'C7667');
        $this->assertEquals(0, count($logs));
    }

    public function testConvertLogsIntoGameResults()
    {
        $tenhouNames = factory(Player::class, 4)
            ->create()
            ->map(function (Player $player) {
                return factory(TenhouName::class)->create(['player_id' => $player->id]);
            })
            ->all();

        $points = [
            [30, -10, -20],
            [50, 10, -20, -40],
        ];
        $tips = [
            [3, -1, -2],
            [5, 1, -2, -4],
        ];

        $inputs = [];
        $expecteds = [];
        for ($i = 0; $i < 2; $i++) {
            $input = [];
            $expected = [];
            for ($j = 0; $j < count($points[$i]); $j++) {
                $input[] = ['playerName' => $tenhouNames[$j]->name, 'point' => $points[$i][$j], 'tip' => $tips[$i][$j]];
                $expected[$tenhouNames[$j]->player_id] = ['point' => $points[$i][$j], 'tip' => $tips[$i][$j]];
            }

            $inputs[] = $input;
            $expecteds[] = $expected;
        }

        $tenhouService = resolve(TenhouService::class);
        $gameResults = $tenhouService->convertLogsIntoGameResults($inputs);

        for ($i = 0; $i < count($gameResults); $i++) {
            $this->assertEquals($i === 0 ? 100 : 100, $gameResults[$i]['rate']);

            foreach ($gameResults[$i]['points'] as $playerId => $point) {
                $this->assertEquals($expecteds[$i][$playerId]['point'], $point);
            }

            foreach ($gameResults[$i]['tips'] as $playerId => $tip) {
                $this->assertEquals($expecteds[$i][$playerId]['tip'], $tip);
            }
        }
    }
}

<?php

namespace Tests\Feature\Service;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Mockery;

use App\Service\TenhouService;

class TenhouServiceTest extends TestCase
{
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
}

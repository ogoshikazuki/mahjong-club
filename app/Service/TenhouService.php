<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Carbon\Carbon;

use App\TenhouName;

class TenhouService
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function downloadLog(Carbon $date, string $roomNumber): array
    {
        try {
            $path = $this->downloadLogFile($date);
            return $this->parseLogFile($path, $roomNumber);
        } catch (ClientException $e) {
            return [];
        }
    }

    public function convertLogsIntoGameResults(array $tenhouLogs): array
    {
        return collect($tenhouLogs)->map(function (array $tenhouLog) {
            $collection = collect($tenhouLog);
            return [
                'rate' => $this->convertPlayersCountIntoRate($collection->count()),
                'points' => $collection->mapWithKeys(function (array $item) {
                    return [TenhouName::where('name', $item['playerName'])->firstOrFail()->player_id => $item['point']];
                })->toArray(),
                'tips' => $collection->mapWithKeys(function (array $item) {
                    return [TenhouName::where('name', $item['playerName'])->firstOrFail()->player_id => $item['tip']];
                })->toArray(),
            ];
        })->toArray();
    }

    private function downloadLogFile(Carbon $date): string
    {
        $file = "sca{$date->format('Ymd')}.log.gz";
        $url = "https://tenhou.net/sc/raw/dat/{$file}";
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $file;
        $this->client->get($url, ['sink' => $path]);

        return $path;
    }

    private function parseLogFile(string $path, string $roomNumber): array
    {
        $lines = gzfile($path);

        $tenhouLogs = collect();

        foreach ($lines as $line) {
            $tenhouLogs->push(TenhouLog::parse($line));
        }

        $targetTenhouLogs = $tenhouLogs
            ->filter(function ($tenhouLog) use ($roomNumber) {
                return $tenhouLog->getRoomNumber() === $roomNumber;
            })
            ->values();

        return $targetTenhouLogs->toArray();
    }

    private function convertPlayersCountIntoRate(int $playersCount): int
    {
        switch ($playersCount) {
            case 3:
                return 50;
            case 4:
                return 100;
        }
    }
}

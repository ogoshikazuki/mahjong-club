<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Carbon\Carbon;

use App\TenhouName;

class TenhouService
{
    public const LOG_FILE_NAME = 'sca%s.log.gz';
    public const LOG_URL = 'https://tenhou.net/sc/raw/dat/%s';

    private $client;
    private $tempDir;

    public function __construct(Client $client, string $tempDir)
    {
        $this->client = $client;
        $this->tempDir = $tempDir;
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
        $file = $this->createLogFileName($date);
        $url = $this->createLogUrl($file);
        $path = $this->createPath($file);
        $this->client->get($url, ['sink' => $path]);

        return $path;
    }

    private function createLogFileName(Carbon $date): string
    {
        return sprintf(self::LOG_FILE_NAME, $date->format('Ymd'));
    }

    private function createLogUrl(string $file): string
    {
        return sprintf(self::LOG_URL, $file);
    }

    private function createPath(string $file): string
    {
        return $this->tempDir . DIRECTORY_SEPARATOR . $file;
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
        if ($playersCount === 3) {
            return 100;
        }
        return 100;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Service\{
    TenhouService,
    GameService
};

class RegisterGameResultFromTenhouLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenhou:register-log {roomNumber} {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '天鳳ログから戦績を登録する。';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(TenhouService $tenhouService, GameService $gameService)
    {
        $roomNumber = $this->argument('roomNumber');
        $date = new Carbon($this->argument('date'));

        $this->info('downloadLogsAndConvertThemIntoGameResults start.');

        $gameResults = $this->downloadLogsAndConvertThemIntoGameResults($tenhouService, $roomNumber, $date);

        $count = count($gameResults);
        $this->info("downloadLogsAndConvertThemIntoGameResults finish. count: {$count}");

        $this->info('registerGameResults start.');

        $this->registerGameResults($gameService, $gameResults);

        $this->info('registerGameResults finish. complete!');
    }

    private function downloadLogsAndConvertThemIntoGameResults(
        TenhouService $tenhouService,
        string $roomNumber,
        Carbon $date
    ): array {
        $log = $tenhouService->downloadLog($date, $roomNumber);
        $logs = collect($log)->map(function (array $item) {
            return $item['gameResults'];
        })->toArray();
        return $tenhouService->convertLogsIntoGameResults($logs);
    }

    private function registerGameResults(GameService $gameService, array $gameResults): void
    {
        $gameService->startGame();

        foreach ($gameResults as $gameResult) {
            $gameService->registerGameResult($gameResult['rate'], $gameResult['points'], $gameResult['tips']);
        }

        $gameService->finishGame();
    }
}

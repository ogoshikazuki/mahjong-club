<?php

namespace App\Service;

use Illuminate\Support\Collection;

use Carbon\Carbon;

use App\Service\Helper\AverageGetter;
use App\Exceptions\GameStartedException;
use App\{
    Game,
    GameResult,
    GameResultPlayer
};

class GameService
{
    private $moneyService;

    public function __construct(MoneyService $moneyService)
    {
        $this->moneyService = $moneyService;
    }

    public function startGame(): void
    {
        throw_if($this->isGameStarted(), GameStartedException::class);
        Game::create();
    }

    public function isGameStarted(): bool
    {
        return Game::whereNull('finished_at')->exists();
    }

    public function finishGame(): void
    {
        $game = $this->getCurrentGame();
        $money = $this->moneyService->getCurrentMoney();

        $game->gameResults->each(function ($gameResult) use ($money) {
            $rate = $gameResult->rate;
            $gameResult->gameResultPlayers->each(function ($gameResultPlayers) use ($money, $rate) {
                $moneyPlayer = $money->moneyPlayer($gameResultPlayers->player);
                $moneyPlayer->money += $gameResultPlayers->point * $rate;
                $moneyPlayer->save();
            });
        });

        $game->finished_at = Carbon::now();
        $game->save();
    }

    public function getCurrentGame(): Game
    {
        return Game::whereNull('finished_at')->firstOrFail();
    }

    public function registerGameResult(int $rate, array $points): void
    {
        $gameResult = $this->getCurrentGame()->gameResults()->save(new GameResult(['rate' => $rate]));

        foreach ($points as $playerId => $point) {
            if (isset($point)) {
                $gameResult
                    ->gameResultPlayers()
                    ->save(new GameResultPlayer(['player_id' => $playerId, 'point' => $point]));
            }
        }
    }

    public function cancelGame(): void
    {
        $this->getCurrentGame()->delete();
    }

    public function getCurrentMoneyGames(): Collection
    {
        return Game::where('created_at', '>', $this->moneyService->getLastFinishedAt())->get();
    }

    public function rememberLastGameResult(): void
    {
        $lastGameResult = GameResult::latest()->first();
        session(['lastGameResult' => $lastGameResult]);
    }

    public function isRegisteredGameSameTime(): bool
    {
        $lastGameResult = GameResult::latest()->first();
        return $lastGameResult->id !== session('lastGameResult')->id;
    }

    public function getAverageFinishOrder(int $playerCount): Collection
    {
        return $this
            ->getFinishOrders($playerCount)
            ->reduce(function ($averageGetters, $finishOrders) {
                foreach ($finishOrders as $playerId => $finishOrder) {
                    if ($averageGetters->has($playerId)) {
                        $averageGetters->replace([
                            $playerId => $averageGetters->get($playerId)->addTotal($finishOrder)
                        ]);
                        continue;
                    }
                    $averageGetter = new AverageGetter();
                    $averageGetter->addTotal($finishOrder);
                    $averageGetters->put($playerId, $averageGetter);
                }
                return $averageGetters;
            }, collect())
            ->mapWithKeys(function ($averageGetter, $playerId) {
                return [$playerId => $averageGetter->getAverage()];
            });
    }

    /**
     * [
     *     [playerId => 着順, playerId => 着順, ...],
     *     [playerId => 着順, playerId => 着順, ...],
     *     ...
     * ]
     */
    private function getFinishOrders(int $playerCount): Collection
    {
        $gameResults = GameResult::has('gameResultPlayers', '=', $playerCount)->get();

        return $gameResults->map(function ($gameResult) {
            return $gameResult
                ->gameResultPlayers()
                ->orderByDesc('point')
                ->get()
                ->mapWithKeys(function ($gameResultPlayer, $index) {
                    // 着順を`$index + 1`とした。
                    return [$gameResultPlayer->player_id => $index + 1];
                });
        });
    }
}

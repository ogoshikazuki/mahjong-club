<?php

namespace App\Service;

use Illuminate\Support\Collection;

use Carbon\Carbon;

use App\{
    Game,
    GameResult,
    GameResultPlayer,
    Player
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
}

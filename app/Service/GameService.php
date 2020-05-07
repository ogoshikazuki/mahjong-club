<?php

namespace App\Service;

use Carbon\Carbon;

use App\{
    Game,
    GameResult,
    GameResultPlayer,
    Player,
};

class GameService {
    private $pointService;

    public function __construct(PointService $pointService)
    {
        $this->pointService = $pointService;
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
        $point = $this->pointService->getCurrentPoint();

        $game->gameResults->each(function ($gameResult) use ($point) {
            $rate = $gameResult->rate;
            $gameResult->gameResultPlayers->each(function ($gameResultPlayers) use ($point, $rate) {
                $pointPlayer = $point->pointPlayer($gameResultPlayers->player);
                $pointPlayer->point += $gameResultPlayers->point * $rate;
                $pointPlayer->save();
            });
        });

        $game->finished_at = Carbon::now();
        $game->save();
    }

    public function getCurrentGame(): ?Game
    {
        return Game::whereNull('finished_at')->first();
    }

    public function registerGameResult(int $rate, array $points): void
    {
        $gameResult = $this->getCurrentGame()->gameResults()->save(new GameResult(['rate' => $rate]));

        foreach ($points as $playerId => $point) {
            if (isset($point)) {
                $gameResult->gameResultPlayers()->save(new GameResultPlayer(['player_id' => $playerId, 'point' => $point]));
            }
        }
    }
}
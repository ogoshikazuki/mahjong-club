<?php

namespace App\Service;

use Illuminate\Support\Collection;

use Carbon\Carbon;

use App\Exceptions\GameStartedException;
use App\{
    Game,
    GameResult,
    GameResultPlayer,
    Player
};

class GameService
{
    private $moneyService;
    private $playerService;

    public function __construct(MoneyService $moneyService, PlayerService $playerService)
    {
        $this->moneyService = $moneyService;
        $this->playerService = $playerService;
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

    public function getAverageFinishOrder($playerCount): Collection
    {
        $gameResults = GameResult::has('gameResultPlayers', '=', $playerCount)->get();

        $totalFinishOrders = $gameResults->reduce(
            function ($totalFinishOrders, $gameResult) {
                $gameResultPlayers = $gameResult->gameResultPlayers()->orderByDesc('point')->get();
                $finishOrder = 0;
                foreach ($gameResultPlayers as $gameResultPlayer) {
                    $finishOrder++;
                    $totalFinishOrders[$gameResultPlayer->player_id] += $finishOrder;
                }
                return $totalFinishOrders;
            },
            $this->playerService->getAllPlayers()->mapWithKeys(function ($player) {
                return [$player->id => 0];
            })
        );

        return $totalFinishOrders->mapWithKeys(function ($totalFinishOrder, $playerId) use ($gameResults) {
            return [$playerId => $totalFinishOrder / $gameResults->count()];
        });
    }
}

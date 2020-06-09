<?php

namespace App\Service;

use Illuminate\Support\Collection;

use Carbon\Carbon;

use App\Service\Helper\AverageGetter;
use App\Exceptions\GameStartedException;
use App\{
    Game,
    GameResult,
    GameResultPlayer,
    Player
};

class GameService
{
    public const POINT_PER_TIP = 2;

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
                $money = ($gameResultPlayers->point + $gameResultPlayers->tip * self::POINT_PER_TIP) * $rate;
                $moneyPlayer->money += $money;
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

    public function registerGameResult(int $rate, array $points, array $tips): void
    {
        $gameResult = $this->getCurrentGame()->gameResults()->save(new GameResult(['rate' => $rate]));

        foreach ($points as $playerId => $point) {
            if (isset($point)) {
                $gameResult
                    ->gameResultPlayers()
                    ->save(new GameResultPlayer([
                        'player_id' => $playerId,
                        'point' => $point,
                        'tip' => $tips[$playerId] ?? 0,
                    ]));
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

    public function updateGameResult(GameResult $gameResult, int $rate, array $points, array $tips): void
    {
        $gameResult->rate = $rate;
        $gameResult->save();

        foreach ($points as $playerId => $point) {
            $player = Player::findOrFail($playerId);
            $gameResultPlayer = $gameResult->gameResultPlayer($player);

            if (!isset($point) || (int)$point === 0 && (int)$tips[$playerId] === 0) {
                if (isset($gameResultPlayer)) {
                    $gameResultPlayer->delete();
                }
                continue;
            }

            if (isset($gameResultPlayer)) {
                $gameResultPlayer->point = $point;
                $gameResultPlayer->tip = $tips[$playerId];
                $gameResultPlayer->save();
                continue;
            }

            $gameResult
                ->gameResultPlayers()
                ->save(new GameResultPlayer([
                    'player_id' => $playerId,
                    'point' => $point,
                    'tip' => $tips[$playerId] ?? 0,
                ]));
        }
    }
}

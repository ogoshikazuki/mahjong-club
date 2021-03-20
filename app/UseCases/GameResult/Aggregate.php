<?php

namespace App\UseCases\GameResult;

use App\Player;
use App\GameResult;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aggregate
{
    public function __invoke(int $playerCount): array
    {
        $gameResults = $this->getGameResults($playerCount);

        return $gameResults->reduce(function (array $result, GameResult $gameResult) {
            $finishOrder = 0;
            foreach ($gameResult->gameResultPlayers as $gameResultPlayer) {
                $finishOrder++;

                $result[$gameResultPlayer->player_id]['game_count']++;
                $result[$gameResultPlayer->player_id]['point'] += $gameResultPlayer->point;
                $result[$gameResultPlayer->player_id]['tip'] += $gameResultPlayer->tip;
                $result[$gameResultPlayer->player_id][$finishOrder]++;
            }
            return $result;
        }, $this->createInitialValue($playerCount));
    }

    private function getGameResults(int $playerCount): Collection
    {
        return GameResult::has('gameResultPlayers', '=', $playerCount)
            ->with(['gameResultPlayers' => function (HasMany $query) {
                $query->orderBy('point', 'desc');
            }])
            ->get();
    }

    private function createInitialValue(int $playerCount): array
    {
        return Player::all()->mapWithKeys(function (Player $player) use ($playerCount) {
            $initialValue = [$player->id => [
                'game_count' => 0,
                'point' => 0,
                'tip' => 0,
                1 => 0,
                2 => 0,
                3 => 0,
            ]];
            if ($playerCount === 4) {
                $initialValue[$player->id][4] = 0;
            }
            return $initialValue;
        })->toArray();
    }
}

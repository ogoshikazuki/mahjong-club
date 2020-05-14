<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Service\{
    GameService,
    PlayerService
};
use App\Game;

class GameController extends Controller
{
    private $gameService;
    private $playerService;

    public function __construct(GameService $gameService, PlayerService $playerService)
    {
        $this->gameService = $gameService;
        $this->playerService = $playerService;
    }

    /**
     * Display the specified resource.
     *
     * @param  Game  $game
     * @return \Illuminate\Http\Response
     */
    public function show(Game $game)
    {
        return view('game.show')
            ->with('players', $this->playerService->getAllPlayers())
            ->with('game', $game);
    }

    public function startGame()
    {
        $this->gameService->startGame();

        return redirect()->route('home');
    }

    public function finishGame()
    {
        $this->gameService->finishGame();

        return redirect()->route('home');
    }

    public function cancelGame()
    {
        $this->gameService->cancelGame();

        return redirect()->route('home');
    }
}

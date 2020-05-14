<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Rules\ZeroSum;
use App\Service\GameService;
use App\GameResult;
use App\GameResultPlayer;

class GameResultController extends Controller
{
    private $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'rate' => ['required', 'numeric'],
            'points' => ['required', 'array', new ZeroSum],
        ]);

        $this->gameService->registerGameResult($validatedData['rate'], $validatedData['points']);

        return redirect()->route('home');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  GameResult  $gameResult
     * @return \Illuminate\Http\Response
     */
    public function destroy(GameResult $gameResult)
    {
        $gameResult->delete();

        return redirect()->route('home');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\GameResultRequest;
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
    public function store(GameResultRequest $request)
    {
        if ($this->gameService->isRegisteredGameSameTime()) {
            session()->flash('temporaryGameErrorMessage', '他の方と同時に登録されたため、キャンセルしました。');
            return redirect()->route('home');
        }

        $this->gameService->registerGameResult($request->validated()['rate'], $request->validated()['points']);

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

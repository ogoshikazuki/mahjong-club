<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Http\Requests\{
    DownloadTenhouLogRequest,
    RegisterTenhouLogRequest
};
use App\Service\{
    GameService,
    TenhouService
};

class TenhouController extends Controller
{
    private $gameService;
    private $tenhouService;

    public function __construct(GameService $gameService, TenhouService $tenhouService)
    {
        $this->gameService = $gameService;
        $this->tenhouService = $tenhouService;
    }

    public function downloadLog(DownloadTenhouLogRequest $request)
    {
        $result = $this
            ->tenhouService
            ->downloadLog(new Carbon($request->input('date')), $request->input('room_number'));
        return response()->json(['data' => $result]);
    }

    public function registerLog(RegisterTenhouLogRequest $request)
    {
        $gameResults = $this->tenhouService->convertLogsIntoGameResults($request->input('tenhou_logs'));

        $this->gameService->startGame();

        foreach ($gameResults as $gameResult) {
            $this->gameService->registerGameResult($gameResult['rate'], $gameResult['points'], $gameResult['tips']);
        }

        $this->gameService->finishGame();

        return response()->json(['data' => count($gameResults)]);
    }
}

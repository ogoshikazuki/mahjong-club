<?php

namespace App\Http\Controllers;

use App\Service\GameService;

class TenhouController extends Controller
{
    private $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function log()
    {
        if ($this->gameService->isGameStarted()) {
            return redirect()->route('home');
        }
        return view('tenhou_log');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\Player as PlayerResource;
use App\Service\PlayerService;

class PlayerController extends Controller
{
    private $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PlayerResource::collection($this->playerService->getAllPlayers());
    }
}

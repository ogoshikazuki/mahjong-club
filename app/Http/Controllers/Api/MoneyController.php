<?php

namespace App\Http\Controllers\Api;

use App\Service\MoneyService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Money as MoneyResource;
use Illuminate\Contracts\Routing\ResponseFactory;

class MoneyController extends Controller
{
    private $moneyService;

    public function __construct(MoneyService $moneyService)
    {
        $this->moneyService = $moneyService;
    }

    public function getCurrent()
    {
        return new MoneyResource($this->moneyService->getCurrentMoney());
    }

    public function getPast()
    {
        return MoneyResource::collection($this->moneyService->getPastMoneys());
    }

    public function reset(ResponseFactory $responseFactory)
    {
        $this->moneyService->resetMoney();

        return $responseFactory->noContent();
    }
}

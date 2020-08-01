<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;

use App\Http\Controllers\Controller;
use App\Http\Requests\DownloadTenhouLogRequest;
use App\Service\TenhouService;

class TenhouController extends Controller
{
    private $tenhouService;

    public function __construct(TenhouService $tenhouService)
    {
        $this->tenhouService = $tenhouService;
    }

    public function downloadLog(DownloadTenhouLogRequest $request)
    {
        $result = $this
            ->tenhouService
            ->downloadLog(new Carbon($request->input('date')), $request->input('room_number'));
        return response()->json(['data' => $result]);
    }
}

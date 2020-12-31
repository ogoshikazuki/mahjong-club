<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Money extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'finished_at' => (new Carbon($this->resource->finished_at))->format('Y/m/d'),
            'money_players' => MoneyPlayer::collection($this->resource->moneyPlayers),
        ];
    }
}

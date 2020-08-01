<?php

namespace App\Service;

use Illuminate\Contracts\Support\Arrayable;

class TenhouLog implements Arrayable
{
    private $roomNumber;
    private $startTime;
    private $roomType;
    private $gameResults;

    public function __construct()
    {
        $this->gameResults = collect();
    }

    public function toArray(): array
    {
        return [
            'roomNumber' => $this->roomNumber,
            'startTime' => $this->startTime,
            'roomType' => $this->roomType,
            'gameResults' => $this->gameResults->toArray(),
        ];
    }

    public function getRoomNumber(): string
    {
        return $this->roomNumber;
    }

    public static function parse(string $line): self
    {
        $exploded = explode(' | ', $line);

        $self = new self();
        $self->roomNumber = $exploded[0];
        $self->startTime = $exploded[1];
        $self->roomType = $exploded[2];

        foreach (explode(' ', $exploded[3]) as $gameResultText) {
            $self->gameResults->push(TenhouLogGameResult::parse($gameResultText));
        }

        return $self;
    }
}

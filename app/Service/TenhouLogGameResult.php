<?php

namespace App\Service;

use Illuminate\Contracts\Support\Arrayable;

class TenhouLogGameResult implements Arrayable
{
    private $playerName;
    private $point;
    private $tip;

    public function toArray(): array
    {
        return [
            'playerName' => $this->playerName,
            'point' => $this->point,
            'tip' => $this->tip,
        ];
    }

    public static function parse(string $text): self
    {
        $matches = [];
        preg_match('/(.+)\(([^,]+)(,(.+)枚)?\)/', $text, $matches);

        $self = new self();
        $self->playerName = $matches[1];
        $self->point = (int)$matches[2];
        if (isset($matches[4])) {
            $self->tip = (int)$matches[4];
        }

        return $self;
    }
}

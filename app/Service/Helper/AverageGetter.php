<?php

namespace App\Service\Helper;

class AverageGetter
{
    private $total = 0;
    private $count = 0;

    public function addTotal(int $number): void
    {
        $this->total += $number;
        $this->count++;
    }

    public function getAverage(): float
    {
        return $this->total / $this->count;
    }
}
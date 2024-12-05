<?php

namespace App\Strategies;

class NoFeeStrategy implements FeeStrategy
{
    public function calculate(float $amount): float
    {
        return 0;
    }
}

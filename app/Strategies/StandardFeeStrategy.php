<?php

namespace App\Strategies;

class StandardFeeStrategy implements FeeStrategy
{
    public function calculate(float $amount): float
    {
        return (2.5 * $amount) / 100;
    }
}

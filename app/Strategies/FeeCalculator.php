<?php

namespace App\Strategies;

class FeeCalculator
{
    protected FeeStrategy $strategy;

    public function __construct(FeeStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function setStrategy(FeeStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function calculateFee(float $amount): float
    {
        return $this->strategy->calculate($amount);
    }
}

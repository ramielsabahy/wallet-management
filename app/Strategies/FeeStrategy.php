<?php

namespace App\Strategies;

interface FeeStrategy
{
    public function calculate(float $amount): float;
}

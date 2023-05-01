<?php

declare(strict_types=1);

namespace App\Tests\Helper\FixtureFactory;

use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;

class MoneyBreakdownFixtureFactory
{
    public function create(array $breakdown): MoneyBreakdown
    {
        $breakdownValues = array_map(fn (float $value) => new Money($value), $breakdown);

        return new MoneyBreakdown($breakdownValues);
    }
}

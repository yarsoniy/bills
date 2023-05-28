<?php

declare(strict_types=1);

namespace App\Controller\Money\DTO;

class MoneyBreakdownDTO
{
    public function __construct(
        /** @var array<string, float> */
        readonly public ?array $values
    ) {
    }
}

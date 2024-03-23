<?php

declare(strict_types=1);

namespace App\Controller\Bill\DTO;

use App\Controller\Money\DTO\MoneyBreakdownDTO;

class ParticipantSummaryDTO
{
    public function __construct(
        readonly public MoneyBreakdownDTO $breakdown,
        readonly public MoneyBreakdownDTO $deposits,
        readonly public MoneyBreakdownDTO $balance,
    ) {
    }
}

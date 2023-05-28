<?php

declare(strict_types=1);

namespace App\Controller\Money\DTOMapper;

use App\Controller\Money\DTO\MoneyBreakdownDTO;
use App\Domain\Money\Model\MoneyBreakdown;

class MoneyBreakdownMapper
{
    public function toDTO(?MoneyBreakdown $breakdown): ?MoneyBreakdownDTO
    {
        if (!$breakdown) {
            return null;
        }

        $values = [];
        foreach ($breakdown->items as $key => $money) {
            $values[$key] = $money->value;
        }

        return new MoneyBreakdownDTO($values);
    }
}

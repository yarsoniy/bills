<?php

declare(strict_types=1);

namespace App\Controller\Money\DTOMapper;

use App\Controller\Money\DTO\MoneyBreakdownDTO;
use App\Domain\Money\Model\Money;
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

    public function fromDTO(?MoneyBreakdownDTO $dto): ?MoneyBreakdown
    {
        if (!$dto) {
            return null;
        }

        $moneyValues = [];
        foreach ($dto->getValues() as $key => $value) {
            $moneyValues[$key] = new Money($value);
        }

        return new MoneyBreakdown($moneyValues);
    }
}

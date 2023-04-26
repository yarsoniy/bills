<?php

declare(strict_types=1);

namespace App\Domain\DebtResolver\Service;

use App\Domain\AccountingBook\Model\Operation;
use App\Domain\AccountingBook\Model\OperationType;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Domain\Participant\ParticipantId;

class DebtResolver
{
    /**
     * @return Operation[]
     */
    public function resolve(MoneyBreakdown $balance): array
    {
        $positiveItems = [];
        $negativeItems = [];
        foreach ($balance->items as $key => $item) {
            if ($item->value >= 0) {
                $positiveItems[$key] = $item;
            } else {
                $negativeItems[$key] = $item;
            }
        }
        uasort($positiveItems, fn (Money $a, Money $b) => $a->value <=> $b->value);
        uasort($negativeItems, fn (Money $a, Money $b) => $a->value <=> $b->value);

        $operations = [];
        foreach ($negativeItems as $negativeKey => $negativeItem) {
            foreach ($positiveItems as $positiveKey => $positiveItem) {
                if (0 == $positiveItem->value) {
                    continue;
                }

                $amount = $negativeItem->abs();
                if ($positiveItem->value < $amount->value) {
                    $amount = $positiveItem;
                }

                $operations[] = new Operation(
                    OperationType::PAY_BACK,
                    new ParticipantId($negativeKey),
                    new ParticipantId($positiveKey),
                    $amount
                );
                $negativeItem = $negativeItem->add($amount)->round();
                $negativeItems[$negativeKey] = $negativeItem;
                $positiveItems[$positiveKey] = $positiveItem->sub($amount)->round();

                if (0 == $negativeItem->value) {
                    continue 2;
                }
            }
        }

        return $operations;
    }
}

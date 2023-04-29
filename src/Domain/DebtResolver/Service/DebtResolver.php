<?php

declare(strict_types=1);

namespace App\Domain\DebtResolver\Service;

use App\Domain\AccountingBook\Model\Transaction;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Domain\Participant\ParticipantId;

class DebtResolver
{
    /**
     * @return Transaction[]
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

        $transactions = [];
        foreach ($negativeItems as $negativeKey => $negativeItem) {
            foreach ($positiveItems as $positiveKey => $positiveItem) {
                if (0 == $positiveItem->value) {
                    continue;
                }

                $amount = $negativeItem->abs();
                if ($positiveItem->value < $amount->value) {
                    $amount = $positiveItem;
                }

                $transactions[] = new Transaction(
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

        return $transactions;
    }
}

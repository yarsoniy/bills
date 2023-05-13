<?php

declare(strict_types=1);

namespace App\Domain\Money\Model;

readonly class MoneyBreakdown
{
    /**
     * Indexed or not.
     *
     * @var Money[]
     */
    public array $items;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function get(string $index): Money
    {
        return $this->items[$index] ?? new Money();
    }

    public function add(string $index, Money $money): self
    {
        $itemsCopy = $this->items;

        $newMoneyValue = $this->get($index)->add($money);
        $itemsCopy[$index] = $newMoneyValue;

        return new self($itemsCopy);
    }

    public function keys(): array
    {
        return array_keys($this->items);
    }

    public function merge(self $breakdown): self
    {
        $allKeys = array_unique(array_merge($this->keys(), $breakdown->keys()));

        $mergedItems = [];
        foreach ($allKeys as $key) {
            $mergedItems[$key] = $this->get($key)->add($breakdown->get($key));
        }

        return new self($mergedItems);
    }

    public function sum(): Money
    {
        $result = new Money();
        foreach ($this->items as $item) {
            $result = $result->add($item);
        }

        return $result;
    }

    public function round(): self
    {
        $roundedItems = [];
        foreach ($this->items as $key => $value) {
            $roundedItems[$key] = $value->round();
        }

        return new self($roundedItems);
    }

    /**
     * Solves the problem when the total != sum of rounded parts
     * Example:
     *    total1 = 100.00
     *    A = 33.33
     *    B = 33.33
     *    C = 33.33
     *    total2 = A + B + C = 99.99
     *    total1 != total2  <=== not equal.
     *
     * The function corrects each part to make sum of parts equal $desiredTotal
     */
    public function roundWithCorrection(Money $desiredTotal): self
    {
        $roundedBreakdown = $this->round();
        $difference = $desiredTotal->sub($roundedBreakdown->sum())->round();

        $differenceInCents = (int) ($difference->value * 100);
        $remainderInCents = $differenceInCents % \count($roundedBreakdown->items);
        $remainderCent = $remainderInCents < 0 ? -1 : 1;

        // divide the amount of cents equally for all items
        $itemCorrection = (int) ($differenceInCents / \count($roundedBreakdown->items));

        $itemValues = array_map(fn (Money $item) => $item->value, $roundedBreakdown->items);
        foreach ($itemValues as $key => $value) {
            if (!$value) {
                continue;
            }
            $correction = $itemCorrection;

            // spread the remainder cents to all items
            if (abs($remainderInCents) > 0) {
                $correction += $remainderCent;
                $remainderInCents -= $remainderCent;
            }

            $itemValues[$key] += $correction / 100;
        }
        $resultItems = array_map(fn (float $value) => new Money($value), $itemValues);

        return (new self($resultItems))->round();
    }
}

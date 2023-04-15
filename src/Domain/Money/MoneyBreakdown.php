<?php

declare(strict_types=1);

namespace App\Domain\Money;

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

    private function get(string $index): Money
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
}

<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

use App\Domain\Money\Money;
use App\Domain\Money\MoneyBreakdown;

class Bill
{
    private BillId $id;

    private string $title;

    /** @var BillItem[] */
    private array $items = [];

    private MoneyBreakdown $deposit;

    public function addItem(BillItem $item): void
    {
        $this->items[] = $item;
    }

    public function calculateTotal(): Money
    {
        $total = new Money();
        foreach ($this->items as $item) {
            $total = $total->add($item->getCost());
        }

        return $total->round();
    }

    public function getCount(): int
    {
        return \count($this->items);
    }

    public function calculateTotalBreakdown(): MoneyBreakdown
    {
        $total = $this->calculateTotal();

        return $this->mergeItemBreakdowns()->roundWithCorrection($total);
    }

    private function mergeItemBreakdowns(): MoneyBreakdown
    {
        $totalBreakdown = new MoneyBreakdown();
        foreach ($this->items as $item) {
            $itemBreakdown = $item->calculateBreakdown();
            $totalBreakdown = $totalBreakdown->merge($itemBreakdown);
        }

        return $totalBreakdown;
    }
}

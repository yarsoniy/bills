<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

use App\Domain\AccountingBook\Model\Transaction;
use App\Domain\Bill\View\BillItemView;
use App\Domain\DebtResolver\Service\DebtResolver;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;

class Bill
{
    private BillId $id;

    private string $title;

    /** @var BillItem[] */
    private array $items = [];

    private MoneyBreakdown $participantDeposits;

    public function addItem(BillItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return BillItem[]
     */
    public function getItems(): array
    {
        return array_map(fn (BillItem $i) => new BillItemView($i), $this->items);
    }

    public function calculateTotalCost(): Money
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
        $total = $this->calculateTotalCost();

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

    private function calculateBalance(): MoneyBreakdown
    {
        $totalCostBreakdown = $this->calculateTotalBreakdown();
        $deposits = $this->participantDeposits;
        $allKeys = array_unique(array_merge($deposits->keys(), $totalCostBreakdown->keys()));

        $balance = new MoneyBreakdown();
        foreach ($allKeys as $key) {
            $participantBalance = $deposits->get($key)->sub($totalCostBreakdown->get($key));
            $balance = $balance->add($key, $participantBalance);
        }

        return $balance->round();
    }

    /**
     * @return Transaction[]
     */
    public function suggestSettleUp(DebtResolver $resolver): array
    {
        return $resolver->resolve($this->calculateBalance());
    }
}

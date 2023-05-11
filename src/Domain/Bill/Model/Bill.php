<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

use App\Domain\AccountingBook\Model\Transaction;
use App\Domain\Bill\Exception\BillItemNotFoundException;
use App\Domain\Bill\View\BillItemView;
use App\Domain\DebtResolver\Service\DebtResolver;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;

class Bill
{
    private BillId $id;

    private ParticipantGroupId $groupId;

    private string $title;

    private \DateTimeImmutable $createdAt;

    private array $items = [];

    private MoneyBreakdown $participantDeposits;

    public function __construct(BillId $id, ParticipantGroupId $groupId)
    {
        $this->id = $id;
        $this->groupId = $groupId;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): BillId
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function addItem(BillItem $item): void
    {
        $this->items[$item->getId()->id] = $item;
    }

    private function getItem(BillItemId $itemId): BillItem
    {
        $item = $this->items[$itemId->id] ?? null;
        if (!$item) {
            throw BillItemNotFoundException::withId($itemId);
        }

        return $item;
    }

    /**
     * @return BillItemView[]
     */
    public function getItemsView(): array
    {
        return array_map(fn (BillItem $i) => new BillItemView($i), $this->items);
    }

    public function setItemTitle(BillItemId $itemId, string $title): void
    {
        $item = $this->getItem($itemId);
        $item->setTitle($title);
    }

    public function setItemCost(BillItemId $itemId, Money $cost): void
    {
        $item = $this->getItem($itemId);
        $item->setCost($cost);
    }

    /**
     * @param Payment[] $payments
     */
    public function setItemPayments(BillItemId $itemId, array $payments): void
    {
        $item = $this->getItem($itemId);
        $item->clearPayments();
        foreach ($payments as $payment) {
            $item->addPayment($payment);
        }
    }

    public function getParticipantDeposits(): MoneyBreakdown
    {
        return $this->participantDeposits;
    }

    public function setParticipantDeposits(MoneyBreakdown $participantDeposits): void
    {
        $this->participantDeposits = $participantDeposits;
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

    public function calculateItemBreakdown(BillItemId $itemId): MoneyBreakdown
    {
        $item = $this->getItem($itemId);

        return $item->calculateBreakdown();
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

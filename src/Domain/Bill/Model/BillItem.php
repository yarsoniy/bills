<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;

class BillItem
{
    private BillItemId $id;

    private string $title;

    private Money $cost;

    /** @var Payment[] */
    private array $payments;

    private \DateTimeImmutable $createdAt;

    public function __construct(BillItemId $id)
    {
        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): BillItemId
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

    public function getCost(): Money
    {
        return $this->cost;
    }

    public function setCost(Money $cost): void
    {
        $this->cost = $cost;
    }

    public function clearPayments(): void
    {
        $this->payments = [];
    }

    public function addPayment(Payment $payment): void
    {
        $this->payments[] = $payment;
    }

    /**
     * @return Payment[]
     */
    public function getPayments(): array
    {
        return $this->payments;
    }

    public function calculateBreakdown(): MoneyBreakdown
    {
        // Find how many Payers will pay for each User share
        // If the number is 1 than the share is paid by 1 payer
        // If the number is 3 than the share is divided and paid by 3 payers
        // etc...
        $numberOfPayersOfUserShare = [];
        foreach ($this->payments as $direction) {
            $userId = $direction->itemUser->id;
            $numberOfPayersOfUserShare[$userId] = $numberOfPayersOfUserShare[$userId] ?? 0;
            ++$numberOfPayersOfUserShare[$userId];
        }

        // Split the share of each User by number of Payers
        // Find the amount of money that each payer will pay for each User share
        $userSharesSplitForPayers = $this->cost->splitByKey(array_keys($numberOfPayersOfUserShare));
        $userSplitShares = [];
        foreach ($userSharesSplitForPayers->items as $userId => $share) {
            $splitNumber = $numberOfPayersOfUserShare[$userId];
            $userSplitShares[$userId] = $share->split($splitNumber);
        }

        // Find how many each Payer should pay
        $payerShares = new MoneyBreakdown();
        foreach ($this->payments as $direction) {
            $payerId = $direction->itemPayer->id;

            /** @var Money $shareToAdd */
            $shareToAdd = array_shift($userSplitShares[$direction->itemUser->id]);
            $payerShares = $payerShares->add($payerId, $shareToAdd);
        }

        return $payerShares;
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Domain\ParticipantGroup\Model\ParticipantId;

class BillItem
{
    private string $title;

    private Money $cost;

    /** @var Payment[] */
    private array $payments;

    public function __construct(string $title, Money $cost = new Money())
    {
        $this->title = $title;
        $this->cost = $cost;
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

    public function addPayment(ParticipantId $itemPayer, ParticipantId $itemUser): void
    {
        $this->payments[] = new Payment($itemPayer, $itemUser);
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

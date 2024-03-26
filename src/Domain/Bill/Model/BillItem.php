<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;

class BillItem
{
    public function __construct(
        private BillItemId $id,

        private \DateTimeImmutable $createdAt = new \DateTimeImmutable(),

        private string $title = 'No title',

        private Money $cost = new Money(),

        private SplitAgreement $agreement = new SplitAgreement([]),
    ) {
    }

    public function getId(): BillItemId
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
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

    public function getAgreement(): SplitAgreement
    {
        return $this->agreement;
    }

    public function setAgreement(SplitAgreement $agreement): void
    {
        $this->agreement = $agreement;
    }

    public function calculateBreakdown(): MoneyBreakdown
    {
        $operations = $this->agreement->getOperations();

        if (!$operations) {
            return new MoneyBreakdown();
        }

        // Find how many Payers will pay for each User share
        // If the number is 1 than the share is paid by 1 payer
        // If the number is 3 than the share is divided and paid by 3 payers
        // etc...
        $numberOfPayersOfUserShare = [];
        foreach ($operations as $operation) {
            $userId = $operation->itemUser->id;
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
        foreach ($operations as $operation) {
            $payerId = $operation->itemPayer->id;

            /** @var Money $shareToAdd */
            $shareToAdd = array_shift($userSplitShares[$operation->itemUser->id]);
            $payerShares = $payerShares->add($payerId, $shareToAdd);
        }

        return $payerShares;
    }
}

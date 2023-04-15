<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

use App\Domain\Money\Money;
use App\Domain\Money\MoneyBreakdown;
use App\Domain\Participant\ParticipantId;

class BillItem
{
    private string $title;

    private Money $cost;

    /** @var PaymentDirection[] */
    private array $paymentDirections;

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

    public function addPaymentDirection(ParticipantId $payer, ParticipantId $buyer): void
    {
        $this->paymentDirections[] = new PaymentDirection($payer, $buyer);
    }

    /**
     * @return PaymentDirection[]
     */
    public function getPaymentDirections(): array
    {
        return $this->paymentDirections;
    }

    public function calculateBreakdown(): MoneyBreakdown
    {
        // Find how many Payers will pay for each Buyer share
        // If the number is 1 than the share is paid by 1 payer
        // If the number is 3 than the share is divided and paid by 3 payers
        // etc...
        $numberOfPayersOfBuyerShare = [];
        foreach ($this->paymentDirections as $direction) {
            $buyerId = $direction->buyer->id;
            $numberOfPayersOfBuyerShare[$buyerId] = $numberOfPayersOfBuyerShare[$buyerId] ?? 0;
            ++$numberOfPayersOfBuyerShare[$buyerId];
        }

        // Split the share of each Buyer by number of Payers
        // Find the amount of money that each payer will pay for each Buyers share
        $buyerSharesSplitForPayers = $this->cost->splitByKey(array_keys($numberOfPayersOfBuyerShare));
        $buyerSplitShares = [];
        foreach ($buyerSharesSplitForPayers->items as $buyerId => $share) {
            $splitNumber = $numberOfPayersOfBuyerShare[$buyerId];
            $buyerSplitShares[$buyerId] = $share->split($splitNumber);
        }

        // Find how many each Payer should pay
        $payerShares = new MoneyBreakdown();
        foreach ($this->paymentDirections as $direction) {
            $payerId = $direction->payer->id;

            /** @var Money $shareToAdd */
            $shareToAdd = array_shift($buyerSplitShares[$direction->buyer->id]);
            $payerShares = $payerShares->add($payerId, $shareToAdd);
        }

        return $payerShares;
    }
}

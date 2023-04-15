<?php

namespace App\Domain\Bill\Model;

use App\Domain\Money\Money;
use App\Domain\Participant\ParticipantId;

class BillItem
{
    private string $title;

    private Money $cost;

    /** @var PaymentDirection[]  */
    private array $paymentDirections;

    public function __construct(string $title, Money $cost = new Money())
    {
        $this->title = $title;
        $this->cost = $cost;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return Money
     */
    public function getCost(): Money
    {
        return $this->cost;
    }

    /**
     * @param Money $cost
     */
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

    /**
     * Index by @see ParticipantId
     * @return Money[]
     */
    public function calculatePayerShares(): array
    {
        $buyerSplits = [];
        foreach ($this->paymentDirections as $direction) {
            $buyerId = $direction->buyer->id;
            if (!isset($buyerSplits[$buyerId])) {
                $buyerSplits[$buyerId] = 0;
            }
            $buyerSplits[$buyerId]++;
        }

        $uniqueBuyers = array_keys($buyerSplits);
        $equalShares = $this->cost->split(count($uniqueBuyers));

        /** @var Money[] $buyerEqualShares */
        $buyerEqualShares = array_combine($uniqueBuyers, $equalShares);

        $buyerSplitShares = [];
        foreach ($buyerEqualShares as $buyerId => $share) {
            $splitNumber = $buyerSplits[$buyerId];
            $buyerSplitShares[$buyerId] = $share->split($splitNumber);
        }

        /** @var Money[] $payerShares */
        $payerShares = [];
        foreach ($this->paymentDirections as $direction) {
            $payerId = $direction->payer->id;
            if (!isset($payerShares[$payerId])) {
                $payerShares[$payerId] = new Money();
            }

            /** @var Money $shareToAdd */
            $shareToAdd = array_shift($buyerSplitShares[$direction->buyer->id]);

            $payerShares[$payerId] = $payerShares[$payerId]->add($shareToAdd);
        }

        return $payerShares;
    }
}

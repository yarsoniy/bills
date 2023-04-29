<?php

declare(strict_types=1);

namespace App\Domain\Bill\View;

use App\Domain\Bill\Model\BillItem;
use App\Domain\Bill\Model\Payment;
use App\Domain\Money\Model\Money;

class BillItemView
{
    private BillItem $billItem;

    public function __construct(BillItem $billItem)
    {
        $this->billItem = $billItem;
    }

    public function getTitle(): string
    {
        return $this->billItem->getTitle();
    }

    public function getCost(): Money
    {
        return $this->billItem->getCost();
    }

    /**
     * @return Payment[]
     */
    public function getPayments(): array
    {
        return $this->billItem->getPayments();
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Bill\View;

use App\Domain\Bill\Model\BillItem;
use App\Domain\Bill\Model\BillItemId;
use App\Domain\Bill\Model\SplitAgreement;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;

class BillItemView
{
    private BillItem $billItem;

    public function __construct(BillItem $billItem)
    {
        $this->billItem = $billItem;
    }

    public function getId(): BillItemId
    {
        return $this->billItem->getId();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->billItem->getCreatedAt();
    }

    public function getTitle(): string
    {
        return $this->billItem->getTitle();
    }

    public function getCost(): Money
    {
        return $this->billItem->getCost();
    }

    public function getAgreement(): SplitAgreement
    {
        return $this->billItem->getAgreement();
    }

    public function calculateBreakdown(): MoneyBreakdown
    {
        return $this->billItem->calculateBreakdown();
    }
}

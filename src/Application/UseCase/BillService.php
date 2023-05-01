<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Bill\Model\Bill;
use App\Domain\Bill\Model\BillId;
use App\Domain\Bill\Service\BillRepositoryInterface;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;

class BillService
{
    public function __construct(
        readonly private BillRepositoryInterface $repository
    ) {
    }

    public function createBill(string $title): BillId
    {
        $id = $this->repository->nextId();
        $bill = new Bill($id);
        $bill->setTitle($title);
        $this->repository->add($bill);

        return $bill->getId();
    }

    public function setParticipantDeposits(BillId $id, MoneyBreakdown $deposits): void
    {
        $bill = $this->repository->getById($id);
        $bill->setParticipantDeposits($deposits);
    }

    public function calculateTotalCost(BillId $id): Money
    {
        $bill = $this->repository->getById($id);

        return $bill->calculateTotalCost();
    }

    public function calculateTotalBreakdown(BillId $id): MoneyBreakdown
    {
        $bill = $this->repository->getById($id);

        return $bill->calculateTotalBreakdown();
    }
}

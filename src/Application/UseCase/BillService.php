<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Bill\Model\Bill;
use App\Domain\Bill\Model\BillId;
use App\Domain\Bill\Service\BillRepositoryInterface;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Domain\ParticipantGroup\Exception\ParticipantGroupNotFoundException;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use App\Domain\ParticipantGroup\Service\ParticipantGroupRepositoryInterface;

class BillService
{
    public function __construct(
        readonly private BillRepositoryInterface $billRepository,
        readonly private ParticipantGroupRepositoryInterface $participantGroupRepository
    ) {
    }

    public function createBill(ParticipantGroupId $groupId, string $title): BillId
    {
        if (!$this->participantGroupRepository->findById($groupId)) {
            throw ParticipantGroupNotFoundException::withId($groupId);
        }

        $id = $this->billRepository->nextId();
        $bill = new Bill($id, $groupId);
        $bill->setTitle($title);
        $this->billRepository->save($bill);

        return $bill->getId();
    }

    public function getBill(BillId $id): Bill
    {
        return $this->billRepository->getById($id);
    }

    public function findByParticipantGroup(ParticipantGroupId $groupId): array
    {
        if (!$this->participantGroupRepository->findById($groupId)) {
            throw ParticipantGroupNotFoundException::withId($groupId);
        }

        return $this->billRepository->findByParticipantGroup($groupId);
    }

    public function setParticipantDeposits(BillId $id, MoneyBreakdown $deposits): void
    {
        $bill = $this->billRepository->getById($id);
        $bill->setParticipantDeposits($deposits);
        $this->billRepository->save($bill);
    }

    public function calculateTotalCost(BillId $id): Money
    {
        $bill = $this->billRepository->getById($id);

        return $bill->calculateTotalCost();
    }

    public function calculateTotalBreakdown(BillId $id): MoneyBreakdown
    {
        $bill = $this->billRepository->getById($id);

        return $bill->calculateTotalBreakdown();
    }
}

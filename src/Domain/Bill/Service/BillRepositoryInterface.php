<?php

declare(strict_types=1);

namespace App\Domain\Bill\Service;

use App\Domain\Bill\Exception\BillItemNotFoundException;
use App\Domain\Bill\Exception\BillNotFoundException;
use App\Domain\Bill\Model\Bill;
use App\Domain\Bill\Model\BillId;
use App\Domain\Bill\Model\BillItemId;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;

interface BillRepositoryInterface
{
    public function nextId(): BillId;

    public function nextItemId(): BillItemId;

    public function add(Bill $bill): void;

    public function findById(BillId $id): ?Bill;

    /**
     * @throws BillNotFoundException
     */
    public function getById(BillId $id): Bill;

    public function findByParticipantGroup(ParticipantGroupId $groupId): array;

    /**
     * @throws BillItemNotFoundException
     */
    public function getByItemId(BillItemId $itemId): Bill;
}

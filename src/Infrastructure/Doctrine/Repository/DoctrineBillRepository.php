<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Bill\Exception\BillNotFoundException;
use App\Domain\Bill\Model\Bill;
use App\Domain\Bill\Model\BillId;
use App\Domain\Bill\Model\BillItemId;
use App\Domain\Bill\Service\BillRepositoryInterface;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use App\Infrastructure\Uuid\UuidServiceInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineBillRepository extends ServiceEntityRepository implements BillRepositoryInterface
{
    private readonly UuidServiceInterface $uuidService;

    public function __construct(
        ManagerRegistry $registry,
        UuidServiceInterface $uuidService
    ) {
        $this->uuidService = $uuidService;
        parent::__construct($registry, Bill::class);
    }

    public function nextId(): BillId
    {
        return new BillId($this->uuidService->generate());
    }

    public function nextItemId(): BillItemId
    {
        return new BillItemId($this->uuidService->generate());
    }

    public function add(Bill $bill): void
    {
        $this->getEntityManager()->persist($bill);
    }

    public function findById(BillId $id): ?Bill
    {
        return $this->find($id);
    }

    public function getById(BillId $id): Bill
    {
        $entity = $this->findById($id);
        if (!$entity) {
            throw BillNotFoundException::withId($id);
        }

        return $entity;
    }

    public function findByParticipantGroup(ParticipantGroupId $groupId): array
    {
        $result = $this->findBy(['groupId' => $groupId]);

        return $result ?: [];
    }

    public function getByItemId(BillItemId $itemId): Bill
    {
        // TODO: Implement getByItemId() method.
    }
}

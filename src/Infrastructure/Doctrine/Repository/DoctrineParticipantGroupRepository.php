<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\ParticipantGroup\Exception\ParticipantGroupNotFoundException;
use App\Domain\ParticipantGroup\Model\ParticipantGroup;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use App\Domain\ParticipantGroup\Model\ParticipantId;
use App\Domain\ParticipantGroup\Service\ParticipantGroupRepositoryInterface;
use App\Infrastructure\Uuid\UuidServiceInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineParticipantGroupRepository extends ServiceEntityRepository implements ParticipantGroupRepositoryInterface
{
    private readonly UuidServiceInterface $uuidService;

    public function __construct(
        ManagerRegistry $registry,
        UuidServiceInterface $uuidService
    ) {
        $this->uuidService = $uuidService;
        parent::__construct($registry, ParticipantGroup::class);
    }

    public function nextId(): ParticipantGroupId
    {
        return new ParticipantGroupId($this->uuidService->generate());
    }

    public function nextParticipantId(): ParticipantId
    {
        return new ParticipantId($this->uuidService->generate());
    }

    public function add(ParticipantGroup $group): void
    {
        $this->getEntityManager()->persist($group);
    }

    public function findById(ParticipantGroupId $id): ?ParticipantGroup
    {
        return $this->find($id);
    }

    public function getById(ParticipantGroupId $id): ParticipantGroup
    {
        $group = $this->findById($id);
        if (!$group) {
            throw ParticipantGroupNotFoundException::withId($id);
        }

        return $group;
    }
}

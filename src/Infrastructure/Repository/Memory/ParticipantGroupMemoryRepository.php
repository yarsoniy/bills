<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\Memory;

use App\Domain\ParticipantGroup\Exception\ParticipantGroupNotFoundException;
use App\Domain\ParticipantGroup\Model\ParticipantGroup;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use App\Domain\ParticipantGroup\Model\ParticipantId;
use App\Domain\ParticipantGroup\Service\ParticipantGroupRepositoryInterface;
use App\Infrastructure\Uuid\UuidServiceInterface;

class ParticipantGroupMemoryRepository implements ParticipantGroupRepositoryInterface
{
    private array $collection;

    public function __construct(
        readonly private UuidServiceInterface $uuidService
    ) {
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
        $this->collection[$group->getId()->id] = $group;
    }

    public function findById(ParticipantGroupId $id): ?ParticipantGroup
    {
        return $this->collection[$id->id] ?? null;
    }

    public function getById(ParticipantGroupId $id): ParticipantGroup
    {
        $result = $this->findById($id);
        if (!$result) {
            throw ParticipantGroupNotFoundException::withId($id);
        }

        return $result;
    }
}

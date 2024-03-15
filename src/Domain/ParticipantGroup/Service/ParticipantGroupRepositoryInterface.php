<?php

declare(strict_types=1);

namespace App\Domain\ParticipantGroup\Service;

use App\Domain\ParticipantGroup\Exception\ParticipantGroupNotFoundException;
use App\Domain\ParticipantGroup\Model\ParticipantGroup;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use App\Domain\ParticipantGroup\Model\ParticipantId;

interface ParticipantGroupRepositoryInterface
{
    public function nextId(): ParticipantGroupId;

    public function nextParticipantId(): ParticipantId;

    public function save(ParticipantGroup $group): void;

    public function findById(ParticipantGroupId $id): ?ParticipantGroup;

    /**
     * @return ParticipantGroup[]
     */
    public function getAll(): array;

    /**
     * @throws ParticipantGroupNotFoundException
     */
    public function getById(ParticipantGroupId $id): ParticipantGroup;
}

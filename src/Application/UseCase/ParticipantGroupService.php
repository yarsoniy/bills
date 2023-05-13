<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\ParticipantGroup\Model\Participant;
use App\Domain\ParticipantGroup\Model\ParticipantGroup;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use App\Domain\ParticipantGroup\Model\ParticipantId;
use App\Domain\ParticipantGroup\Service\ParticipantGroupRepositoryInterface;

class ParticipantGroupService
{
    public function __construct(
        readonly private ParticipantGroupRepositoryInterface $repository
    ) {
    }

    public function createGroup(string $title): ParticipantGroupId
    {
        $group = new ParticipantGroup($this->repository->nextId());
        $group->setTitle($title);
        $this->repository->save($group);

        return $group->getId();
    }

    public function getGroup(ParticipantGroupId $id)
    {
        return $this->repository->getById($id);
    }

    public function addParticipant(ParticipantGroupId $groupId, string $name): ParticipantId
    {
        $group = $this->repository->getById($groupId);
        $participantId = $this->repository->nextParticipantId();
        $group->addParticipant(new Participant($participantId));
        $group->setParticipantName($participantId, $name);

        $this->repository->save($group);

        return $participantId;
    }
}

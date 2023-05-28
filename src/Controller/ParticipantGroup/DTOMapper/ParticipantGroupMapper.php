<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup\DTOMapper;

use App\Controller\ParticipantGroup\DTO\ParticipantGroupDTO;
use App\Domain\ParticipantGroup\Model\ParticipantGroup;

class ParticipantGroupMapper
{
    public function __construct(
        readonly private ParticipantMapper $participantMapper
    ) {
    }

    public function toDTO(ParticipantGroup $group): ParticipantGroupDTO
    {
        return new ParticipantGroupDTO(
            $group->getId()->id,
            $group->getTitle(),
            $group->getCreatedAt(),
            $this->participantMapper->mapMany($group->getParticipantsView())
        );
    }

    /**
     * @param ParticipantGroup[] $participants
     *
     * @return ParticipantGroupDTO[]
     */
    public function manyToDTO(array $participants): array
    {
        $result = array_map(fn (ParticipantGroup $p) => $this->toDTO($p), $participants);

        return array_values($result);
    }
}

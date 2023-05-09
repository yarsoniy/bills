<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup\ResponseMapper;

use App\Controller\ParticipantGroup\Resource\ParticipantGroupResource;
use App\Domain\ParticipantGroup\Model\ParticipantGroup;

class ParticipantGroupResponseMapper
{
    public function __construct(
        readonly private ParticipantResponseMapper $participantMapper
    ) {
    }

    public function map(ParticipantGroup $group): ParticipantGroupResource
    {
        return new ParticipantGroupResource(
            $group->getId()->id,
            $group->getTitle(),
            $group->getCreatedAt(),
            $this->participantMapper->mapMany($group->getParticipantsView())
        );
    }

    /**
     * @param ParticipantGroup[] $participants
     *
     * @return ParticipantGroupResource[]
     */
    public function mapMany(array $participants): array
    {
        $result = array_map(fn (ParticipantGroup $p) => $this->map($p), $participants);

        return array_values($result);
    }
}

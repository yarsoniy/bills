<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup\ResponseMapper;

use App\Controller\ParticipantGroup\Resource\ParticipantResource;
use App\Domain\ParticipantGroup\View\ParticipantView;

class ParticipantResponseMapper
{
    public function map(ParticipantView $participant): ParticipantResource
    {
        return new ParticipantResource(
            $participant->getId()->id,
            $participant->getName(),
        );
    }

    /**
     * @param ParticipantView[] $participants
     *
     * @return ParticipantResource[]
     */
    public function mapMany(array $participants): array
    {
        $result = array_map(fn (ParticipantView $p) => $this->map($p), $participants);

        return array_values($result);
    }
}

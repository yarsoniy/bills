<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup\DTOMapper;

use App\Controller\ParticipantGroup\DTO\ParticipantDTO;
use App\Domain\ParticipantGroup\View\ParticipantView;

class ParticipantMapper
{
    public function map(ParticipantView $participant): ParticipantDTO
    {
        return new ParticipantDTO(
            $participant->getId()->id,
            $participant->getName(),
        );
    }

    /**
     * @param ParticipantView[] $participants
     *
     * @return ParticipantDTO[]
     */
    public function mapMany(array $participants): array
    {
        $result = array_map(fn (ParticipantView $p) => $this->map($p), $participants);

        return array_values($result);
    }
}

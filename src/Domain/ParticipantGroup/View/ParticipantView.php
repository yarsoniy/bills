<?php

declare(strict_types=1);

namespace App\Domain\ParticipantGroup\View;

use App\Domain\ParticipantGroup\Model\Participant;
use App\Domain\ParticipantGroup\Model\ParticipantId;

class ParticipantView
{
    private Participant $participant;

    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
    }

    public function getId(): ParticipantId
    {
        return $this->participant->getId();
    }

    public function getName(): string
    {
        return $this->participant->getName();
    }
}

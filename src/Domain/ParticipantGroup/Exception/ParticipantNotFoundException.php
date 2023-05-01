<?php

declare(strict_types=1);

namespace App\Domain\ParticipantGroup\Exception;

use App\Domain\ParticipantGroup\Model\ParticipantId;

class ParticipantNotFoundException extends \DomainException
{
    public static function withId(ParticipantId $id): self
    {
        return new self("Participant not found with id: '{$id->id}'");
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\ParticipantGroup\Exception;

use App\Domain\ParticipantGroup\Model\ParticipantGroupId;

class ParticipantGroupNotFoundException extends \DomainException
{
    public static function withId(ParticipantGroupId $id): self
    {
        return new self('Participant Group not found with id: '.$id->id);
    }
}

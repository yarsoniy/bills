<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\ParticipantGroup\Model\ParticipantId;

class ParticipantIdType extends BaseGuidType
{
    public function getName(): string
    {
        return 'ParticipantId';
    }

    protected function getClass(): string
    {
        return ParticipantId::class;
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\ParticipantGroup\Model\ParticipantGroupId;

class ParticipantGroupIdType extends BaseGuidType
{
    public function getName(): string
    {
        return 'ParticipantGroupId';
    }

    protected function getClass(): string
    {
        return ParticipantGroupId::class;
    }
}

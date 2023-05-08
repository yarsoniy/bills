<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class ParticipantGroupIdType extends GuidType
{
    public function getName(): string
    {
        return 'ParticipantGroupId';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ParticipantGroupId
    {
        return null === $value
            ? null
            : new ParticipantGroupId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof ParticipantGroupId
            ? $value->id
            : (string) $value;
    }
}

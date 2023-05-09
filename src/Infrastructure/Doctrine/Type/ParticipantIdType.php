<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\ParticipantGroup\Model\ParticipantId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class ParticipantIdType extends GuidType
{
    public function getName(): string
    {
        return 'ParticipantId';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ParticipantId
    {
        return null === $value
            ? null
            : new ParticipantId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof ParticipantId
            ? $value->id
            : (string) $value;
    }
}

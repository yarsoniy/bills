<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

abstract class BaseGuidType extends GuidType
{
    abstract protected function getClass(): string;

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $class = $this->getClass();

        return null === $value
            ? null
            : new $class($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return \is_object($value) && $value::class == $this->getClass()
            ? $value->id
            : (string) $value;
    }
}

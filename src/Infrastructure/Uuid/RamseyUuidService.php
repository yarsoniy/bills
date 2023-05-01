<?php

declare(strict_types=1);

namespace App\Infrastructure\Uuid;

use Ramsey\Uuid\Uuid;

class RamseyUuidService implements UuidServiceInterface
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}

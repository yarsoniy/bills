<?php

declare(strict_types=1);

namespace App\Infrastructure\Uuid;

interface UuidServiceInterface
{
    public function generate(): string;
}

<?php

declare(strict_types=1);

namespace App\Domain\Shared\Model;

abstract readonly class StringId
{
    public function __construct(
        public string $id
    ) {
    }

    public function __toString(): string
    {
        return $this->id;
    }
}

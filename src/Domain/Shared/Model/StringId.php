<?php

declare(strict_types=1);

namespace App\Domain\Shared\Model;

abstract readonly class StringId
{
    /**
     * @param string[] $ids
     *
     * @return static[]
     */
    public static function fromArray(array $ids): array
    {
        return array_map(fn ($id) => new static($id), $ids);
    }

    /**
     * @param static[] $ids
     *
     * @return string[]
     */
    public static function toArray(array $ids): array
    {
        return array_map(fn ($id) => $id->id, $ids);
    }

    public function __construct(
        public string $id
    ) {
    }

    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}

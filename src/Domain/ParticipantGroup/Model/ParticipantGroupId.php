<?php

declare(strict_types=1);

namespace App\Domain\ParticipantGroup\Model;

readonly class ParticipantGroupId
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
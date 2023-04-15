<?php

declare(strict_types=1);

namespace App\Domain\Participant;

readonly class ParticipantId
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}

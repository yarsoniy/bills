<?php

declare(strict_types=1);

namespace App\Domain\ParticipantGroup\Model;

class Participant
{
    private ParticipantId $id;

    private string $name;

    private \DateTimeImmutable $createdAt;

    public function __construct(ParticipantId $id)
    {
        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ParticipantId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}

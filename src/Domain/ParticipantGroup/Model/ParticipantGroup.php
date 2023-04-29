<?php

declare(strict_types=1);

namespace App\Domain\ParticipantGroup\Model;

use App\Domain\ParticipantGroup\View\ParticipantView;

class ParticipantGroup
{
    private ParticipantGroupId $id;

    private string $title;

    private \DateTimeImmutable $createdAt;

    /**
     * @var Participant[]
     */
    private array $participants;

    public function getId(): ParticipantGroupId
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function addParticipant(Participant $participant): void
    {
        $this->participants[$participant->getId()->id] = $participant;
    }

    /**
     * @return ParticipantView[]
     */
    public function getParticipants(): array
    {
        return array_map(fn (Participant $p) => new ParticipantView($p), $this->participants);
    }
}

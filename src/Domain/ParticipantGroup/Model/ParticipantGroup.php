<?php

declare(strict_types=1);

namespace App\Domain\ParticipantGroup\Model;

use App\Domain\ParticipantGroup\Exception\ParticipantNotFoundException;
use App\Domain\ParticipantGroup\View\ParticipantView;

class ParticipantGroup
{
    public function __construct(
        private ParticipantGroupId $id,

        private string $title = 'No title',

        private \DateTimeImmutable $createdAt = new \DateTimeImmutable(),

        /** @var Participant[] $participants */
        private array $participants = []
    ) {
    }

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

    private function getParticipant(ParticipantId $id): Participant
    {
        $participant = $this->participants[$id->id] ?? null;
        if (!$participant) {
            throw ParticipantNotFoundException::withId($id);
        }

        return $participant;
    }

    public function setParticipantName(ParticipantId $id, string $name): void
    {
        $participant = $this->getParticipant($id);
        $participant->setName($name);
    }

    /**
     * @return ParticipantId[]
     */
    public function getParticipantIds(): array
    {
        return array_map(fn (Participant $p) => $p->getId(), $this->participants);
    }

    /**
     * @return ParticipantView[]
     */
    public function getParticipantsView(): array
    {
        return array_map(fn (Participant $p) => new ParticipantView($p), $this->participants);
    }
}

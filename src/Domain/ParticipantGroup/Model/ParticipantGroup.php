<?php

declare(strict_types=1);

namespace App\Domain\ParticipantGroup\Model;

use App\Domain\ParticipantGroup\Exception\ParticipantNotFoundException;
use App\Domain\ParticipantGroup\View\ParticipantView;

class ParticipantGroup
{
    private ParticipantGroupId $id;

    private string $title;

    private \DateTimeImmutable $createdAt;

    private array $participants = [];

    public function __construct(ParticipantGroupId $id)
    {
        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
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
        $thisGroupId = $this->getId();
        $participantGroupId = $participant->getGroup()->getId();
        if (!$thisGroupId->equals($participantGroupId)) {
            $msg = "Can't add Participant to Group. Ids don't match: '$thisGroupId' '$participantGroupId'";
            throw new \LogicException($msg);
        }
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
     * @return ParticipantView[]
     */
    public function getParticipantsView(): array
    {
        return array_map(fn (Participant $p) => new ParticipantView($p), $this->participants);
    }
}

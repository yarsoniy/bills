<?php

declare(strict_types=1);

namespace App\Domain\ParticipantGroup\Model;

use App\Domain\ParticipantGroup\Exception\ParticipantNotFoundException;
use App\Domain\ParticipantGroup\View\ParticipantView;
use App\Infrastructure\Doctrine\Repository\DoctrineParticipantGroupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineParticipantGroupRepository::class)]
#[ORM\Table(name: 'participant_groups')]
class ParticipantGroup
{
    #[ORM\Id]
    #[ORM\Column(type: 'ParticipantGroupId')]
    private ParticipantGroupId $id;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(name: 'created_at')]
    private \DateTimeImmutable $createdAt;

    /**
     * @var Participant[]
     */
    private array $participants;

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

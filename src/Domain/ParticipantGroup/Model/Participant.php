<?php

declare(strict_types=1);

namespace App\Domain\ParticipantGroup\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'participants')]
class Participant
{
    #[ORM\Id]
    #[ORM\Column(type: 'ParticipantId')]
    private ParticipantId $id;

    #[ORM\ManyToOne(targetEntity: ParticipantGroup::class, inversedBy: 'participants')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id')]
    private ParticipantGroup $group;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(ParticipantId $id, ParticipantGroup $group)
    {
        $this->id = $id;
        $this->group = $group;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ParticipantId
    {
        return $this->id;
    }

    public function getGroup(): ParticipantGroup
    {
        return $this->group;
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

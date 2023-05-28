<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ParticipantGroupDTO
{
    public function __construct(
        private ?string $id,

        #[Assert\NotBlank]
        private ?string $title,

        private ?\DateTimeImmutable $createdAt,

        /** @var ParticipantDTO[] */
        private ?array $participants
    ) {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return ParticipantDTO[]
     */
    public function getParticipants(): array
    {
        return $this->participants;
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup\Resource;

use Symfony\Component\Validator\Constraints as Assert;

class ParticipantGroupResource
{
    public function __construct(
        private ?string $id,

        #[Assert\NotBlank]
        private ?string $title,

        private ?\DateTimeImmutable $createdAt,

        /** @var ParticipantResource[] */
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
     * @return ParticipantResource[]
     */
    public function getParticipants(): array
    {
        return $this->participants;
    }
}

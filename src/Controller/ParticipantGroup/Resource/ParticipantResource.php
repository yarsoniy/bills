<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup\Resource;

use Symfony\Component\Validator\Constraints as Assert;

class ParticipantResource
{
    public function __construct(
        private ?string $id,

        #[Assert\NotBlank]
        private ?string $name
    ) {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}

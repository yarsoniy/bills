<?php

declare(strict_types=1);

namespace App\Controller\Bill\Resource;

use Symfony\Component\Validator\Constraints as Assert;

class BillResource
{
    public function __construct(
        readonly private ?string $id,

        #[Assert\NotBlank]
        readonly private ?string $title,

        readonly private ?\DateTimeImmutable $createdAt
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
}

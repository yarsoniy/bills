<?php

declare(strict_types=1);

namespace App\Controller\Bill\DTO;

use App\Controller\BillItem\DTO\BillItemDTO;
use Symfony\Component\Validator\Constraints as Assert;

class BillDTO
{
    public function __construct(
        readonly public ?string $id,

        #[Assert\NotBlank]
        readonly public ?string $title,

        readonly public ?\DateTimeImmutable $createdAt,

        /** @var BillItemDTO[] */
        readonly public ?array $items,

        readonly public ?float $totalCost
    ) {
    }
}

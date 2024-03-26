<?php

declare(strict_types=1);

namespace App\Controller\BillItem\DTO;

use App\Controller\Money\DTO\MoneyBreakdownDTO;
use Symfony\Component\Validator\Constraints as Assert;

class BillItemDTO
{
    public function __construct(
        readonly public ?string $id,

        #[Assert\NotBlank]
        readonly public ?string $title,

        readonly public ?\DateTimeImmutable $createdAt,

        #[Assert\NotBlank]
        #[Assert\PositiveOrZero]
        readonly public ?float $cost,

        readonly public ?MoneyBreakdownDTO $costBreakdown,

        readonly public ?SplitAgreementDTO $agreement,
    ) {
    }
}

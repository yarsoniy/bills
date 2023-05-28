<?php

declare(strict_types=1);

namespace App\Controller\BillItem\DTO;

class PaymentDTO
{
    public function __construct(
        readonly public ?string $itemPayer,
        readonly public ?string $itemUser,
    ) {
    }
}

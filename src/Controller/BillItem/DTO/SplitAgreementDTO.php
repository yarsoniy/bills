<?php

declare(strict_types=1);

namespace App\Controller\BillItem\DTO;

class SplitAgreementDTO
{
    public function __construct(
        /** @var SplitRuleDTO[]|null */
        readonly public ?array $rules,
    ) {
    }
}

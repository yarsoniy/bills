<?php

declare(strict_types=1);

namespace App\Controller\BillItem\DTO;

class SplitRuleDTO
{
    public function __construct(
        /** @var string[]|null */
        readonly public ?array $itemPayers,

        /** @var string[]|null */
        readonly public ?array $itemUsers,
    ) {
    }
}

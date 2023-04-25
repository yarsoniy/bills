<?php

declare(strict_types=1);

namespace App\Domain\AccountingBook;

class AccountingOperation
{
    private string $title;

    private \DateTimeImmutable $createdAt;

    /** @var AccountingRecord[] */
    private array $records;
}

<?php

declare(strict_types=1);

namespace App\Domain\AccountingBook;

use App\Domain\Money\MoneyBreakdown;

class AccountingBook
{
    private AccountingBookId $id;

    private string $title;

    /**
     * @var AccountingOperation[]
     */
    private array $operations;

    public function addOperation(AccountingOperation $operation): void
    {
    }

    public function calculateBalance(): MoneyBreakdown
    {
    }

    /**
     * @return AccountingRecord[]
     */
    public function suggestSettleUp(): array
    {
    }
}

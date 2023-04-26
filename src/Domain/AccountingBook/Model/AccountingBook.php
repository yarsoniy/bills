<?php

declare(strict_types=1);

namespace App\Domain\AccountingBook\Model;

use App\Domain\DebtResolver\Service\DebtResolver;
use App\Domain\Money\Model\MoneyBreakdown;

class AccountingBook
{
    private AccountingBookId $id;

    private string $title;

    /**
     * @var Record[]
     */
    private array $records;

    public function addRecord(Record $record): void
    {
        $this->records[] = $record;
    }

    public function calculateBalance(): MoneyBreakdown
    {
        $balance = new MoneyBreakdown();
        foreach ($this->records as $r) {
            $balance = $balance->merge($r->calculateBalance());
        }

        return $balance;
    }

    /**
     * @return Operation[]
     */
    public function suggestSettleUp(DebtResolver $resolver): array
    {
        return $resolver->resolve($this->calculateBalance());
    }
}

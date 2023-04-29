<?php

declare(strict_types=1);

namespace App\Domain\AccountingBook\Model;

use App\Domain\Money\Model\MoneyBreakdown;

class Record
{
    private RecordType $type;

    private string $title;

    private \DateTimeImmutable $createdAt;

    /** @var Transaction[] */
    private array $transactions;

    public function __construct(RecordType $type, string $title, \DateTimeImmutable $createdAt, array $operations)
    {
        $this->type = $type;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->transactions = $operations;
    }

    public function calculateBalance(): MoneyBreakdown
    {
        $balance = new MoneyBreakdown();
        foreach ($this->transactions as $tx) {
            if (\in_array($this->type, [RecordType::LEND, RecordType::PAY_BACK], true)) {
                $balance = $balance
                    ->add($tx->a->id, $tx->amount)
                    ->add($tx->b->id, $tx->amount->negative());
                continue;
            }
            if (\in_array($this->type, [RecordType::DEBT_CANCELLATION], true)) {
                $balance = $balance
                    ->add($tx->a->id, $tx->amount->negative())
                    ->add($tx->b->id, $tx->amount);
                continue;
            }

            throw new \Error('Unsupported operation type '.$this->type);
        }

        return $balance;
    }
}

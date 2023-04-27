<?php

declare(strict_types=1);

namespace App\Domain\AccountingBook\Model;

use App\Domain\Money\Model\MoneyBreakdown;

class Record
{
    private string $title;

    private \DateTimeImmutable $createdAt;

    /** @var Operation[] */
    private array $operations;

    public function __construct(string $title, \DateTimeImmutable $createdAt, array $operations)
    {
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->operations = $operations;
    }

    public function calculateBalance(): MoneyBreakdown
    {
        $balance = new MoneyBreakdown();
        foreach ($this->operations as $o) {
            if (\in_array($o->type, [OperationType::LEND, OperationType::PAY_BACK], true)) {
                $balance = $balance
                    ->add($o->a->id, $o->amount)
                    ->add($o->b->id, $o->amount->negative());
                continue;
            }
            if (\in_array($o->type, [OperationType::DEBT_CANCELLATION], true)) {
                $balance = $balance
                    ->add($o->a->id, $o->amount->negative())
                    ->add($o->b->id, $o->amount);
                continue;
            }

            throw new \Error('Unknown operation type '.$o->type);
        }

        return $balance;
    }
}

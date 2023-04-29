<?php

declare(strict_types=1);

namespace App\Domain\AccountingBook\Model;

use App\Domain\Money\Model\Money;
use App\Domain\Participant\ParticipantId;

readonly class Transaction
{
    public ParticipantId $a;

    public ParticipantId $b;

    public Money $amount;

    public function __construct(ParticipantId $a, ParticipantId $b, Money $amount)
    {
        $this->a = $a;
        $this->b = $b;
        $this->amount = $amount;
    }
}

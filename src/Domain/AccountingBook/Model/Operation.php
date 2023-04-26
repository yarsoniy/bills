<?php

declare(strict_types=1);

namespace App\Domain\AccountingBook\Model;

use App\Domain\Money\Model\Money;
use App\Domain\Participant\ParticipantId;

readonly class Operation
{
    public OperationType $type;

    public ParticipantId $a;

    public ParticipantId $b;

    public Money $amount;

    public function __construct(OperationType $type, ParticipantId $a, ParticipantId $b, Money $amount)
    {
        $this->type = $type;
        $this->a = $a;
        $this->b = $b;
        $this->amount = $amount;
    }
}

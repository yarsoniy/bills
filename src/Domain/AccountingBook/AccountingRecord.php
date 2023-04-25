<?php

declare(strict_types=1);

namespace App\Domain\AccountingBook;

use App\Domain\Money\Money;
use App\Domain\Participant\ParticipantId;

readonly class AccountingRecord
{
    public AccountingRecordType $type;

    public ParticipantId $a;

    public ParticipantId $b;

    public Money $amount;
}

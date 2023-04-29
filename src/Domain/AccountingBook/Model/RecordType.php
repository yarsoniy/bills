<?php

declare(strict_types=1);

namespace App\Domain\AccountingBook\Model;

enum RecordType: string
{
    case LEND = 'lend';
    case PAY_BACK = 'pay_back';
    case DEBT_CANCELLATION = 'debt_cancellation';
}

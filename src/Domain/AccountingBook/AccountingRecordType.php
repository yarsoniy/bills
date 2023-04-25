<?php

declare(strict_types=1);

namespace App\Domain\AccountingBook;

enum AccountingRecordType: string
{
    case BORROW = 'borrow';
    case PAY_BACK = 'pay_back';
}

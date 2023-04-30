<?php

declare(strict_types=1);

namespace App\Tests\Helper\FixtureFactory;

use App\Domain\AccountingBook\Model\AccountingBook;
use App\Domain\AccountingBook\Model\Record;
use App\Domain\AccountingBook\Model\RecordType;
use App\Domain\AccountingBook\Model\Transaction;
use App\Domain\Money\Model\Money;
use App\Domain\ParticipantGroup\Model\ParticipantId;

class AccountingBookFixtureFactory
{
    public function createAccountingBook(): AccountingBook
    {
        return new AccountingBook();
    }

    public function createRecord(array $params): Record
    {
        $type = $params['type'];
        $title = $params['title'] ?? '';
        $createdAt = $params['createdAt'] ?? new \DateTimeImmutable();
        $transactions = $params['transactions'] ?? [];

        return new Record($type, $title, $createdAt, $transactions);
    }

    public function createRecordLend(array $params): Record
    {
        $params['type'] = RecordType::LEND;

        return $this->createRecord($params);
    }

    public function createRecordPayback(array $params): Record
    {
        $params['type'] = RecordType::PAY_BACK;

        return $this->createRecord($params);
    }

    public function createRecordDebtCancellation(array $params): Record
    {
        $params['type'] = RecordType::DEBT_CANCELLATION;

        return $this->createRecord($params);
    }

    public function createTransaction(string $a, string $b, float $amount): Transaction
    {
        return new Transaction(new ParticipantId($a), new ParticipantId($b), new Money($amount));
    }
}

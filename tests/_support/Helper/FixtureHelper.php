<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Domain\AccountingBook\Model\AccountingBook;
use App\Domain\AccountingBook\Model\Record;
use App\Domain\AccountingBook\Model\Transaction;
use App\Domain\Bill\Model\Bill;
use App\Domain\Bill\Model\BillItem;
use App\Tests\Helper\FixtureFactory\AccountingBookFixtureFactory;
use App\Tests\Helper\FixtureFactory\BillFixtureFactory;
use Codeception\Module;

class FixtureHelper extends Module
{
    public function createAccountingBook(): AccountingBook
    {
        return (new AccountingBookFixtureFactory())->createAccountingBook();
    }

    public function createRecord(array $params): Record
    {
        return (new AccountingBookFixtureFactory())->createRecord($params);
    }

    public function createRecordLend(array $params): Record
    {
        return (new AccountingBookFixtureFactory())->createRecordLend($params);
    }

    public function createRecordPayback(array $params): Record
    {
        return (new AccountingBookFixtureFactory())->createRecordPayback($params);
    }

    public function createRecordDebtCancellation(array $params): Record
    {
        return (new AccountingBookFixtureFactory())->createRecordDebtCancellation($params);
    }

    public function createTransaction(string $a, string $b, float $amount): Transaction
    {
        return (new AccountingBookFixtureFactory())->createTransaction($a, $b, $amount);
    }

    public function createBill(): Bill
    {
        return (new BillFixtureFactory())->createBill();
    }

    public function createBillItem(array $params): BillItem
    {
        return (new BillFixtureFactory())->createBillItem($params);
    }
}

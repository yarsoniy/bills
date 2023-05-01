<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\AccountingBook\Model;

use App\Tests\UnitTester;
use Codeception\Test\Unit;

class RecordTest extends Unit
{
    protected UnitTester $tester;

    public function testCalculateBalanceLend()
    {
        $record = $this->tester->createRecordLend(['transactions' => [
            $this->tester->createTransaction('pA', 'pB', 200),
            $this->tester->createTransaction('pA', 'pC', 100),
            $this->tester->createTransaction('pA', 'pD', 50),
        ]]);

        $expected = $this->tester->createMoneyBreakdown([
            'pA' => 350,
            'pB' => -200,
            'pC' => -100,
            'pD' => -50,
        ]);

        $actual = $record->calculateBalance();
        $this->assertEquals($expected, $actual);
    }

    public function testCalculateBalancePayBack()
    {
        $record = $this->tester->createRecordPayback(['transactions' => [
            $this->tester->createTransaction('pB', 'pA', 200),
            $this->tester->createTransaction('pC', 'pA', 100),
            $this->tester->createTransaction('pD', 'pA', 50),
        ]]);

        $expected = $this->tester->createMoneyBreakdown([
            'pA' => -350,
            'pB' => 200,
            'pC' => 100,
            'pD' => 50,
        ]);

        $actual = $record->calculateBalance();
        $this->assertEquals($expected, $actual);
    }

    public function testCalculateBalanceDebtCancellation()
    {
        $record = $this->tester->createRecordDebtCancellation(['transactions' => [
            $this->tester->createTransaction('pA', 'pB', 200),
            $this->tester->createTransaction('pA', 'pC', 100),
            $this->tester->createTransaction('pA', 'pD', 50),
        ]]);

        $expected = $this->tester->createMoneyBreakdown([
            'pA' => -350,
            'pB' => 200,
            'pC' => 100,
            'pD' => 50,
        ]);

        $actual = $record->calculateBalance();
        $this->assertEquals($expected, $actual);
    }
}

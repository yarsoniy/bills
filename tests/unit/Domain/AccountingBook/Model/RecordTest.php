<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\AccountingBook\Model;

use App\Domain\AccountingBook\Model\Record;
use App\Domain\AccountingBook\Model\RecordType;
use App\Domain\AccountingBook\Model\Transaction;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Domain\ParticipantGroup\Model\ParticipantId;
use Codeception\Test\Unit;

class RecordTest extends Unit
{
    public function testCalculateBalanceLend()
    {
        $record = new Record(RecordType::LEND, 'Lend for party', new \DateTimeImmutable(), [
            new Transaction(new ParticipantId('pA'), new ParticipantId('pB'), new Money(200)),
            new Transaction(new ParticipantId('pA'), new ParticipantId('pC'), new Money(100)),
            new Transaction(new ParticipantId('pA'), new ParticipantId('pD'), new Money(50)),
        ]);

        $expected = new MoneyBreakdown(
            [
                'pA' => new Money(350),
                'pB' => new Money(-200),
                'pC' => new Money(-100),
                'pD' => new Money(-50),
            ]
        );

        $actual = $record->calculateBalance();
        $this->assertEquals($expected, $actual);
    }

    public function testCalculateBalancePayBack()
    {
        $record = new Record(RecordType::PAY_BACK, 'Pay back for party', new \DateTimeImmutable(), [
            new Transaction(new ParticipantId('pB'), new ParticipantId('pA'), new Money(200)),
            new Transaction(new ParticipantId('pC'), new ParticipantId('pA'), new Money(100)),
            new Transaction(new ParticipantId('pD'), new ParticipantId('pA'), new Money(50)),
        ]);

        $expected = new MoneyBreakdown(
            [
                'pA' => new Money(-350),
                'pB' => new Money(200),
                'pC' => new Money(100),
                'pD' => new Money(50),
            ]
        );

        $actual = $record->calculateBalance();
        $this->assertEquals($expected, $actual);
    }

    public function testCalculateBalanceDebtCancellation()
    {
        $record = new Record(RecordType::DEBT_CANCELLATION, 'Lend for party', new \DateTimeImmutable(), [
            new Transaction(new ParticipantId('pA'), new ParticipantId('pB'), new Money(200)),
            new Transaction(new ParticipantId('pA'), new ParticipantId('pC'), new Money(100)),
            new Transaction(new ParticipantId('pA'), new ParticipantId('pD'), new Money(50)),
        ]);

        $expected = new MoneyBreakdown(
            [
                'pA' => new Money(-350),
                'pB' => new Money(200),
                'pC' => new Money(100),
                'pD' => new Money(50),
            ]
        );

        $actual = $record->calculateBalance();
        $this->assertEquals($expected, $actual);
    }
}

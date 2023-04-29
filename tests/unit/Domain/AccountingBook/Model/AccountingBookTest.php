<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\AccountingBook\Model;

use App\Domain\AccountingBook\Model\AccountingBook;
use App\Domain\AccountingBook\Model\Record;
use App\Domain\AccountingBook\Model\RecordType;
use App\Domain\AccountingBook\Model\Transaction;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Domain\ParticipantGroup\Model\ParticipantId;
use Codeception\Test\Unit;

class AccountingBookTest extends Unit
{
    /**
     * @dataProvider providerCalculateBalance
     *
     * @param Record[] $records
     *
     * @return void
     */
    public function testCalculateBalance(array $records, MoneyBreakdown $expected)
    {
        $book = new AccountingBook();
        foreach ($records as $record) {
            $book->addRecord($record);
        }

        $this->assertEquals($expected, $book->calculateBalance());
    }

    public function providerCalculateBalance(): array
    {
        return [
            [
                'records' => [
                    new Record(RecordType::LEND, 'Lend for party', new \DateTimeImmutable(), [
                        new Transaction(new ParticipantId('pA'), new ParticipantId('pB'), new Money(200)),
                        new Transaction(new ParticipantId('pA'), new ParticipantId('pC'), new Money(100)),
                        new Transaction(new ParticipantId('pA'), new ParticipantId('pD'), new Money(50)),
                    ]),
                ],
                'expected' => new MoneyBreakdown(
                    [
                        'pA' => new Money(350),
                        'pB' => new Money(-200),
                        'pC' => new Money(-100),
                        'pD' => new Money(-50),
                    ]
                ),
            ],
            [
                'records' => [
                    new Record(RecordType::LEND, 'Lend for party', new \DateTimeImmutable(), [
                        new Transaction(new ParticipantId('pA'), new ParticipantId('pB'), new Money(200)),
                        new Transaction(new ParticipantId('pA'), new ParticipantId('pC'), new Money(100)),
                        new Transaction(new ParticipantId('pA'), new ParticipantId('pD'), new Money(50)),
                    ]),
                    new Record(RecordType::PAY_BACK, 'Pay back for party', new \DateTimeImmutable(), [
                        new Transaction(new ParticipantId('pB'), new ParticipantId('pA'), new Money(120)),
                        new Transaction(new ParticipantId('pC'), new ParticipantId('pA'), new Money(70)),
                        new Transaction(new ParticipantId('pD'), new ParticipantId('pA'), new Money(30)),
                    ]),
                ],
                'expected' => new MoneyBreakdown(
                    [
                        'pA' => new Money(130),
                        'pB' => new Money(-80),
                        'pC' => new Money(-30),
                        'pD' => new Money(-20),
                    ]
                ),
            ],
            [
                'records' => [
                    new Record(RecordType::LEND, 'Lend for party', new \DateTimeImmutable(), [
                        new Transaction(new ParticipantId('pA'), new ParticipantId('pB'), new Money(200)),
                        new Transaction(new ParticipantId('pA'), new ParticipantId('pC'), new Money(100)),
                        new Transaction(new ParticipantId('pA'), new ParticipantId('pD'), new Money(50)),
                    ]),
                    new Record(RecordType::PAY_BACK, 'Pay back for party', new \DateTimeImmutable(), [
                        new Transaction(new ParticipantId('pB'), new ParticipantId('pA'), new Money(120)),
                        new Transaction(new ParticipantId('pC'), new ParticipantId('pA'), new Money(70)),
                        new Transaction(new ParticipantId('pD'), new ParticipantId('pA'), new Money(30)),
                    ]),
                    new Record(RecordType::DEBT_CANCELLATION, 'pA cancels all debts of other participants', new \DateTimeImmutable(), [
                        new Transaction(new ParticipantId('pA'), new ParticipantId('pB'), new Money(80)),
                        new Transaction(new ParticipantId('pA'), new ParticipantId('pC'), new Money(30)),
                        new Transaction(new ParticipantId('pA'), new ParticipantId('pD'), new Money(20)),
                    ]),
                ],
                'expected' => new MoneyBreakdown(
                    [
                        'pA' => new Money(0),
                        'pB' => new Money(0),
                        'pC' => new Money(0),
                        'pD' => new Money(0),
                    ]
                ),
            ],
        ];
    }
}

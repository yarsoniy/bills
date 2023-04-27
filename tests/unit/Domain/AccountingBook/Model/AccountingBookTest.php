<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\AccountingBook\Model;

use App\Domain\AccountingBook\Model\AccountingBook;
use App\Domain\AccountingBook\Model\Operation;
use App\Domain\AccountingBook\Model\OperationType;
use App\Domain\AccountingBook\Model\Record;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Domain\Participant\ParticipantId;
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
                    new Record('Lend for party', new \DateTimeImmutable(), [
                        new Operation(OperationType::LEND, new ParticipantId('pA'), new ParticipantId('pB'), new Money(200)),
                        new Operation(OperationType::LEND, new ParticipantId('pA'), new ParticipantId('pC'), new Money(100)),
                        new Operation(OperationType::LEND, new ParticipantId('pA'), new ParticipantId('pD'), new Money(50)),
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
                    new Record('Lend for party', new \DateTimeImmutable(), [
                        new Operation(OperationType::LEND, new ParticipantId('pA'), new ParticipantId('pB'), new Money(200)),
                        new Operation(OperationType::LEND, new ParticipantId('pA'), new ParticipantId('pC'), new Money(100)),
                        new Operation(OperationType::LEND, new ParticipantId('pA'), new ParticipantId('pD'), new Money(50)),
                    ]),
                    new Record('Lend for party', new \DateTimeImmutable(), [
                        new Operation(OperationType::PAY_BACK, new ParticipantId('pB'), new ParticipantId('pA'), new Money(120)),
                        new Operation(OperationType::PAY_BACK, new ParticipantId('pC'), new ParticipantId('pA'), new Money(70)),
                        new Operation(OperationType::PAY_BACK, new ParticipantId('pD'), new ParticipantId('pA'), new Money(30)),
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
                    new Record('Lend for party', new \DateTimeImmutable(), [
                        new Operation(OperationType::LEND, new ParticipantId('pA'), new ParticipantId('pB'), new Money(200)),
                        new Operation(OperationType::LEND, new ParticipantId('pA'), new ParticipantId('pC'), new Money(100)),
                        new Operation(OperationType::LEND, new ParticipantId('pA'), new ParticipantId('pD'), new Money(50)),
                    ]),
                    new Record('Pay back for party', new \DateTimeImmutable(), [
                        new Operation(OperationType::PAY_BACK, new ParticipantId('pB'), new ParticipantId('pA'), new Money(120)),
                        new Operation(OperationType::PAY_BACK, new ParticipantId('pC'), new ParticipantId('pA'), new Money(70)),
                        new Operation(OperationType::PAY_BACK, new ParticipantId('pD'), new ParticipantId('pA'), new Money(30)),
                    ]),
                    new Record('pA cancels all debts of other participants', new \DateTimeImmutable(), [
                        new Operation(OperationType::DEBT_CANCELLATION, new ParticipantId('pA'), new ParticipantId('pB'), new Money(80)),
                        new Operation(OperationType::DEBT_CANCELLATION, new ParticipantId('pA'), new ParticipantId('pC'), new Money(30)),
                        new Operation(OperationType::DEBT_CANCELLATION, new ParticipantId('pA'), new ParticipantId('pD'), new Money(20)),
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

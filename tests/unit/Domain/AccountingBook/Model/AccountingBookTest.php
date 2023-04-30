<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\AccountingBook\Model;

use App\Domain\AccountingBook\Model\RecordType;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

class AccountingBookTest extends Unit
{
    protected UnitTester $tester;

    /**
     * @dataProvider providerCalculateBalance
     *
     * @param array[] $records
     *
     * @return void
     */
    public function testCalculateBalance(array $records, MoneyBreakdown $expected)
    {
        $book = $this->tester->createAccountingBook();
        foreach ($records as $recordData) {
            $type = $recordData['type'];
            $transactions = array_map(fn (array $item) => $this->tester->createTransaction(...$item), $recordData['transactions']);
            $record = $this->tester->createRecord(['type' => $type, 'transactions' => $transactions]);
            $book->addRecord($record);
        }

        $this->assertEquals($expected, $book->calculateBalance());
    }

    public function providerCalculateBalance(): array
    {
        return [
            [
                'records' => [
                    [
                        'type' => RecordType::LEND,
                        'transactions' => [
                            ['pA', 'pB', 200],
                            ['pA', 'pC', 100],
                            ['pA', 'pD', 50],
                        ],
                    ],
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
                    [
                        'type' => RecordType::LEND,
                        'transactions' => [
                            ['pA', 'pB', 200],
                            ['pA', 'pC', 100],
                            ['pA', 'pD', 50],
                        ],
                    ],
                    [
                        'type' => RecordType::PAY_BACK,
                        'transactions' => [
                            ['pB', 'pA', 120],
                            ['pC', 'pA', 70],
                            ['pD', 'pA', 30],
                        ],
                    ],
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
                    [
                        'type' => RecordType::LEND,
                        'transactions' => [
                            ['pA', 'pB', 200],
                            ['pA', 'pC', 100],
                            ['pA', 'pD', 50],
                        ],
                    ],
                    [
                        'type' => RecordType::PAY_BACK,
                        'transactions' => [
                            ['pB', 'pA', 120],
                            ['pC', 'pA', 70],
                            ['pD', 'pA', 30],
                        ],
                    ],
                    [
                        'type' => RecordType::DEBT_CANCELLATION,
                        'transactions' => [
                            ['pA', 'pB', 80],
                            ['pA', 'pC', 30],
                            ['pA', 'pD', 20],
                        ],
                    ],
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
